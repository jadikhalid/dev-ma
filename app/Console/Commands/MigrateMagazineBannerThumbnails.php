<?php

namespace App\Console\Commands;

use App\Support\MagazineBannerStorage;
use Illuminate\Console\Command;

class MigrateMagazineBannerThumbnails extends Command
{
    protected $signature = 'magazine-banner:migrate-thumbnails';

    protected $description = 'Copie les miniatures legacy (storage/app/public) vers public/magazine-banner';

    public function handle(): int
    {
        $moved = MagazineBannerStorage::migrateLegacyFilesToPublic();

        $this->info("Miniatures copiées : {$moved}");

        return self::SUCCESS;
    }
}
