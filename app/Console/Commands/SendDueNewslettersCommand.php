<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use App\Services\NewsletterDeliveryService;
use Illuminate\Console\Command;

class SendDueNewslettersCommand extends Command
{
    protected $signature = 'newsletters:send-due';

    protected $description = 'Send newsletters whose scheduled_at is due';

    public function handle(NewsletterDeliveryService $delivery): int
    {
        $due = Newsletter::query()
            ->where('status', Newsletter::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at')
            ->get();

        foreach ($due as $newsletter) {
            $count = $delivery->deliver($newsletter);
            $this->info("Newsletter #{$newsletter->id} sent to {$count} recipients.");
        }

        return self::SUCCESS;
    }
}
