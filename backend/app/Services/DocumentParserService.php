<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

class DocumentParserService
{
    private $pdfParser;

    public function __construct(Parser $pdfParser)
    {
        $this->pdfParser = $pdfParser;
    }

    /**
     * Parse text from a file based on its MIME type.
     */
    public function parse(string $filePath, string $mimeType): string
    {
        if (!file_exists($filePath)) {
            return '';
        }

        return match ($mimeType) {
            'application/pdf' => $this->parsePdf($filePath),
            'text/plain' => file_get_contents($filePath) ?: '',
            default => '',
        };
    }

    /**
     * Extract text from a PDF file.
     */
    private function parsePdf(string $filePath): string
    {
        try {
            $pdf = $this->pdfParser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error("PDF Parsing failed: " . $e->getMessage());
            return '';
        }
    }
}
