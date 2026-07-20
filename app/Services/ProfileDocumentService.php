<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\ProfileDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProfileDocumentService
{
    /** @var list<string> */
    public const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public const MAX_REGISTRATION = 5;

    public const MAX_CV = 4;

    public const MAX_FILE_SIZE = 1024;

    /**
     * @param  list<UploadedFile>  $files
     */
    public function storeMany(Profile $profile, array $files, string $type = ProfileDocument::TYPE_REGISTRATION): void
    {
        if ($type === ProfileDocument::TYPE_REGISTRATION && count($files) > self::MAX_REGISTRATION) {
            throw ValidationException::withMessages([
                'certification_documents' => __('talenma.auth.validation.documents_max'),
            ]);
        }

        $nextSort = ((int) $profile->documents()->ofType($type)->max('sort_order')) + 1;
        $errorKey = $type === ProfileDocument::TYPE_REGISTRATION ? 'certification_documents' : 'documents';

        foreach ($files as $file) {
            $this->storeOne($profile, $file, $nextSort++, $type, $errorKey);
        }
    }

    public function storeCv(Profile $profile, UploadedFile $file, string $language): ProfileDocument
    {
        if (! in_array($language, ProfileDocument::CV_LANGUAGES, true)) {
            throw ValidationException::withMessages([
                'cv_language' => __('talenma.talent.cv_language_invalid'),
            ]);
        }

        $incomingName = mb_strtolower(trim($file->getClientOriginalName()));
        $duplicateExists = $profile->documents()
            ->ofType(ProfileDocument::TYPE_CV)
            ->where('language', '!=', $language)
            ->get()
            ->contains(fn (ProfileDocument $document) => mb_strtolower(trim($document->original_name)) === $incomingName);

        if ($duplicateExists) {
            throw ValidationException::withMessages([
                'cv' => __('talenma.talent.cv_docs_duplicate'),
            ]);
        }

        $existing = $profile->documents()
            ->ofType(ProfileDocument::TYPE_CV)
            ->where('language', $language)
            ->get();

        foreach ($existing as $document) {
            $this->delete($document);
        }

        $sortOrder = array_search($language, ProfileDocument::CV_LANGUAGES, true);
        $sortOrder = $sortOrder === false ? 1 : ($sortOrder + 1);

        return $this->storeOne(
            $profile,
            $file,
            $sortOrder,
            ProfileDocument::TYPE_CV,
            'cv',
            $language,
        );
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    public function storeRegistrationDocs(Profile $profile, array $files): void
    {
        $files = array_values(array_filter($files));
        $currentCount = $profile->documents()->ofType(ProfileDocument::TYPE_REGISTRATION)->count();

        if ($currentCount + count($files) > self::MAX_REGISTRATION) {
            throw ValidationException::withMessages([
                'certification_documents' => __('talenma.auth.validation.documents_max'),
            ]);
        }

        $knownNames = $profile->documents()
            ->ofType(ProfileDocument::TYPE_REGISTRATION)
            ->pluck('original_name')
            ->map(fn (string $name) => mb_strtolower(trim($name)))
            ->filter()
            ->values()
            ->all();

        foreach ($files as $file) {
            $nameKey = mb_strtolower(trim((string) $file->getClientOriginalName()));

            if ($nameKey === '' || in_array($nameKey, $knownNames, true)) {
                throw ValidationException::withMessages([
                    'certification_documents' => __('talenma.talent.certifications_docs_duplicate'),
                ]);
            }

            $knownNames[] = $nameKey;
        }

        $this->storeMany($profile, $files, ProfileDocument::TYPE_REGISTRATION);
    }

    public function storeOne(
        Profile $profile,
        UploadedFile $file,
        int $sortOrder,
        string $type = ProfileDocument::TYPE_REGISTRATION,
        string $errorKey = 'documents',
        ?string $language = null,
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
            'language' => $language,
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

    /**
     * @return array<string, string>
     */
    public static function cvLanguageOptions(): array
    {
        $options = [];

        foreach (ProfileDocument::CV_LANGUAGES as $code) {
            $options[$code] = __('talenma.talent.lang_'.$code);
        }

        return $options;
    }

    public static function cvLanguageRule(): array
    {
        return ['required', 'string', Rule::in(ProfileDocument::CV_LANGUAGES)];
    }
}
