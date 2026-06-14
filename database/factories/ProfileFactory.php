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
        $titles = [
            'Talent Full Stack MERN',
            'Ingénieur Backend Laravel & Node.js',
            'Talent Frontend React / Next.js',
            'Architecte Cloud & PHP Engineer',
            'Talent Mobile Flutter & React Native'
        ];

        $villes = ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir'];

        $skills = ['Laravel', 'React', 'Node.js', 'PHP', 'TypeScript', 'Flutter', 'Docker', 'MySQL'];

        return [
            'title' => $this->faker->randomElement($titles),
            'bio' => "Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j'accompagne les entreprises françaises et européennes dans la création d'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.",
            'experience_years' => $this->faker->numberBetween(2, 15),
            'daily_rate_eur' => $this->faker->numberBetween(250, 600),
            'availability' => $this->faker->randomElement(['disponible', 'sous 2 semaines', 'mission en cours']),
            'city' => $this->faker->randomElement($villes),
            'country' => 'Maroc',
            'skills' => $this->faker->randomElements($skills, 4),
            'github_url' => 'https://github.com',
            'linkedin_url' => 'https://linkedin.com',
            'portfolio_url' => 'https://google.com',
        ];
    }
}
