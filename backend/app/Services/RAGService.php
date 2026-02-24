<?php

namespace App\Services;

use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RAGService
{
    protected $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    public function answer(string $query, int $userId): string
    {
        $queryEmbedding = $this->embeddingService->getEmbedding($query);

        if (empty($queryEmbedding)) {
            return "I'm sorry, I couldn't process your request at the moment.";
        }

        $chunks = $this->retrieveContext($queryEmbedding, $userId);

        if ($chunks->isEmpty()) {
            return "I couldn't find any relevant information in your documents to answer that.";
        }

        $context = $chunks->pluck('content')->implode("\n\n---\n\n");

        return $this->getLLMResponse($query, $context);
    }

    protected function retrieveContext(array $embedding, int $userId): \Illuminate\Support\Collection
    {
        return DocumentChunk::raw(function ($collection) use ($embedding, $userId) {
            return $collection->aggregate([
                [
                    '$vectorSearch' => [
                        'index' => 'vector_index',
                        'path' => 'embedding',
                        'queryVector' => $embedding,
                        'numCandidates' => 100,
                        'limit' => 3,
                        'filter' => [
                            'metadata.user_id' => $userId
                        ]
                    ]
                ]
            ]);
        });
    }

    protected function getLLMResponse(string $query, string $context): string
    {
        $apiKey = config('services.openrouter.key');
        $model = config('services.openrouter.chat_model', 'openai/gpt-3.5-turbo');

        $prompt = "You are a helpful assistant. Use the following pieces of retrieved context to answer the user's question.\n\n"
                . "Context:\n" . $context . "\n\n"
                . "Question: " . $query . "\n\n"
                . "Answer:";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? "Error retrieving response.";
            }

            Log::error("OpenRouter API Error: " . $response->body());
            return "Error communicating with AI service.";
        } catch (\Exception $e) {
            Log::error("RAG Service Exception: " . $e->getMessage());
            return "An unexpected error occurred.";
        }
    }
}
