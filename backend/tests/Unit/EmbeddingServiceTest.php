<?php

namespace Tests\Unit;

use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmbeddingServiceTest extends TestCase
{
    public function test_it_returns_embedding_from_openrouter()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/embeddings' => Http::response([
                'data' => [
                    [
                        'embedding' => [0.1, 0.2, 0.3]
                    ]
                ]
            ], 200)
        ]);

        $service = new EmbeddingService();
        $embedding = $service->getEmbedding("test text");

        $this->assertEquals([0.1, 0.2, 0.3], $embedding);
    }

    public function test_it_handles_api_failure()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/embeddings' => Http::response("Error", 500)
        ]);

        $service = new EmbeddingService();
        $embedding = $service->getEmbedding("test text");

        $this->assertEmpty($embedding);
    }
}
