<?php

namespace Tests\Unit;

use App\Services\EmbeddingService;
use App\Services\RAGService;
use App\Models\DocumentChunk;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class RAGServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_it_returns_ai_answer_using_retrieved_context()
    {
        $embeddingMock = Mockery::mock(EmbeddingService::class);
        $embeddingMock->shouldReceive('getEmbedding')->andReturn([0.1, 0.2]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'The answer is 42']]
                ]
            ], 200)
        ]);

        $mockChunk1 = new DocumentChunk(['content' => 'Retrieval context 1']);
        $mockChunk2 = new DocumentChunk(['content' => 'Retrieval context 2']);
        
        DocumentChunk::shouldReceive('raw')->andReturn(collect([$mockChunk1, $mockChunk2]));

        $service = new RAGService($embeddingMock);
        $answer = $service->answer("What is the meaning of life?", 1);

        $this->assertEquals('The answer is 42', $answer);
    }
}
