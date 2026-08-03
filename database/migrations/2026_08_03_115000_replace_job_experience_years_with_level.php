<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('experience_level', 20)
                ->nullable()
                ->after('profession_id');
        });

        if (Schema::hasColumn('job_postings', 'experience_years')) {
            DB::table('job_postings')
                ->whereNotNull('experience_years')
                ->orderBy('id')
                ->chunkById(100, function ($rows): void {
                    foreach ($rows as $row) {
                        $years = (int) $row->experience_years;
                        $level = match (true) {
                            $years < 3 => 'beginner',
                            $years <= 7 => 'junior',
                            default => 'senior',
                        };

                        DB::table('job_postings')
                            ->where('id', $row->id)
                            ->update(['experience_level' => $level]);
                    }
                });

            Schema::table('job_postings', function (Blueprint $table) {
                $table->dropColumn('experience_years');
            });
        }
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->unsignedTinyInteger('experience_years')
                ->nullable()
                ->after('profession_id');
        });

        DB::table('job_postings')
            ->whereNotNull('experience_level')
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $years = match ($row->experience_level) {
                        'beginner' => 1,
                        'junior' => 5,
                        'senior' => 8,
                        default => null,
                    };

                    DB::table('job_postings')
                        ->where('id', $row->id)
                        ->update(['experience_years' => $years]);
                }
            });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('experience_level');
        });
    }
};
