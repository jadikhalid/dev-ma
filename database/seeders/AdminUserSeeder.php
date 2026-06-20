<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@talentsdumaroc.com');
        $password = env('ADMIN_PASSWORD', 'ChangeMe-Admin-2026!');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrateur',
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_subscribed' => false,
                'subscription_expires_at' => null,
            ]
        );
    }
}
