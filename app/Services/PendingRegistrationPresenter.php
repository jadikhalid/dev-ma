<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class PendingRegistrationPresenter
{
    public function present(User $user): array
    {
        $user->loadMissing([
            'profile.professionSector',
            'profile.documents',
        ]);

        $profile = $user->profile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'registered_at' => $user->created_at?->translatedFormat('d M Y, H:i'),
            'sector' => $profile?->sectorLabel() ?? '—',
            'description' => $profile?->registration_description ?? '—',
            'documents' => $profile?->documents
                ->map(fn ($document) => [
                    'id' => $document->id,
                    'name' => $document->original_name,
                    'url' => $document->url(),
                    'size' => $document->formattedSize(),
                    'is_image' => Str::startsWith($document->mime_type ?? '', 'image/'),
                ])
                ->values()
                ->all(),
            'approve_url' => route('admin.users.approve', $user),
            'reject_url' => route('admin.users.reject', $user),
        ];
    }
}
