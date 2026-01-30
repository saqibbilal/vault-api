<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DocumentResource;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Http\Requests\Api\V1\StoreDocumentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // For now, we show all. Later, the Policy will filter this.
        // Note the use of with('user') to avoid the N+1 problem!
        $documents = Document::with('user')->latest()->get();

        return DocumentResource::collection($documents);
    }

    public function store(StoreDocumentRequest $request): DocumentResource
    {
        $document = $request->user()->documents()->create($request->validated());

        return new DocumentResource($document);
    }

}
