<?php

namespace App\Services;

use App\Models\User;
use App\Support\PublicStorageUrl;
use Illuminate\Support\Facades\Storage;

/**
 * Miniature floutée de la photo d'un talent, générée côté serveur :
 * les clients mail ignorent le CSS `filter: blur()`.
 */
class NewsletterBlurredAvatar
{
    private const DIRECTORY = 'newsletter/blurred-avatars';

    private const SIZE = 112;

    private const PIXELATE_SIZE = 10;

    private const BLUR_PASSES = 20;

    private const VERSION = 'v2';

    public function urlFor(User $talent): ?string
    {
        $source = (string) $talent->avatar_path;
        $disk = Storage::disk('public');

        if ($source === '' || ! $disk->exists($source) || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $target = self::DIRECTORY.'/'.sha1(self::VERSION.'|'.$source.'|'.$disk->lastModified($source)).'.jpg';

        if (! $disk->exists($target)) {
            $blurred = $this->blur((string) $disk->get($source));

            if ($blurred === null) {
                return null;
            }

            $disk->put($target, $blurred);
        }

        return PublicStorageUrl::make($target);
    }

    private function blur(string $contents): ?string
    {
        $image = @imagecreatefromstring($contents);

        if ($image === false) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $side = min($width, $height);

        $tiny = imagecreatetruecolor(self::PIXELATE_SIZE, self::PIXELATE_SIZE);
        imagefill($tiny, 0, 0, imagecolorallocate($tiny, 255, 255, 255));
        imagecopyresampled(
            $tiny,
            $image,
            0,
            0,
            (int) (($width - $side) / 2),
            (int) (($height - $side) / 2),
            self::PIXELATE_SIZE,
            self::PIXELATE_SIZE,
            $side,
            $side,
        );
        imagedestroy($image);

        $thumb = imagecreatetruecolor(self::SIZE, self::SIZE);
        imagecopyresampled($thumb, $tiny, 0, 0, 0, 0, self::SIZE, self::SIZE, self::PIXELATE_SIZE, self::PIXELATE_SIZE);
        imagedestroy($tiny);

        for ($i = 0; $i < self::BLUR_PASSES; $i++) {
            imagefilter($thumb, IMG_FILTER_GAUSSIAN_BLUR);
        }

        ob_start();
        imagejpeg($thumb, null, 82);
        imagedestroy($thumb);

        return (string) ob_get_clean();
    }
}
