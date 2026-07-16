<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('articles');

        if (Schema::hasColumn('profiles', 'title')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }

        $this->deduplicateByUserId('profiles');
        $this->deduplicateByUserId('company_profiles');

        Schema::table('profiles', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        if (! Schema::hasColumn('profiles', 'title')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->string('title')->nullable()->after('user_id');
            });
        }

        if (! Schema::hasTable('articles')) {
            Schema::create('articles', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category')->default('talents');
                $table->text('excerpt');
                $table->longText('content');
                $table->json('translations')->nullable();
                $table->string('cover_emoji')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamp('published_at')->nullable();
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    private function deduplicateByUserId(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $duplicateUserIds = DB::table($table)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        foreach ($duplicateUserIds as $userId) {
            $keepId = DB::table($table)
                ->where('user_id', $userId)
                ->orderByDesc('id')
                ->value('id');

            DB::table($table)
                ->where('user_id', $userId)
                ->where('id', '!=', $keepId)
                ->delete();
        }
    }
};
