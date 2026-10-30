<?php

namespace App\Jobs;

use App\Models\PendingRegistration;
use App\Services\PendingRegistrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurgePendingRegistrationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $pendingRegistrationId) {}

    public function handle(PendingRegistrationService $service): void
    {
        $pending = PendingRegistration::query()->find($this->pendingRegistrationId);

        if (! $pending) {
            return;
        }

        // Keep completed rows for a while so confirmation links stay idempotent.
        if ($pending->isCompleted()) {
            return;
        }

        if ($pending->isExpired()) {
            $service->purge($pending);
        }
    }
}
