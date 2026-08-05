<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legacy moderator access mapped onto the new permission set.
     *
     * @var list<string>
     */
    private const LEGACY_PERMISSIONS = [
        'accounts.view',
        'accounts.approve',
        'accounts.reject',
        'accounts.delete',
        'sourcing.manage',
        'direct_hire.manage',
        'jobs.manage',
        'staff_messages.manage',
    ];

    public function up(): void
    {
        Schema::create('moderator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('granted_at');
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('revoked_at');
        });

        Schema::create('moderator_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('permission', 64);
            $table->timestamps();

            $table->unique(['moderator_assignment_id', 'permission'], 'moderator_permissions_unique');
        });

        $legacyModerators = DB::table('users')
            ->where('role', 'moderator')
            ->get(['id']);

        foreach ($legacyModerators as $moderator) {
            $assignmentId = DB::table('moderator_assignments')->insertGetId([
                'user_id' => $moderator->id,
                'granted_by' => null,
                'granted_at' => now(),
                'revoked_by' => null,
                'revoked_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (self::LEGACY_PERMISSIONS as $permission) {
                DB::table('moderator_permissions')->insert([
                    'moderator_assignment_id' => $assignmentId,
                    'permission' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $hasProfile = DB::table('profiles')->where('user_id', $moderator->id)->exists();

            DB::table('users')->where('id', $moderator->id)->update([
                'role' => 'dev',
                'approval_status' => 'approved',
                'approved_at' => DB::raw('COALESCE(approved_at, NOW())'),
                'updated_at' => now(),
            ]);

            if (! $hasProfile) {
                DB::table('profiles')->insert([
                    'user_id' => $moderator->id,
                    'experience_years' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $assignments = DB::table('moderator_assignments')
            ->whereNull('revoked_at')
            ->get(['user_id']);

        foreach ($assignments as $assignment) {
            DB::table('users')->where('id', $assignment->user_id)->update([
                'role' => 'moderator',
                'approval_status' => null,
                'updated_at' => now(),
            ]);
        }

        Schema::dropIfExists('moderator_permissions');
        Schema::dropIfExists('moderator_assignments');
    }
};
