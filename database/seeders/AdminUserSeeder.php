<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@talentsdumaroc.com');
        $password = env('ADMIN_PASSWORD', 'ChangeMe-Admin-2026!');

        $existing = User::query()->where('email', $email)->first();

        if ($existing) {
            // Ne jamais écraser le mot de passe en prod (reset oublié / déploiement).
            $existing->forceFill([
                'role' => 'admin',
                'email_verified_at' => $existing->email_verified_at ?? now(),
                'approval_status' => null,
                'approved_at' => null,
            ])->save();

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
