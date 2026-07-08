<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfileDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyProfileDocumentController extends Controller
{
    public function show(CompanyProfileDocument $companyProfileDocument): StreamedResponse
    {
        $disk = Storage::disk('public');

        abort_unless($disk->exists($companyProfileDocument->path), 404);

        return $disk->response(
            $companyProfileDocument->path,
            $companyProfileDocument->original_name,
            [
                'Content-Type' => $companyProfileDocument->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$companyProfileDocument->original_name.'"',
            ],
        );
    }
}
