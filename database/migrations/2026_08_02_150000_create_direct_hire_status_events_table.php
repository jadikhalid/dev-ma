<?php

use App\Models\DirectHireRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_hire_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_hire_request_id')->constrained('direct_hire_requests')->cascadeOnDelete();
            $table->string('event', 40);
            $table->string('status', 40);
            $table->text('comment')->nullable();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['direct_hire_request_id', 'created_at'], 'dh_status_events_request_created_idx');
        });

        $now = now();

        DB::table('direct_hire_requests')->orderBy('id')->chunkById(100, function ($rows) use ($now) {
            $inserts = [];

            foreach ($rows as $row) {
                $inserts[] = [
                    'direct_hire_request_id' => $row->id,
                    'event' => 'proposed',
                    'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
                    'comment' => null,
                    'actor_user_id' => $row->initiated_by_user_id ?: $row->company_user_id,
                    'created_at' => $row->created_at ?? $now,
                ];

                if ($row->talent_decision_at) {
                    $decisionStatus = match ($row->status) {
                        DirectHireRequest::STATUS_DECLINED => DirectHireRequest::STATUS_DECLINED,
                        DirectHireRequest::STATUS_DEFERRED => DirectHireRequest::STATUS_DEFERRED,
                        DirectHireRequest::STATUS_WITHDRAWN => DirectHireRequest::STATUS_IN_PROCESS,
                        DirectHireRequest::STATUS_HIRED,
                        DirectHireRequest::STATUS_CLOSED_NEGATIVE,
                        DirectHireRequest::STATUS_IN_PROCESS => DirectHireRequest::STATUS_IN_PROCESS,
                        default => $row->status === DirectHireRequest::STATUS_PENDING_RESPONSE
                            ? DirectHireRequest::STATUS_IN_PROCESS
                            : $row->status,
                    };

                    // Prefer mapping from current status when it still reflects the talent decision.
                    if ($row->status === DirectHireRequest::STATUS_DEFERRED) {
                        $decisionStatus = DirectHireRequest::STATUS_DEFERRED;
                    } elseif ($row->status === DirectHireRequest::STATUS_DECLINED) {
                        $decisionStatus = DirectHireRequest::STATUS_DECLINED;
                    } elseif (in_array($row->status, [
                        DirectHireRequest::STATUS_IN_PROCESS,
                        DirectHireRequest::STATUS_HIRED,
                        DirectHireRequest::STATUS_CLOSED_NEGATIVE,
                        DirectHireRequest::STATUS_WITHDRAWN,
                    ], true)) {
                        $decisionStatus = DirectHireRequest::STATUS_IN_PROCESS;
                    }

                    $inserts[] = [
                        'direct_hire_request_id' => $row->id,
                        'event' => 'talent_decision',
                        'status' => $decisionStatus,
                        'comment' => filled($row->talent_decision_note) ? $row->talent_decision_note : null,
                        'actor_user_id' => $row->talent_user_id,
                        'created_at' => $row->talent_decision_at,
                    ];
                }

                if ($row->company_deferral_responded_at) {
                    $inserts[] = [
                        'direct_hire_request_id' => $row->id,
                        'event' => 'deferral_acknowledged',
                        'status' => DirectHireRequest::STATUS_DEFERRED,
                        'comment' => filled($row->company_deferral_note) ? $row->company_deferral_note : null,
                        'actor_user_id' => $row->company_user_id ?: $row->initiated_by_user_id,
                        'created_at' => $row->company_deferral_responded_at,
                    ];
                }

                if ($row->closed_at && in_array($row->status, [
                    DirectHireRequest::STATUS_WITHDRAWN,
                    DirectHireRequest::STATUS_HIRED,
                    DirectHireRequest::STATUS_CLOSED_NEGATIVE,
                ], true)) {
                    $inserts[] = [
                        'direct_hire_request_id' => $row->id,
                        'event' => $row->status === DirectHireRequest::STATUS_WITHDRAWN ? 'withdrawn' : 'closed',
                        'status' => $row->status,
                        'comment' => filled($row->closure_note) ? $row->closure_note : null,
                        'actor_user_id' => $row->closed_by,
                        'created_at' => $row->closed_at,
                    ];
                }
            }

            if ($inserts !== []) {
                DB::table('direct_hire_status_events')->insert($inserts);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_hire_status_events');
    }
};
