<?php

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const TITLE = 'Invitation à créer un compte — abonnés non inscrits';

    public function up(): void
    {
        if (Newsletter::query()->where('title', self::TITLE)->exists()) {
            return;
        }

        $blocks = [
            [
                'type' => Newsletter::BLOCK_HEADER,
                'title' => 'Votre talent mérite une vitrine internationale',
                'subtitle' => 'Créez votre compte gratuit en 2 minutes et débloquez visibilité, offres d\'emploi et outils carrière.',
            ],
            [
                'type' => Newsletter::BLOCK_HERO,
                'image_url' => '/images/newsletter/guest-signup-hero.jpg',
                'alt' => 'Des talents marocains visibles auprès des entreprises du monde entier',
            ],
            [
                'type' => Newsletter::BLOCK_FEATURE,
                'title' => '🌍 Visible à l\'international',
                'body' => 'Des recruteurs au Maroc et à l\'étranger consultent votre profil. Présentez-vous une fois, ils viennent à vous.',
            ],
            [
                'type' => Newsletter::BLOCK_FEATURE,
                'title' => '💼 Des annonces pour vous',
                'body' => 'Des offres ciblées pour les talents marocains. Postulez en quelques clics.',
            ],
            [
                'type' => Newsletter::BLOCK_TEXT,
                'body' => "🧰  MES APPLICATIONS : votre boîte à outils carrière\n\n•  Créateur de CV : un CV professionnel en quelques minutes, à partir de modèles modernes.\n•  ATS Score : vérifiez si votre CV passe les filtres automatiques des recruteurs et améliorez-le.\n•  Bibliothèque : des ouvrages pour progresser dans votre métier.\n•  Bientôt : la Vidéothèque et l'E Académie pour continuer à vous former.",
            ],
            [
                'type' => Newsletter::BLOCK_REGISTER,
            ],
        ];

        Newsletter::query()->create([
            'title' => self::TITLE,
            'subject' => 'Créez votre compte gratuit et rendez votre talent visible à l\'international 🌍',
            'headline' => 'Rejoignez Talents du Maroc',
            'locale' => Newsletter::LOCALE_FR,
            'audience' => Newsletter::AUDIENCE_GUESTS,
            'status' => Newsletter::STATUS_DRAFT,
            'body_blocks' => $blocks,
            'created_by' => User::query()->where('role', 'admin')->orderBy('id')->value('id'),
        ]);
    }

    public function down(): void
    {
        Newsletter::query()
            ->where('title', self::TITLE)
            ->where('status', Newsletter::STATUS_DRAFT)
            ->delete();
    }
};
