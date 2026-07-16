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

    public const MAX_OTHER = 3;

    public const MAX_FILE_SIZE = 1024;

    /**
     * @param  list<UploadedFile>  $files
     */
    public function storeMany(Profile $profile, array $files, string $type = ProfileDocument::TYPE_REGISTRATION): void
    {
        if ($type === ProfileDocument::TYPE_OTHER && count($files) > self::MAX_OTHER) {
            throw ValidationException::withMessages([
                'other_documents' => __('talenma.talent.documents_other_max'),
            ]);
        }

        if ($type === ProfileDocument::TYPE_REGISTRATION && count($files) > self::MAX_OTHER) {
            throw ValidationException::withMessages([
                'documents' => __('talenma.auth.validation.documents_max'),
            ]);
        }

        $nextSort = ((int) $profile->documents()->ofType($type)->max('sort_order')) + 1;
        $errorKey = $type === ProfileDocument::TYPE_OTHER ? 'other_documents' : 'documents';

        foreach ($files as $file) {
            $this->storeOne($profile, $file, $nextSort++, $type, $errorKey);
        }
    }

    public function storeCv(Profile $profile, UploadedFile $file): ProfileDocument
    {
        $existing = $profile->documents()->ofType(ProfileDocument::TYPE_CV)->get();

        foreach ($existing as $document) {
            $this->delete($document);
        }

        return $this->storeOne($profile, $file, 1, ProfileDocument::TYPE_CV, 'cv');
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    public function storeOthers(Profile $profile, array $files): void
    {
        $files = array_values(array_filter($files));
        $currentCount = $profile->documents()->ofType(ProfileDocument::TYPE_OTHER)->count();

        if ($currentCount + count($files) > self::MAX_OTHER) {
            throw ValidationException::withMessages([
                'other_documents' => __('talenma.talent.documents_other_max'),
            ]);
        }

        $this->storeMany($profile, $files, ProfileDocument::TYPE_OTHER);
    }

    public function storeOne(
        Profile $profile,
        UploadedFile $file,
        int $sortOrder,
        string $type = ProfileDocument::TYPE_REGISTRATION,
        string $errorKey = 'documents',
    ): ProfileDocument {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                $errorKey => __('talenma.auth.validation.documents_type'),
            ]);
        }

        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw ValidationException::withMessages([
                $errorKey => __('talenma.auth.validation.documents_size'),
            ]);
        }

        $extension = $file->guessExtension() ?: 'bin';
        $path = $file->storeAs(
            'profile-documents/'.$profile->id,
            uniqid('doc_', true).'.'.$extension,
            'public',
        );

        return $profile->documents()->create([
            'document_type' => $type,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => (int) $file->getSize(),
            'sort_order' => $sortOrder,
        ]);
    }

    public function delete(ProfileDocument $document): void
    {
        Storage::disk('public')->delete($document->path);
        $document->delete();
    }
}
