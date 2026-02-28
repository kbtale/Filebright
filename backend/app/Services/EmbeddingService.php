<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private $apiKey;
    private $model;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
        $this->model = config('services.openrouter.embedding_model', 'text-embedding-3-small');
    }

    /**
     * Get vector embeddings for multiple text chunks in a single API call.
     */
    public function getBulkEmbeddings(array $texts): array
    {
        if (empty($texts)) return [];

        $sanitizedTexts = array_map([$this, 'sanitize'], $texts);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url'),
            ])->post('https://openrouter.ai/api/v1/embeddings', [
                'model' => $this->model,
                'input' => $sanitizedTexts,
            ]);

            if ($response->failed()) {
                Log::error("OpenRouter Bulk Embedding API failed: " . $response->body());
                return [];
            }

            $data = $response->json('data');
            if (empty($data)) return [];

            // Sort by index if provided by API, though usually it matches order
            usort($data, fn($a, $b) => ($a['index'] ?? 0) <=> ($b['index'] ?? 0));

            return array_map(fn($item) => $item['embedding'], $data);
        } catch (\Exception $e) {
            Log::error("Bulk Embedding API Exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get the vector embedding for a given text.
     */
    public function getEmbedding(string $text): array
    {
        $response = $this->getBulkEmbeddings([$text]);
        return $response[0] ?? [];
    }

    /**
     * Sanitize string to ensure it's valid UTF-8.
     */
    private function sanitize(string $text): string
    {
        // Strip malformed UTF-8 characters
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }
}
