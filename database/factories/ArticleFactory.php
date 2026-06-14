<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Article> */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->randomElement([
            'Le pool de talents tech au Maroc en 2026',
            'Pourquoi les entreprises françaises externalisent au Maroc',
            'Remote : comment collaborer avec un dev marocain',
            'Laravel et React : les stacks les plus demandées',
            'De Casablanca à Paris : parcours de freelances marocains',
        ]);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 999),
            'category' => $this->faker->randomElement(['talents', 'remote', 'ecosysteme', 'guides']),
            'excerpt' => $this->faker->paragraph(2),
            'content' => collect(range(1, 4))->map(fn () => '<p>' . $this->faker->paragraph(4) . '</p>')->join(''),
            'cover_emoji' => $this->faker->randomElement(['🇲🇦', '💻', '🌍', '🚀', '📰']),
            'is_published' => true,
            'published_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
