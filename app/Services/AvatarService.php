<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AvatarService
{
    public const MAX_SIZE = 400;

    /** @var list<string> */
    public const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    public function store(User $user, UploadedFile $file): string
    {
        $this->validateUpload($file);

        [$contents, $extension] = $this->process($file);

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = 'avatars/'.$user->id.'.'.$extension;
        Storage::disk('public')->put($path, $contents);

        $user->update(['avatar_path' => $path]);

        return $path;
    }

    public function storeAt(UploadedFile $file, string $basename, ?string $oldPath = null): string
    {
        $this->validateUpload($file);

        [$contents, $extension] = $this->process($file);

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $basename.'.'.$extension;
        Storage::disk('public')->put($path, $contents);

        return $path;
    }

    public function deleteAt(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function validateUpload(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'avatar' => __('talenma.account.avatar_invalid_type'),
            ]);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'avatar' => __('talenma.account.avatar_too_large'),
            ]);
        }
    }

    public function delete(User $user): void
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function process(UploadedFile $file): array
    {
        if ($this->gdAvailable()) {
            return [$this->resizeToSquareWithGd($file), 'jpg'];
        }

        return [
            (string) file_get_contents($file->getRealPath()),
            $this->extensionForMime($file->getMimeType()),
        ];
    }

    private function gdAvailable(): bool
    {
        return \extension_loaded('gd')
            && \function_exists('imagecreatetruecolor')
            && \function_exists('imagejpeg');
    }

    private function resizeToSquareWithGd(UploadedFile $file): string
    {
        $source = $this->createImageResource($file);

        if (! $source) {
            throw ValidationException::withMessages([
                'avatar' => __('talenma.account.avatar_invalid_type'),
            ]);
        }

        $width = \imagesx($source);
        $height = \imagesy($source);

        if ($width < 1 || $height < 1) {
            \imagedestroy($source);
            throw ValidationException::withMessages([
                'avatar' => __('talenma.account.avatar_invalid_type'),
            ]);
        }

        $cropSide = min($width, $height);
        $srcX = (int) floor(($width - $cropSide) / 2);
        $srcY = (int) floor(($height - $cropSide) / 2);
        $outputSize = min(self::MAX_SIZE, $cropSide);

        $target = \imagecreatetruecolor($outputSize, $outputSize);

        if (! $target) {
            \imagedestroy($source);
            throw ValidationException::withMessages([
                'avatar' => __('talenma.account.avatar_invalid_type'),
            ]);
        }

        \imagecopyresampled(
            $target,
            $source,
            0,
            0,
            $srcX,
            $srcY,
            $outputSize,
            $outputSize,
            $cropSide,
            $cropSide,
        );
        \imagedestroy($source);

        ob_start();
        \imagejpeg($target, null, 85);
        $contents = (string) ob_get_clean();
        \imagedestroy($target);

        return $contents;
    }

    /**
     * @return resource|null
     */
    private function createImageResource(UploadedFile $file)
    {
        $path = $file->getRealPath();

        return match ($file->getMimeType()) {
            'image/jpeg' => \function_exists('imagecreatefromjpeg') ? @\imagecreatefromjpeg($path) : null,
            'image/png' => \function_exists('imagecreatefrompng') ? @\imagecreatefrompng($path) : null,
            'image/webp' => \function_exists('imagecreatefromwebp') ? @\imagecreatefromwebp($path) : null,
            default => null,
        };
    }

    private function extensionForMime(?string $mime): string
    {
        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}
