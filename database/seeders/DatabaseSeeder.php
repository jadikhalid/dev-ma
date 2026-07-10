<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            ProfessionSeeder::class,
            SocialFeedSeeder::class,
            SocialPostSeeder::class,
            AdminUserSeeder::class,
            DevTalentSeeder::class,
        ]);

        // Jeu de données générique complémentaire (hors catalogue métier).
        User::factory(3)->create([
            'role' => 'dev',
            'is_subscribed' => false,
            'subscription_expires_at' => null,
        ])->each(function ($user) {
            $user->profile()->create(Profile::factory()->make()->toArray());
        });

        User::factory(5)->create(['role' => 'company'])->each(function ($user) {
            $user->companyProfile()->create(CompanyProfile::factory()->make()->toArray());
        });
    }
}
