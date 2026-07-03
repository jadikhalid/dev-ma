<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileDocumentController extends Controller
{
    public function show(ProfileDocument $profileDocument): StreamedResponse
    {
        $disk = Storage::disk('public');

        abort_unless($disk->exists($profileDocument->path), 404);

        return $disk->response(
            $profileDocument->path,
            $profileDocument->original_name,
            [
                'Content-Type' => $profileDocument->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$profileDocument->original_name.'"',
            ],
        );
    }
}
