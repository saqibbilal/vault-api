<?php

namespace App\Observers;

use App\Models\Document;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Enums\MimeType;
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

        // Case A: It's a note
        if ($document->type === 'note' && $document->isDirty('content')) {
            $textToEmbed = $document->content;
        }

        // Case B: It's a file upload
        elseif ($document->type !== 'note' && $document->isDirty('file_path')) {
            $extractedText = $this->extractTextFromFile($document);
            $document->content = $extractedText;

            // Combine Title + Content for the embedding logic
            $textToEmbed = "Title: " . $document->title . "\nContent: " . $extractedText;
        }

        // Handle Embedding (Existing logic)
        if (!empty($textToEmbed)) {
            try {
                $result = Gemini::embeddingModel('models/gemini-embedding-001')
                    ->embedContent($textToEmbed);

                $document->embedding = $result->embedding->values;
            } catch (\Exception $e) {
                Log::error("Gemini Embedding Failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Helper to extract text from PDF, DOCX, or Images (OCR)
     */
    private function extractTextFromFile(Document $document): string
    {
        try {
            $path = Storage::disk('public')->path($document->file_path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mimeType = mime_content_type($path);

            // 1. Handle Images (OCR via Gemini Vision)
            if (str_starts_with($mimeType, 'image/')) {
                return $this->performOcrWithGemini($path, $mimeType);
            }

            // 2. Handle PDF
            if ($extension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                return $parser->parseFile($path)->getText();
            }

            // 3. Handle Word
            if (in_array($extension, ['docx', 'doc'])) {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
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

    /**
     * Perform OCR using Gemini Multimodal
     */
    private function performOcrWithGemini(string $path, string $mimeType): string
    {
        try {
            $geminiMimeType = match ($mimeType) {
                'image/png', 'image/x-png' => MimeType::IMAGE_PNG,
                'image/jpeg', 'image/jpg'  => MimeType::IMAGE_JPEG,
                'image/webp'               => MimeType::IMAGE_WEBP,
                'image/heic', 'image/heif' => MimeType::IMAGE_HEIC,
                default                    => MimeType::from($mimeType),
            };

            // USE THE 2.5 FLASH MODEL FROM YOUR TINKER LIST
            $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->generateContent([
                    'Read and extract all text from this image accurately. Preserve the document structure and headers.',
                    new \Gemini\Data\Blob(
                        mimeType: $geminiMimeType,
                        data: base64_encode(file_get_contents($path))
                    )
                ]);

            $text = $result->text();
            Log::info("OCR Success (Gemini 2.5): " . substr($text, 0, 50) . "...");

            return $text;
        } catch (\Exception $e) {
            // If 'gemini-2.5-flash' fails, try 'models/gemini-2.5-flash'
            Log::error("Gemini OCR Failed: " . $e->getMessage());
            return '';
        }
    }

}
