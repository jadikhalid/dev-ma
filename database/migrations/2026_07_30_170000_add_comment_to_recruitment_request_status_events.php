<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitment_request_status_events', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('status');
        });

        // Best-effort: attach the current admin comment to the latest eligible event
        // so the visible commentaire is not lost from the timeline.
        $requests = DB::table('recruitment_requests')
            ->whereNotNull('admin_comment')
            ->where('admin_comment', '!=', '')
            ->get(['id', 'admin_comment']);

        foreach ($requests as $request) {
            $eventId = DB::table('recruitment_request_status_events')
                ->where('recruitment_request_id', $request->id)
                ->whereIn('event', ['status_changed', 'comment_updated'])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->value('id');

            if ($eventId) {
                DB::table('recruitment_request_status_events')
                    ->where('id', $eventId)
                    ->whereNull('comment')
                    ->update(['comment' => $request->admin_comment]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('recruitment_request_status_events', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
