<?php

namespace Database\Seeders;

use App\Models\Profession;
use App\Models\ProfessionSuggestion;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $webDeveloper = Profession::updateOrCreate(
            ['slug' => 'web-developer'],
            [
                'name_fr' => 'Développeur web',
                'name_en' => 'Web developer',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $suggestions = [
            ['label_fr' => 'Laravel', 'label_en' => 'Laravel', 'keywords' => 'php framework'],
            ['label_fr' => 'React', 'label_en' => 'React', 'keywords' => 'javascript frontend js'],
            ['label_fr' => 'Vue.js', 'label_en' => 'Vue.js', 'keywords' => 'javascript frontend js'],
            ['label_fr' => 'Next.js', 'label_en' => 'Next.js', 'keywords' => 'react javascript frontend'],
            ['label_fr' => 'Angular', 'label_en' => 'Angular', 'keywords' => 'typescript frontend'],
            ['label_fr' => 'PHP', 'label_en' => 'PHP', 'keywords' => 'backend symfony laravel'],
            ['label_fr' => 'Symfony', 'label_en' => 'Symfony', 'keywords' => 'php backend'],
            ['label_fr' => 'JavaScript', 'label_en' => 'JavaScript', 'keywords' => 'js frontend backend node'],
            ['label_fr' => 'TypeScript', 'label_en' => 'TypeScript', 'keywords' => 'ts javascript'],
            ['label_fr' => 'Node.js', 'label_en' => 'Node.js', 'keywords' => 'javascript backend api'],
            ['label_fr' => 'Développeur full-stack', 'label_en' => 'Full-stack developer', 'keywords' => 'fullstack full stack'],
            ['label_fr' => 'Développeur frontend', 'label_en' => 'Frontend developer', 'keywords' => 'front-end ui'],
            ['label_fr' => 'Développeur backend', 'label_en' => 'Backend developer', 'keywords' => 'back-end api'],
            ['label_fr' => 'API REST', 'label_en' => 'REST API', 'keywords' => 'api backend'],
            ['label_fr' => 'GraphQL', 'label_en' => 'GraphQL', 'keywords' => 'api backend'],
            ['label_fr' => 'MySQL', 'label_en' => 'MySQL', 'keywords' => 'sql database bdd'],
            ['label_fr' => 'PostgreSQL', 'label_en' => 'PostgreSQL', 'keywords' => 'sql database bdd postgres'],
            ['label_fr' => 'MongoDB', 'label_en' => 'MongoDB', 'keywords' => 'nosql database'],
            ['label_fr' => 'Docker', 'label_en' => 'Docker', 'keywords' => 'devops conteneur container'],
            ['label_fr' => 'DevOps', 'label_en' => 'DevOps', 'keywords' => 'ci cd infrastructure'],
            ['label_fr' => 'Tailwind CSS', 'label_en' => 'Tailwind CSS', 'keywords' => 'css frontend ui'],
            ['label_fr' => 'WordPress', 'label_en' => 'WordPress', 'keywords' => 'cms php'],
            ['label_fr' => 'Shopify', 'label_en' => 'Shopify', 'keywords' => 'e-commerce ecommerce'],
            ['label_fr' => 'React Native', 'label_en' => 'React Native', 'keywords' => 'mobile javascript'],
            ['label_fr' => 'Flutter', 'label_en' => 'Flutter', 'keywords' => 'mobile dart'],
            ['label_fr' => 'Python', 'label_en' => 'Python', 'keywords' => 'django flask backend'],
            ['label_fr' => 'Django', 'label_en' => 'Django', 'keywords' => 'python backend'],
            ['label_fr' => 'Product Designer', 'label_en' => 'Product Designer', 'keywords' => 'ux ui design'],
            ['label_fr' => 'UI/UX Designer', 'label_en' => 'UI/UX Designer', 'keywords' => 'design figma'],
        ];

        foreach ($suggestions as $index => $item) {
            ProfessionSuggestion::updateOrCreate(
                [
                    'profession_id' => $webDeveloper->id,
                    'label_fr' => $item['label_fr'],
                ],
                [
                    'label_en' => $item['label_en'],
                    'keywords' => $item['keywords'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
