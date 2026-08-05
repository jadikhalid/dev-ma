<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('profiles', 'registration_description')) {
            DB::table('profiles')
                ->whereNotNull('registration_description')
                ->where(function ($query) {
                    $query->whereNull('bio')->orWhere('bio', '');
                })
                ->update(['bio' => DB::raw('registration_description')]);

            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('registration_description');
            });
        }

        $companyPairs = [
            'registration_sector' => 'sector',
            'registration_description' => 'description',
            'registration_hiring_needs' => 'hiring_needs',
        ];

        $drop = [];

        foreach ($companyPairs as $snapshot => $target) {
            if (! Schema::hasColumn('company_profiles', $snapshot)) {
                continue;
            }

            DB::table('company_profiles')
                ->whereNotNull($snapshot)
                ->where(function ($query) use ($target) {
                    $query->whereNull($target)->orWhere($target, '');
                })
                ->update([$target => DB::raw($snapshot)]);

            $drop[] = $snapshot;
        }

        if ($drop !== []) {
            Schema::table('company_profiles', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('registration_description', 500)->nullable()->after('specialization');
        });

        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('registration_sector')->nullable();
            $table->text('registration_description')->nullable();
            $table->text('registration_hiring_needs')->nullable();
        });
    }
};
