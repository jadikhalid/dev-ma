<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('representative_name')->nullable()->after('last_name');
            $table->string('representative_email')->nullable()->after('representative_name');
            $table->string('phone', 30)->nullable()->after('representative_email');
            $table->string('linkedin_url')->nullable()->after('phone');
        });

        if (Schema::hasColumn('company_profiles', 'representative_name')) {
            $rows = DB::table('company_profiles')
                ->select([
                    'user_id',
                    'representative_name',
                    'representative_email',
                    'phone',
                    'linkedin_url',
                ])
                ->get();

            foreach ($rows as $row) {
                DB::table('users')
                    ->where('id', $row->user_id)
                    ->update([
                        'representative_name' => $row->representative_name,
                        'representative_email' => $row->representative_email,
                        'phone' => $row->phone,
                        'linkedin_url' => $row->linkedin_url,
                    ]);
            }

            Schema::table('company_profiles', function (Blueprint $table) {
                $table->dropColumn([
                    'representative_name',
                    'representative_email',
                    'phone',
                    'linkedin_url',
                ]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('representative_name')->nullable()->after('user_id');
            $table->string('representative_email')->nullable()->after('representative_name');
            $table->string('phone')->nullable()->after('representative_email');
            $table->string('linkedin_url')->nullable()->after('phone');
        });

        $rows = DB::table('users')
            ->where('role', 'company')
            ->whereNotNull('company_seat')
            ->select([
                'id',
                'representative_name',
                'representative_email',
                'phone',
                'linkedin_url',
            ])
            ->get();

        foreach ($rows as $row) {
            DB::table('company_profiles')
                ->where('user_id', $row->id)
                ->update([
                    'representative_name' => $row->representative_name,
                    'representative_email' => $row->representative_email,
                    'phone' => $row->phone,
                    'linkedin_url' => $row->linkedin_url,
                ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'representative_name',
                'representative_email',
                'phone',
                'linkedin_url',
            ]);
        });
    }
};
