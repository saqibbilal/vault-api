<?php

namespace App\Observers;

use App\Models\Document;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class DocumentObserver
{
    /**
     * Handle the Document "saving" event.
     * This triggers on both 'create' and 'update'.
     */
    public function saving(Document $document): void
    {
        // 1. Only generate embeddings for 'note' types
        // 2. Only run if the content is not empty
        // 3. Only run if the content has actually changed (to save API credits)
        if ($document->type === 'note' && !empty($document->content) && $document->isDirty('content')) {
            try {
                $result = Gemini::embeddingModel('text-embedding-004')
                    ->embedContent($document->content);

                // We store the array of 768 floats as a JSON-compatible string
                // Postgres pgvector accepts the [0.1, 0.2, ...] format
                $document->embedding = $result->embedding->values;

            } catch (\Exception $e) {
                // We log the error so the save doesn't crash the whole app if Gemini is down
                Log::error("Gemini Embedding Failed: " . $e->getMessage());
            }
        }
    }
}
