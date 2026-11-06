<?php

use App\Models\PendingRegistration;
use App\Services\PendingRegistrationService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Prolong unfinished email confirmations from 24h to 48h
     * (expires_at = created_at + EXPIRY_HOURS). No schema change.
     */
    public function up(): void
    {
        $this->recomputeExpiry(PendingRegistrationService::EXPIRY_HOURS);
    }

    public function down(): void
    {
        $this->recomputeExpiry(24);
    }

    private function recomputeExpiry(int $hours): void
    {
        PendingRegistration::query()
            ->whereNull('completed_at')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($hours): void {
                foreach ($rows as $pending) {
                    if ($pending->created_at === null) {
                        continue;
                    }

                    $pending->forceFill([
                        'expires_at' => $pending->created_at->copy()->addHours($hours),
                    ])->save();
                }
            });
    }
};
