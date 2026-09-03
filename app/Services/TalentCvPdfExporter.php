<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class TalentCvPdfExporter
{
    public function download(string $html, string $filename): Response
    {
        return Pdf::loadHTML($html)->setPaper('a4')->download($filename);
    }
}
