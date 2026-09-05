<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;

class AtsCvTextExtractor
{
    public const MIN_CHARS = 80;

    /**
     * @return array{text: string, char_count: int, extension: string}
     */
    public function extract(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');
        $path = $file->getRealPath();

        if ($path === false || $path === '') {
            throw new RuntimeException('upload_unreadable');
        }

        $text = match ($extension) {
            'pdf' => $this->fromPdf($path),
            'docx' => $this->fromDocx($path),
            'txt' => $this->fromTxt($path),
            default => throw new RuntimeException('unsupported_type'),
        };

        $text = $this->normalize($text);
        $charCount = mb_strlen($text);

        if ($charCount < self::MIN_CHARS) {
            throw new RuntimeException('text_too_short');
        }

        return [
            'text' => $text,
            'char_count' => $charCount,
            'extension' => $extension,
        ];
    }

    private function fromPdf(string $path): string
    {
        try {
            $pdf = (new PdfParser)->parseFile($path);

            return $pdf->getText() ?? '';
        } catch (\Throwable) {
            throw new RuntimeException('pdf_parse_failed');
        }
    }

    private function fromDocx(string $path): string
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException('docx_parse_failed');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false || $xml === '') {
            throw new RuntimeException('docx_parse_failed');
        }

        $withBreaks = str_replace(['</w:p>', '</w:tr>', '<w:tab/>', '<w:br/>'], ["\n", "\n", "\t", "\n"], $xml);
        $plain = strip_tags($withBreaks);

        return html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function fromTxt(string $path): string
    {
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('txt_parse_failed');
        }

        return $raw;
    }

    private function normalize(string $text): string
    {
        $text = str_replace("\0", '', $text);
        $text = preg_replace("/[ \t]+/u", ' ', $text) ?? $text;
        $text = preg_replace("/\r\n?|\n/u", "\n", $text) ?? $text;
        $text = preg_replace("/\n{3,}/u", "\n\n", $text) ?? $text;

        return trim($text);
    }
}
