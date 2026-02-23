<?php

namespace App\Services;

class TextChunkerService
{
    /**
     * Chunk text into overlapping segments.
     * 1200 characters target p chunk with 300 character overlap.
     */
    public function chunk(string $text, int $chunkSize = 1200, int $overlap = 300): array
    {
        if (empty($text)) {
            return [];
        }

        $text = preg_replace('/\s+/', ' ', trim($text));
        $textLength = strlen($text);

        if ($textLength <= $chunkSize) {
            return [$text];
        }

        $chunks = [];
        $start = 0;

        while ($start < $textLength) {
            $end = $start + $chunkSize;

            if ($end < $textLength) {
                $lastSpace = strrpos(substr($text, $start, $chunkSize), ' ');
                if ($lastSpace !== false && $lastSpace > ($chunkSize - 100)) {
                    $end = $start + $lastSpace;
                }
            } else {
                $end = $textLength;
            }

            $chunk = trim(substr($text, $start, $end - $start));
            if (!empty($chunk)) {
                $chunks[] = $chunk;
            }

            if ($end >= $textLength) {
                break;
            }

            $nextStart = $end - $overlap;
            $start = ($nextStart > $start) ? $nextStart : $end;
        }

        return $chunks;
    }
}
