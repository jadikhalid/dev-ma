<?php

namespace App\Console\Commands;

use App\Services\PendingRegistrationService;
use Illuminate\Console\Command;

class PurgeExpiredPendingRegistrationsCommand extends Command
{
    protected $signature = 'registrations:purge-expired';

    protected $description = 'Supprime les inscriptions en attente dont le lien de vérification a expiré';

    public function handle(PendingRegistrationService $service): int
    {
        $count = $service->purgeExpired();

        if ($count > 0) {
            $this->info("{$count} inscription(s) expirée(s) supprimée(s).");
        }

        return self::SUCCESS;
    }
}
