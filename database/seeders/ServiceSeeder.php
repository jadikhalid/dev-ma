<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'slug' => 'conseil-juridique',
                'icon' => '⚖️',
                'title' => 'Conseil juridique',
                'summary' => 'Création de société, contrats, conformité et accompagnement réglementaire pour s\'installer au Maroc.',
                'content' => '<p>Nous accompagnons les entreprises françaises et européennes dans leurs démarches juridiques au Maroc.</p>',
                'sort_order' => 1,
                'translations' => [
                    'en' => ['title' => 'Legal advisory', 'summary' => 'Company formation, contracts, compliance and regulatory support for setting up in Morocco.', 'content' => '<p>We support French and European companies with legal procedures in Morocco.</p>'],
                ],
            ],
            [
                'slug' => 'conseil-financier',
                'icon' => '💰',
                'title' => 'Conseil financier & DAF',
                'summary' => 'Fiscalité, comptabilité, trésorerie et pilotage financier pour votre activité marocaine.',
                'content' => '<p>Optimisez votre structure financière au Maroc avec un accompagnement sur mesure.</p>',
                'sort_order' => 2,
                'translations' => [
                    'en' => ['title' => 'Financial advisory & CFO', 'summary' => 'Tax, accounting, treasury and financial management for your Moroccan operations.', 'content' => '<p>Optimize your financial structure in Morocco with tailored support.</p>'],
                ],
            ],
            [
                'slug' => 'relations-publiques',
                'icon' => '📢',
                'title' => 'Relations publiques',
                'summary' => 'Visibilité locale, relations presse et communication pour ancrer votre marque au Maroc.',
                'content' => '<p>Développez votre notoriété au Maroc grâce à une stratégie RP adaptée.</p>',
                'sort_order' => 3,
                'translations' => [
                    'en' => ['title' => 'Public relations', 'summary' => 'Local visibility, press relations and communication to anchor your brand in Morocco.', 'content' => '<p>Build your reputation in Morocco with an adapted PR strategy.</p>'],
                ],
            ],
            [
                'slug' => 'ressources-humaines',
                'icon' => '👥',
                'title' => 'Ressources humaines',
                'summary' => 'Recrutement local, gestion des équipes et conformité sociale marocaine.',
                'content' => '<p>Constituez et managez vos équipes au Maroc.</p>',
                'sort_order' => 4,
                'translations' => [
                    'en' => ['title' => 'Human resources', 'summary' => 'Local recruitment, team management and Moroccan labour compliance.', 'content' => '<p>Build and manage your teams in Morocco.</p>'],
                ],
            ],
            [
                'slug' => 'accompagnement-implantation',
                'icon' => '🏢',
                'title' => 'Accompagnement implantation',
                'summary' => 'Bureau, coworking, réseau local et mise en relation avec l\'écosystème tech marocain.',
                'content' => '<p>De la recherche de locaux à l\'intégration dans les hubs tech.</p>',
                'sort_order' => 5,
                'translations' => [
                    'en' => ['title' => 'Setup support', 'summary' => 'Office, coworking, local network and connection to the Moroccan tech ecosystem.', 'content' => '<p>From finding premises to integrating into tech hubs.</p>'],
                ],
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
