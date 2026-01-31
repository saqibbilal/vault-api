<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DocumentResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Http\Requests\Api\V1\StoreDocumentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Contracts\FileStorageInterface;

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

}
