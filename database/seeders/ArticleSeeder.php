<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'slug' => 'pool-talents-tech-maroc-2026',
                'category' => 'talents',
                'cover_emoji' => '🇲🇦',
                'title' => 'Le pool de talents tech au Maroc : état des lieux 2026',
                'excerpt' => 'Le Maroc compte plus de 30 000 talents tech qualifiés. Découvrez pourquoi les entreprises françaises s\'y tournent de plus en plus.',
                'content' => '<p>Le Maroc s\'est imposé comme un hub tech majeur en Afrique du Nord.</p>',
                'translations' => [
                    'en' => [
                        'title' => 'Morocco\'s tech talent pool: 2026 overview',
                        'excerpt' => 'Morocco has over 30,000 qualified tech talents. Discover why French companies are turning to them.',
                        'content' => '<p>Morocco has established itself as a major tech hub in North Africa.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'remote-france-maroc-bonnes-pratiques',
                'category' => 'guides',
                'cover_emoji' => '🌍',
                'title' => 'Remote France-Maroc : les bonnes pratiques',
                'excerpt' => 'Fuseau horaire, communication, outils collaboratifs : tout ce qu\'il faut savoir.',
                'content' => '<p>Le décalage horaire France-Maroc est un atout pour la collaboration.</p>',
                'translations' => [
                    'en' => [
                        'title' => 'France-Morocco remote: best practices',
                        'excerpt' => 'Time zones, communication, collaborative tools: everything you need to know.',
                        'content' => '<p>The France-Morocco time difference is an asset for collaboration.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'pourquoi-externaliser-developpement-maroc',
                'category' => 'ecosysteme',
                'cover_emoji' => '💼',
                'title' => 'Pourquoi externaliser son développement au Maroc ?',
                'excerpt' => 'Qualité, coût, proximité culturelle : les raisons qui convainquent les DSI françaises.',
                'content' => '<p>Les entreprises françaises choisissent le Maroc pour la qualité des profils.</p>',
                'translations' => [
                    'en' => [
                        'title' => 'Why outsource development to Morocco?',
                        'excerpt' => 'Quality, cost, cultural proximity: reasons that convince French IT departments.',
                        'content' => '<p>French companies choose Morocco for the quality of profiles.</p>',
                    ],
                ],
            ],
            [
                'slug' => 'installer-entreprise-francaise-maroc',
                'category' => 'guides',
                'cover_emoji' => '🏢',
                'title' => 'S\'installer au Maroc : guide de l\'entreprise française',
                'excerpt' => 'Juridique, fiscal, RH : les étapes clés pour implanter votre activité tech.',
                'content' => '<p>Au-delà du recrutement, de nombreuses entreprises s\'implantent au Maroc.</p>',
                'translations' => [
                    'en' => [
                        'title' => 'Setting up in Morocco: a guide for French companies',
                        'excerpt' => 'Legal, tax, HR: key steps to establish your tech business.',
                        'content' => '<p>Beyond recruitment, many companies are establishing themselves in Morocco.</p>',
                    ],
                ],
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                array_merge($article, [
                    'is_published' => true,
                    'published_at' => now()->subDays(rand(1, 60)),
                ])
            );
        }
    }
}
