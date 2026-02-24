<?php

namespace Tests\Unit;

use App\Services\VectorStorageService;
use App\Models\DocumentChunk;
use Tests\TestCase;
use Mockery;

class VectorStorageServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_it_stores_chunks_in_mongodb()
    {
        $modelMock = Mockery::mock(DocumentChunk::class);
        $modelMock->shouldReceive('create')
                  ->times(2)
                  ->andReturn(new DocumentChunk());

        $service = new VectorStorageService($modelMock);
        $chunks = ["chunk 1", "chunk 2"];
        $embeddings = [[0.1], [0.2]];

        $service->storeChunks(1, 1, $chunks, $embeddings);

        $this->assertTrue(true);
    }
}
