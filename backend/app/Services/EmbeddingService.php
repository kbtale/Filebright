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
     * Get the vector embedding for a given text.
     */
    public function getEmbedding(string $text): array
    {
        $sanitizedText = $this->sanitize($text);
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url'),
            ])->post('https://openrouter.ai/api/v1/embeddings', [
                'model' => $this->model,
                'input' => $sanitizedText,
            ]);

            if ($response->failed()) {
                Log::error("OpenRouter Embedding API failed: " . $response->body());
                return [];
            }

            return $response->json('data.0.embedding') ?? [];
        } catch (\Exception $e) {
            Log::error("Embedding API Exception: " . $e->getMessage());
            return [];
        }
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
