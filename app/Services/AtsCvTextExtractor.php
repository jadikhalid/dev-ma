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
     * @return array{text: string, char_count: int, extension: string, has_embedded_image: bool}
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
            'has_embedded_image' => $this->hasEmbeddedImage($path, $extension),
        ];
    }

    /**
     * Detect portrait/logo/decorative images that often hurt ATS parsing.
     */
    public function hasEmbeddedImage(string $path, string $extension): bool
    {
        return match ($extension) {
            'pdf' => $this->pdfHasImage($path),
            'docx' => $this->docxHasImage($path),
            default => false,
        };
    }

    private function pdfHasImage(string $path): bool
    {
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $found = false;
        while (! feof($handle)) {
            $chunk = fread($handle, 1024 * 256);
            if ($chunk === false || $chunk === '') {
                break;
            }

            // PDF image XObjects / inline image markers.
            if (
                preg_match('/\/Subtype\s*\/Image\b/', $chunk)
                || preg_match('/\bBI\b[\s\S]{0,200}\/W\s+\d+/', $chunk)
            ) {
                $found = true;
                break;
            }
        }

        fclose($handle);

        return $found;
    }

    private function docxHasImage(string $path): bool
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            return false;
        }

        $found = false;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (! is_string($name)) {
                continue;
            }

            if (preg_match('#^word/media/.+\.(png|jpe?g|gif|bmp|webp|tiff?|emf|wmf)$#i', $name)) {
                $found = true;
                break;
            }
        }

        $zip->close();

        return $found;
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
