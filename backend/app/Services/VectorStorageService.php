<?php

namespace App\Services;

use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Log;

class VectorStorageService
{
    private $chunkModel;

    public function __construct(DocumentChunk $chunkModel)
    {
        $this->chunkModel = $chunkModel;
    }

    /**
     * Store processed chunks and their embeddings in MongoDB.
     */
    public function storeChunks(int $documentId, array $chunks, array $embeddings): void
    {
        try {
            foreach ($chunks as $index => $content) {
                $this->chunkModel->create([
                    'document_id' => $documentId,
                    'content' => $content,
                    'embedding' => $embeddings[$index],
                    'metadata' => [
                        'chunk_index' => $index,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Vector storage failed for document ID {$documentId}: " . $e->getMessage());
            throw $e;
        }
    }
}
