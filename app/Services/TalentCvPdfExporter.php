<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class TalentCvPdfExporter
{
    public function download(string $html, string $filename): Response
    {
        $driver = (string) config('talenma.cv_builder.pdf_driver', 'browsershot');

        if ($driver === 'browsershot') {
            try {
                return $this->browsershotDownload($html, $filename);
            } catch (\Throwable $e) {
                Log::warning('CV PDF Browsershot failed, falling back to DomPDF', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $this->dompdfDownload($html, $filename);
    }

    private function browsershotDownload(string $html, string $filename): Response
    {
        $shot = Browsershot::html($html)
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->timeout((int) config('talenma.cv_builder.browsershot_timeout', 120));

        $chromePath = $this->resolveChromePath();
        if ($chromePath !== null) {
            $shot->setChromePath($chromePath);
        }

        $nodeBinary = config('talenma.cv_builder.node_binary');
        if (is_string($nodeBinary) && $nodeBinary !== '') {
            $shot->setNodeBinary($nodeBinary);
        }

        $npmBinary = config('talenma.cv_builder.npm_binary');
        if (is_string($npmBinary) && $npmBinary !== '') {
            $shot->setNpmBinary($npmBinary);
        }

        $pdf = $shot->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function dompdfDownload(string $html, string $filename): Response
    {
        return Pdf::loadHTML($html)->setPaper('a4')->download($filename);
    }

    private function resolveChromePath(): ?string
    {
        $configured = config('talenma.cv_builder.chrome_path');
        if (is_string($configured) && $configured !== '' && is_file($configured)) {
            return $configured;
        }

        if (PHP_OS_FAMILY !== 'Windows') {
            return null;
        }

        $candidates = array_filter([
            getenv('PROGRAMFILES') ? getenv('PROGRAMFILES').'\Google\Chrome\Application\chrome.exe' : null,
            getenv('PROGRAMFILES(X86)') ? getenv('PROGRAMFILES(X86)').'\Google\Chrome\Application\chrome.exe' : null,
            getenv('LOCALAPPDATA') ? getenv('LOCALAPPDATA').'\Google\Chrome\Application\chrome.exe' : null,
        ]);

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
