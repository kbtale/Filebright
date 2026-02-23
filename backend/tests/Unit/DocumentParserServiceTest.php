<?php

namespace Tests\Unit;

use App\Services\DocumentParserService;
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Document;
use Tests\TestCase;
use Mockery;

class DocumentParserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_it_parses_txt_file()
    {
        $pdfParser = Mockery::mock(Parser::class);
        $service = new DocumentParserService($pdfParser);
        
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, "Hello World TXT");

        $result = $service->parse($tempFile, 'text/plain');

        $this->assertEquals("Hello World TXT", $result);
        unlink($tempFile);
    }

    public function test_it_parses_pdf_file()
    {
        $pdfParser = Mockery::mock(Parser::class);
        $document = Mockery::mock(Document::class);
        
        $pdfParser->shouldReceive('parseFile')
                  ->once()
                  ->andReturn($document);
        
        $document->shouldReceive('getText')
                 ->once()
                 ->andReturn("Hello World PDF");

        $service = new DocumentParserService($pdfParser);
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        
        $result = $service->parse($tempFile, 'application/pdf');

        $this->assertEquals("Hello World PDF", $result);
        unlink($tempFile);
    }
}
