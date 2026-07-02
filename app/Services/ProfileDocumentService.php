<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\ProfileDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileDocumentService
{
    /** @var list<string> */
    public const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public const MAX_FILES = 3;

    public const MAX_FILE_SIZE = 1024;

    /**
     * @param  list<UploadedFile>  $files
     */
    public function storeMany(Profile $profile, array $files): void
    {
        if (count($files) > self::MAX_FILES) {
            throw ValidationException::withMessages([
                'documents' => __('talenma.auth.validation.documents_max'),
            ]);
        }

        foreach ($files as $index => $file) {
            $this->storeOne($profile, $file, $index + 1);
        }
    }

    public function storeOne(Profile $profile, UploadedFile $file, int $sortOrder): ProfileDocument
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'documents' => __('talenma.auth.validation.documents_type'),
            ]);
        }

        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw ValidationException::withMessages([
                'documents' => __('talenma.auth.validation.documents_size'),
            ]);
        }

        $extension = $file->guessExtension() ?: 'bin';
        $path = $file->storeAs(
            'profile-documents/'.$profile->id,
            uniqid('doc_', true).'.'.$extension,
            'public',
        );

        return $profile->documents()->create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => (int) $file->getSize(),
            'sort_order' => $sortOrder,
        ]);
    }
}
