<?php

namespace App\Console\Commands;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ObscurePublicMediaPathsCommand extends Command
{
    protected $signature = 'media:obscure-public-paths {--dry-run : List renames without writing}';

    protected $description = 'Rename sequential public media filenames (avatars, logos, contact photos) to unguessable paths';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $renamed = 0;

        User::query()
            ->whereNotNull('avatar_path')
            ->where('avatar_path', '!=', '')
            ->orderBy('id')
            ->each(function (User $user) use ($dryRun, &$renamed): void {
                if ($this->relocate($user->avatar_path, 'avatars', $dryRun, $newPath)) {
                    if (! $dryRun) {
                        $user->forceFill(['avatar_path' => $newPath])->saveQuietly();
                    }
                    $renamed++;
                }
            });

        CompanyProfile::query()
            ->where(function ($query): void {
                $query->where(function ($logo): void {
                    $logo->whereNotNull('logo_path')->where('logo_path', '!=', '');
                })->orWhere(function ($contact): void {
                    $contact->whereNotNull('representative_photo_path')
                        ->where('representative_photo_path', '!=', '');
                });
            })
            ->orderBy('id')
            ->each(function (CompanyProfile $profile) use ($dryRun, &$renamed): void {
                $dirty = false;

                if ($this->relocate($profile->logo_path, 'company-logos', $dryRun, $newLogo)) {
                    if (! $dryRun) {
                        $profile->logo_path = $newLogo;
                        $dirty = true;
                    }
                    $renamed++;
                }

                if ($this->relocate($profile->representative_photo_path, 'company-contacts', $dryRun, $newContact)) {
                    if (! $dryRun) {
                        $profile->representative_photo_path = $newContact;
                        $dirty = true;
                    }
                    $renamed++;
                }

                if ($dirty) {
                    $profile->saveQuietly();
                }
            });

        $this->info($dryRun
            ? "Dry-run: {$renamed} sequential public media path(s) would be renamed."
            : "{$renamed} sequential public media path(s) renamed.");

        return self::SUCCESS;
    }

    private function relocate(?string $path, string $directory, bool $dryRun, ?string &$newPath): bool
    {
        $newPath = null;

        if (! is_string($path) || $path === '') {
            return false;
        }

        if (! preg_match('#^'.preg_quote($directory, '#').'/(\d+)(\.[a-z0-9]+)$#i', $path, $matches)) {
            return false;
        }

        $extension = strtolower($matches[2]);
        $candidate = $directory.'/'.Str::lower(Str::random(40)).$extension;

        if ($dryRun) {
            $this->line("{$path} → {$candidate}");
            $newPath = $candidate;

            return true;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            $this->warn("Missing file, clearing path: {$path}");
            $newPath = null;

            return true;
        }

        while ($disk->exists($candidate)) {
            $candidate = $directory.'/'.Str::lower(Str::random(40)).$extension;
        }

        $disk->move($path, $candidate);
        $this->line("{$path} → {$candidate}");
        $newPath = $candidate;

        return true;
    }
}
