<?php

namespace App\Observers;

use App\Models\Document;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// Import the parsers
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordParser;

class DocumentObserver
{
    public function saving(Document $document): void
    {
        $textToEmbed = '';

        // Case A: It's a note and the content changed
        if ($document->type === 'note' && $document->isDirty('content')) {
            $textToEmbed = $document->content;
        }

        // Case B: It's a file and the file_path changed (new upload)
        elseif ($document->type !== 'note' && $document->isDirty('file_path')) {
            $textToEmbed = $this->extractTextFromFile($document);
        }

        // Trigger Gemini if we have text and it's not empty
        if (!empty($textToEmbed)) {
            try {
                $result = Gemini::embeddingModel('text-embedding-004')
                    ->embedContent($textToEmbed);

                $document->embedding = $result->embedding->values;
            } catch (\Exception $e) {
                Log::error("Gemini Embedding Failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Helper to extract text from PDF or DOCX
     */
    private function extractTextFromFile(Document $document): string
    {
        try {
            // Get the absolute path from the storage disk
            $path = Storage::disk('public')->path($document->file_path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $parser = new PdfParser();
                return $parser->parseFile($path)->getText();
            }

            if (in_array($extension, ['docx', 'doc'])) {
                $phpWord = WordParser::load($path);
                $fullText = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $fullText .= $element->getText() . " ";
                        }
                    }
                }
                return $fullText;
            }
        } catch (\Exception $e) {
            Log::error("Text Extraction Failed for {$document->file_path}: " . $e->getMessage());
        }

        return '';
    }
}
