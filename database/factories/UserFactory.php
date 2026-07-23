<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fullName = fake()->name();
        $parts = preg_split('/\s+/u', trim($fullName), 2) ?: [];

        return [
            'name' => $fullName,
            'first_name' => $parts[0] ?? $fullName,
            'last_name' => $parts[1] ?? '',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'approval_status' => 'approved',
            'approved_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function companyOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'company',
            'company_seat' => User::SEAT_OWNER,
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);
    }

    public function companyMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'company',
            'company_seat' => User::SEAT_MEMBER,
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);
    }

    public function talent(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'dev',
            'company_seat' => null,
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);
    }
}
