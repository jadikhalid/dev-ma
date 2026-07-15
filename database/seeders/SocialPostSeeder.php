<?php

namespace Database\Seeders;

use App\Models\SocialPost;
use Illuminate\Database\Seeder;

class SocialPostSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'title' => 'Talents du Maroc — la plateforme qui connecte',
                'subtitle' => 'Découvrez notre vision sur LinkedIn',
                'url' => 'https://www.linkedin.com/company/talents-du-maroc',
                'network' => 'linkedin',
            ],
            [
                'title' => 'Recruter des talents marocains à distance',
                'subtitle' => 'Nos conseils pour les entreprises européennes',
                'url' => 'https://x.com/talentsdumaroc',
                'network' => 'x',
            ],
            [
                'title' => 'Coulisses d’un entretien avec un talent tech',
                'subtitle' => 'Retour d’expérience sur Instagram',
                'url' => 'https://www.instagram.com/talentsdumaroc',
                'network' => 'instagram',
            ],
            [
                'title' => 'Pourquoi les recruteurs misent sur le Maroc',
                'subtitle' => 'Tendances RH et mobilité internationale',
                'url' => 'https://www.linkedin.com/pulse/talents-du-maroc-recrutement',
                'network' => 'linkedin',
            ],
        ];

        foreach ($defaults as $item) {
            if (SocialPost::query()->where('url', $item['url'])->exists()) {
                continue;
            }

            SocialPost::create($item);
        }
    }
}
