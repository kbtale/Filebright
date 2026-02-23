<?php

namespace App\Jobs;

use App\Services\EmbeddingService;
use App\Services\VectorStorageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessDocument implements ShouldQueue
{
    use Queueable;

    private $document;

    public function __construct(DocumentMetadata $document)
    {
        $this->document = $document;
    }

    public function handle(
        DocumentParserService $parser,
        TextChunkerService $chunker,
        EmbeddingService $embeddingService,
        VectorStorageService $vectorStorage
    ): void {
        $this->document->update(['status' => 'processing']);

        try {
            $filePath = Storage::disk('private')->path($this->document->path);
            $text = $parser->parse($filePath, $this->document->mime_type);

            if (empty($text)) {
                throw new \Exception("Extraction returned empty text.");
            }

            $chunks = $chunker->chunk($text);
            $embeddings = [];

            foreach ($chunks as $chunk) {
                $vector = $embeddingService->getEmbedding($chunk);
                if (empty($vector)) {
                    throw new \Exception("Failed to generate embedding for a chunk.");
                }
                $embeddings[] = $vector;
            }

            $vectorStorage->storeChunks($this->document->id, $chunks, $embeddings);

            Log::info("Document processed and stored: {$this->document->filename}", [
                'chunk_count' => count($chunks)
            ]);

            $this->document->update(['status' => 'completed']);
        } catch (\Exception $e) {
            Log::error("Failed to process document: {$this->document->filename}. Error: " . $e->getMessage());
            $this->document->update(['status' => 'failed']);
        }
    }
}
