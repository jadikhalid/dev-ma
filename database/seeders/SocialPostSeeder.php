<?php

namespace Database\Seeders;

use App\Models\SocialPost;
use Illuminate\Database\Seeder;

class SocialPostSeeder extends Seeder
{
    public function run(): void
    {
        if (SocialPost::query()->exists()) {
            return;
        }

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
                'url' => 'https://x.com',
                'network' => 'x',
            ],
        ];

        foreach ($defaults as $item) {
            SocialPost::pushPost($item);
        }
    }
}
