<?php

namespace Tests\Unit;

use App\Services\TextChunkerService;
use PHPUnit\Framework\TestCase;

class TextChunkerServiceTest extends TestCase
{
    public function test_it_chunks_text_with_overlap()
    {
        $service = new TextChunkerService();
        $text = str_repeat("abcdefghij ", 150); // 1650 chars
        
        // Target 1200 chars with 300 overlap
        $chunks = $service->chunk($text, 1200, 300);

        $this->assertCount(2, $chunks);
        // Length should be around 1200 (- the trailing space which is trimmed)
        $this->assertGreaterThan(1100, strlen($chunks[0]));
        $this->assertLessThanOrEqual(1200, strlen($chunks[0]));
        $this->assertStringContainsString(substr($chunks[0], -300), $chunks[1]);
    }

    public function test_it_handles_short_text()
    {
        $service = new TextChunkerService();
        $text = "Short text.";
        $chunks = $service->chunk($text, 1200, 300);

        $this->assertCount(1, $chunks);
        $this->assertEquals($text, $chunks[0]);
    }

    public function test_it_handles_empty_text()
    {
        $service = new TextChunkerService();
        $chunks = $service->chunk("");
        $this->assertEmpty($chunks);
    }
}
