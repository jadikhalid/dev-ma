<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Bootstrap one-shot du compte admin.
 * Ne doit PAS être appelé à chaque déploiement (voir ProductionDataSeeder).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('talenma.admin.email', 'admin@talentsdumaroc.com');
        $password = (string) config('talenma.admin.password', 'ChangeMe-Admin-2026!');

        if ($email === '' || User::query()->where('email', $email)->exists()) {
            return;
        }

        User::create([
            'email' => $email,
            'name' => 'Administrateur',
            'password' => $password,
            'role' => 'admin',
            'email_verified_at' => now(),
            'approval_status' => null,
            'approved_at' => null,
            'is_subscribed' => false,
            'subscription_expires_at' => null,
        ]);
    }
}
