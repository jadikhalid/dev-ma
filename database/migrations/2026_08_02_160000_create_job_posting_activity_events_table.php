<?php

use App\Models\JobPosting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posting_activity_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained('company_profiles')->cascadeOnDelete();
            $table->foreignId('job_posting_id')->nullable()->constrained('job_postings')->nullOnDelete();
            $table->string('job_title', 255);
            $table->string('event', 40);
            $table->string('status', 40)->nullable();
            $table->string('actor_label', 255)->nullable();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_self')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['company_profile_id', 'created_at'], 'job_activity_company_created_idx');
            $table->index(['job_posting_id', 'created_at'], 'job_activity_job_created_idx');
        });

        $now = now();

        DB::table('job_postings')->orderBy('id')->chunkById(100, function ($rows) use ($now) {
            $inserts = [];

            foreach ($rows as $row) {
                $inserts[] = [
                    'company_profile_id' => $row->company_profile_id,
                    'job_posting_id' => $row->id,
                    'job_title' => $row->title,
                    'event' => 'created',
                    'status' => JobPosting::STATUS_DRAFT,
                    'actor_label' => null,
                    'actor_user_id' => $row->created_by,
                    'is_self' => true,
                    'created_at' => $row->created_at ?? $now,
                ];

                if ($row->published_at) {
                    $inserts[] = [
                        'company_profile_id' => $row->company_profile_id,
                        'job_posting_id' => $row->id,
                        'job_title' => $row->title,
                        'event' => 'published',
                        'status' => JobPosting::STATUS_PUBLISHED,
                        'actor_label' => null,
                        'actor_user_id' => $row->created_by,
                        'is_self' => true,
                        'created_at' => $row->published_at,
                    ];
                }

                if ($row->closed_at || $row->status === JobPosting::STATUS_CLOSED) {
                    $inserts[] = [
                        'company_profile_id' => $row->company_profile_id,
                        'job_posting_id' => $row->id,
                        'job_title' => $row->title,
                        'event' => 'closed',
                        'status' => JobPosting::STATUS_CLOSED,
                        'actor_label' => null,
                        'actor_user_id' => $row->created_by,
                        'is_self' => true,
                        'created_at' => $row->closed_at ?? $row->updated_at ?? $now,
                    ];
                }

                if ($row->status === JobPosting::STATUS_HIDDEN) {
                    $inserts[] = [
                        'company_profile_id' => $row->company_profile_id,
                        'job_posting_id' => $row->id,
                        'job_title' => $row->title,
                        'event' => 'hidden',
                        'status' => JobPosting::STATUS_HIDDEN,
                        'actor_label' => null,
                        'actor_user_id' => $row->created_by,
                        'is_self' => true,
                        'created_at' => $row->updated_at ?? $now,
                    ];
                }

                if ($row->status === JobPosting::STATUS_POSTPONED) {
                    $inserts[] = [
                        'company_profile_id' => $row->company_profile_id,
                        'job_posting_id' => $row->id,
                        'job_title' => $row->title,
                        'event' => 'postponed',
                        'status' => JobPosting::STATUS_POSTPONED,
                        'actor_label' => null,
                        'actor_user_id' => $row->created_by,
                        'is_self' => true,
                        'created_at' => $row->updated_at ?? $now,
                    ];
                }
            }

            if ($inserts !== []) {
                DB::table('job_posting_activity_events')->insert($inserts);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posting_activity_events');
    }
};
