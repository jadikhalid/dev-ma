<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bio' => "Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j'accompagne les entreprises françaises et européennes dans la création d'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.",
            'experience_years' => $this->faker->numberBetween(2, 15),
            'availability' => $this->faker->randomElement(array_keys(Profile::statusOptions())),
            'is_public' => true,
            'github_url' => 'https://github.com',
            'linkedin_url' => 'https://linkedin.com',
            'portfolio_url' => 'https://google.com',
        ];
    }
}
