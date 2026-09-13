<?php

namespace App\Console\Commands;

use App\Services\NewsletterDeliveryService;
use Illuminate\Console\Command;

class SendDueNewslettersCommand extends Command
{
    protected $signature = 'newsletters:send-due';

    protected $description = 'Start due scheduled newsletters and send the next paced outbox email (1/minute)';

    public function handle(NewsletterDeliveryService $delivery): int
    {
        $started = $delivery->processDueScheduled();

        if ($started > 0) {
            $this->info("Started {$started} scheduled newsletter(s).");
        }

        // One email per scheduler tick (~1 minute on Hostinger cron).
        if ($delivery->processNextPending()) {
            $this->info('Sent 1 newsletter outbox email.');
        }

        return self::SUCCESS;
    }
}
