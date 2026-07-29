<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_request_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_request_id')->constrained('recruitment_requests')->cascadeOnDelete();
            $table->string('event', 32);
            $table->string('status', 32);
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['recruitment_request_id', 'created_at'], 'rr_status_events_request_created_idx');
        });

        $now = now();

        DB::table('recruitment_requests')->orderBy('id')->chunkById(100, function ($rows) use ($now) {
            $inserts = [];

            foreach ($rows as $row) {
                $inserts[] = [
                    'recruitment_request_id' => $row->id,
                    'event' => 'submitted',
                    'status' => 'pending',
                    'actor_user_id' => $row->company_user_id,
                    'created_at' => $row->created_at ?? $now,
                ];

                $status = $row->status ?: 'pending';

                if ($status === 'cancelled') {
                    $status = 'completed';
                }

                if ($status !== 'pending' || filled($row->status_updated_at)) {
                    $inserts[] = [
                        'recruitment_request_id' => $row->id,
                        'event' => 'status_changed',
                        'status' => $status,
                        'actor_user_id' => $row->status_updated_by,
                        'created_at' => $row->status_updated_at ?? $row->updated_at ?? $now,
                    ];
                }
            }

            if ($inserts !== []) {
                DB::table('recruitment_request_status_events')->insert($inserts);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_request_status_events');
    }
};
