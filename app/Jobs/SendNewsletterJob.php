<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Services\NewsletterDeliveryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNewsletterJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $newsletterId) {}

    public function handle(NewsletterDeliveryService $delivery): void
    {
        $newsletter = Newsletter::query()->find($this->newsletterId);
        if (! $newsletter) {
            return;
        }

        if ($newsletter->status === Newsletter::STATUS_CANCELLED) {
            return;
        }

        if ($newsletter->status === Newsletter::STATUS_SENT) {
            return;
        }

        if ($newsletter->status !== Newsletter::STATUS_SCHEDULED) {
            return;
        }

        $delivery->deliver($newsletter);
    }
}
