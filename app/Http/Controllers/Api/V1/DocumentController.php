<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SearchRequest;
use App\Http\Resources\Api\V1\DocumentResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Http\Requests\Api\V1\StoreDocumentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Contracts\FileStorageInterface;
use Illuminate\Support\Facades\Storage;
use Gemini\Laravel\Facades\Gemini;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected FileStorageInterface $fileService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        // we don't even need a Policy—we use Eloquent Scopes or simple relationship filtering
        $documents = $request->user()->documents()->with('user')->latest()->get();

        return DocumentResource::collection($documents);
    }

    public function store(StoreDocumentRequest $request): DocumentResource
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_path'] = $this->fileService->store($request->file('file'));
            $data['mime_type'] = $request->file('file')->getClientMimeType();
            $data['size']      = $request->file('file')->getSize();
        }

        $document = $request->user()->documents()->create($data);

        return new DocumentResource($document);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Document $document): DocumentResource
    {
        // This looks for the 'view' method in DocumentPolicy
        $this->authorize('view', $document);

        return new DocumentResource($document);
    }


    /**
     * @throws AuthorizationException
     */
    public function update(StoreDocumentRequest $request, Document $document): DocumentResource
    {
        $this->authorize('update', $document);

        $document->update($request->validated());

        return new DocumentResource($document);
    }

    public function destroy(Document $document) : \Illuminate\Http\Response
    {
        // 1. Authorization (Spatie or Policy)
        // $this->authorize('delete', $document);

        // 2. Delete the physical file if it exists
        if ($document->type === 'file' && $document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        // 3. Delete the database record
        $document->delete();

        return response()->noContent();
    }

    public function search(SearchRequest $request)
    {
        $searchTerm = $request->get('query');

        try {
            // 1. Turn the user's search string into a vector "meaning"

            $response = Gemini::embeddingModel('models/gemini-embedding-001')
                ->embedContent($searchTerm);

            // 1. Convert the embedding array to a Postgres-compatible string
            $embeddingString = '[' . implode(',', $response->embedding->values) . ']';

            // 2. Query the DB using Cosine Distance (<=>)

            $documents = Document::query()
                ->select('*')
                // We calculate the distance once
                ->selectRaw('embedding <=> ? AS distance', [$embeddingString])
                // ONLY show results that are semantically close (lower distance = better match)
                // 0.6 is a good starting point. Adjust to 0.4 for stricter, 0.7 for looser.
                ->whereRaw('embedding <=> ? < 0.4', [$embeddingString])
                ->orderBy('distance')
                ->get();

            return DocumentResource::collection($documents);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
