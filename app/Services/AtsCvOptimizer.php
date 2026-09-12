<?php

namespace App\Services;

/**
 * Builds concrete ATS edit suggestions (add / remove / rewrite / move)
 * from diagnostic findings — never replaces the whole CV.
 */
class AtsCvOptimizer
{
    public function __construct(private AtsCompatibilityScorer $scorer) {}

    /**
     * @param  array{
     *     score: int,
     *     findings: list<array{id: string, status: string, severity: string, earned: int, max: int}>
     * }  $originalResult
     * @return array{
     *     suggestions: list<array{id: string, action: string, title: string, why: string, example: ?string}>,
     *     text: string,
     *     result: array<string, mixed>,
     *     remaining_actions: list<string>
     * }
     */
    public function optimize(string $originalText, array $originalResult, string $locale = 'fr'): array
    {
        $suggestions = $this->buildSuggestions(trim($originalText), $originalResult, $locale);
        $projected = $this->projectScore($originalResult, $suggestions);

        return [
            'suggestions' => $suggestions,
            'text' => $this->suggestionsAsPlainText($suggestions, $locale),
            'result' => $projected,
            'remaining_actions' => array_map(
                fn (array $s) => $s['title'].($s['example'] ? ' — '.$s['example'] : ''),
                $suggestions
            ),
        ];
    }

    /**
     * @param  array{findings: list<array{id: string, status: string, earned: int, max: int}>}  $originalResult
     * @param  list<array{id: string}>  $suggestions
     * @return array<string, mixed>
     */
    private function projectScore(array $originalResult, array $suggestions): array
    {
        $fixedIds = array_flip(array_column($suggestions, 'id'));
        $findings = [];

        foreach ($originalResult['findings'] ?? [] as $finding) {
            if (isset($fixedIds[$finding['id']])) {
                $findings[] = [
                    ...$finding,
                    'status' => 'pass',
                    'earned' => (int) $finding['max'],
                ];

                continue;
            }

            $findings[] = $finding;
        }

        $earned = (int) array_sum(array_column($findings, 'earned'));
        $max = (int) array_sum(array_column($findings, 'max'));
        $score = $max > 0 ? (int) round(($earned / $max) * 100) : 0;
        $passed = count(array_filter($findings, fn (array $f) => $f['status'] === 'pass'));
        $issues = count(array_filter($findings, fn (array $f) => in_array($f['status'], ['fail', 'partial', 'warn'], true)));

        return [
            'score' => max(0, min(100, $score)),
            'max_points' => $max,
            'earned_points' => $earned,
            'findings' => $findings,
            'passed_count' => $passed,
            'issue_count' => $issues,
            'char_count' => (int) ($originalResult['char_count'] ?? 0),
            'projected' => true,
        ];
    }

    /**
     * @param  array{findings: list<array{id: string, status: string}>}  $result
     * @return list<array{id: string, action: string, title: string, why: string, example: ?string}>
     */
    private function buildSuggestions(string $text, array $result, string $locale): array
    {
        $isFr = $locale !== 'en';
        $suggestions = [];
        $ctx = $this->context($text);

        foreach ($result['findings'] ?? [] as $finding) {
            if (! in_array($finding['status'], ['fail', 'partial', 'warn'], true)) {
                continue;
            }

            $suggestion = $this->suggestionForFinding($finding['id'], $finding['status'], $ctx, $isFr);
            if ($suggestion !== null) {
                $suggestions[] = $suggestion;
            }
        }

        return $suggestions;
    }

    /**
     * @param  array{name: ?string, email: ?string, phone: ?string, location: ?string, linkedin: ?string}  $ctx
     * @return array{id: string, action: string, title: string, why: string, example: ?string}|null
     */
    private function suggestionForFinding(string $id, string $status, array $ctx, bool $isFr): ?array
    {
        $name = $ctx['name'] ?: ($isFr ? 'Prénom Nom' : 'First Last');
        $city = $ctx['location'] ?: ($isFr ? 'Casablanca, Maroc' : 'Casablanca, Morocco');
        $email = $ctx['email'] ?: ($isFr ? 'votre.email@exemple.com' : 'your.email@example.com');
        $phone = $ctx['phone'] ?: '+212 6 00 00 00 00';
        $linkedin = $ctx['linkedin'] ?: 'https://www.linkedin.com/in/votre-profil';

        return match ($id) {
            'email' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez votre e-mail en texte clair' : 'Add your email as plain text',
                'why' => $isFr
                    ? 'Les ATS cherchent une adresse e-mail lisible (pas dans une image).'
                    : 'ATS tools look for a readable email address (not inside an image).',
                'example' => $email,
            ],
            'phone' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez un numéro de téléphone' : 'Add a phone number',
                'why' => $isFr
                    ? 'Un numéro en texte aide les recruteurs et les filtres ATS.'
                    : 'A plain-text phone number helps recruiters and ATS filters.',
                'example' => $phone,
            ],
            'city' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Indiquez votre localisation' : 'Add your location',
                'why' => $isFr
                    ? 'Ville / pays / remote en clair améliore le matching géographique.'
                    : 'City / country / remote in plain text improves location matching.',
                'example' => $city,
            ],
            'links' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez un lien professionnel' : 'Add a professional link',
                'why' => $isFr
                    ? 'LinkedIn, GitHub ou portfolio en URL texte sont facilement détectés.'
                    : 'LinkedIn, GitHub or portfolio as a plain URL is easy to detect.',
                'example' => $linkedin,
            ],
            'contact_header' => [
                'id' => $id,
                'action' => 'move',
                'title' => $isFr ? 'Placez vos coordonnées tout en haut' : 'Move contact details to the very top',
                'why' => $isFr
                    ? 'E-mail et téléphone doivent apparaître dans les premières lignes.'
                    : 'Email and phone should appear in the first lines.',
                'example' => $isFr
                    ? "{$name}\n{$city} | {$email} | {$phone}"
                    : "{$name}\n{$city} | {$email} | {$phone}",
            ],
            'summary_section' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez une section Profil / Résumé' : 'Add a Profile / Summary section',
                'why' => $isFr
                    ? 'Un titre de section clair aide les ATS à classer votre profil.'
                    : 'A clear section heading helps ATS tools classify your profile.',
                'example' => $isFr
                    ? "Profil\nProfessionnel motivé, orienté résultats, avec une expérience concrète sur des projets mesurables. À l’aise en collaboration remote et en environnement international. (Adaptez cette phrase à votre métier.)"
                    : "Profile\nResults-oriented professional with hands-on experience on measurable projects. Comfortable with remote collaboration and international environments. (Adapt this sentence to your role.)",
            ],
            'skills_section' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez une section Compétences' : 'Add a Skills section',
                'why' => $isFr
                    ? 'Les mots-clés de compétences sont essentiels au matching ATS.'
                    : 'Skill keywords are essential for ATS matching.',
                'example' => $isFr
                    ? "Compétences\n[Compétence 1], [Compétence 2], [Outil], [Méthode], Communication, Organisation, Travail en équipe"
                    : "Skills\n[Skill 1], [Skill 2], [Tool], [Method], Communication, Organization, Teamwork",
            ],
            'experience_section' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez une section Expérience titrée' : 'Add a titled Experience section',
                'why' => $isFr
                    ? 'Sans titre « Expérience », beaucoup d’ATS peinent à parser le parcours.'
                    : 'Without an “Experience” heading, many ATS tools struggle to parse your history.',
                'example' => $isFr
                    ? "Expérience\n[Intitulé] — [Entreprise] — 2021 - 2024\n- Réalisation concrète avec un résultat chiffré (+20 %)\n- Deuxième réalisation mesurable"
                    : "Experience\n[Job title] — [Company] — 2021 - 2024\n- Concrete achievement with a quantified result (+20%)\n- Second measurable achievement",
            ],
            'education_section' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez une section Formation' : 'Add an Education section',
                'why' => $isFr
                    ? 'Un bloc Formation clairement titré est souvent filtré par les ATS.'
                    : 'A clearly titled Education block is often used by ATS filters.',
                'example' => $isFr
                    ? "Formation\n[Diplôme] — [Établissement] — 2018"
                    : "Education\n[Degree] — [Institution] — 2018",
            ],
            'languages_section' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez une section Langues' : 'Add a Languages section',
                'why' => $isFr
                    ? 'Les langues sont fréquemment utilisées comme filtre ATS.'
                    : 'Languages are frequently used as an ATS filter.',
                'example' => $isFr
                    ? "Langues\nFrançais — courant\nAnglais — professionnel"
                    : "Languages\nFrench — fluent\nEnglish — professional",
            ],
            'certifications_section' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez vos certifications (si vous en avez)' : 'Add certifications (if any)',
                'why' => $isFr
                    ? 'Une section dédiée rend vos certificats détectables.'
                    : 'A dedicated section makes certificates easier to detect.',
                'example' => $isFr
                    ? "Certifications\n- [Nom de la certification] — [Organisme] — 2024"
                    : "Certifications\n- [Certification name] — [Issuer] — 2024",
            ],
            'experience_dates' => [
                'id' => $id,
                'action' => 'rewrite',
                'title' => $isFr ? 'Précisez les dates de chaque poste' : 'Clarify dates for each role',
                'why' => $isFr
                    ? 'Les années (début / fin) aident les ATS à lire votre parcours.'
                    : 'Years (start / end) help ATS tools read your timeline.',
                'example' => $isFr
                    ? "[Intitulé] — [Entreprise] — 2020 - 2024\n(ou : janv. 2020 – présent)"
                    : "[Job title] — [Company] — 2020 - 2024\n(or: Jan 2020 – Present)",
            ],
            'experience_bullets' => [
                'id' => $id,
                'action' => 'add',
                'title' => $isFr ? 'Ajoutez des puces de réalisations' : 'Add achievement bullets',
                'why' => $isFr
                    ? 'Les puces texte sont mieux parsées que les longs paragraphes.'
                    : 'Text bullets parse better than long paragraphs.',
                'example' => $isFr
                    ? "- Piloté [projet] avec un impact de [X %]\n- Collaboré avec [N] équipes sur [résultat]\n- Réduit [délai / coût] de [X %]"
                    : "- Led [project] with an impact of [X%]\n- Collaborated with [N] teams on [outcome]\n- Reduced [time / cost] by [X%]",
            ],
            'measurable_bullets' => [
                'id' => $id,
                'action' => 'rewrite',
                'title' => $isFr ? 'Quantifiez vos impacts' : 'Quantify your impact',
                'why' => $isFr
                    ? 'Les chiffres (%, volumes, délais) renforcent le matching et la crédibilité.'
                    : 'Numbers (%, volumes, timelines) strengthen matching and credibility.',
                'example' => $isFr
                    ? "- Amélioré le taux de conversion de 28 % en 6 mois\n- Suivi de 12 projets livrés dans les délais"
                    : "- Improved conversion rate by 28% in 6 months\n- Tracked 12 projects delivered on time",
            ],
            'no_emoji' => [
                'id' => $id,
                'action' => 'remove',
                'title' => $isFr ? 'Retirez les emoji du CV' : 'Remove emoji from the CV',
                'why' => $isFr
                    ? 'Beaucoup d’ATS gèrent mal les emoji et cassent l’extraction.'
                    : 'Many ATS tools mishandle emoji and break extraction.',
                'example' => $isFr
                    ? 'Remplacez « Motivé 🚀 » par « Motivé et orienté résultats ».'
                    : 'Replace “Motivated 🚀” with “Motivated and results-oriented”.',
            ],
            'no_photo' => [
                'id' => $id,
                'action' => 'remove',
                'title' => $isFr ? 'Retirez la photo / les images' : 'Remove the photo / images',
                'why' => $isFr
                    ? 'Une photo n’aide pas au matching et peut gêner l’extraction ATS.'
                    : 'A photo does not help matching and can hinder ATS extraction.',
                'example' => $isFr
                    ? 'Exportez une version texte/PDF sans photo ni éléments graphiques inutiles.'
                    : 'Export a text-first PDF without a photo or unnecessary graphics.',
            ],
            'length' => [
                'id' => $id,
                'action' => $status === 'fail' ? 'add' : 'rewrite',
                'title' => $isFr ? 'Enrichissez le contenu utile' : 'Enrich useful content',
                'why' => $isFr
                    ? 'Trop peu de texte limite le matching ATS.'
                    : 'Too little text limits ATS matching.',
                'example' => $isFr
                    ? "Ajoutez 4 à 6 puces d’expérience concrètes et une courte section Profil (3–4 phrases)."
                    : 'Add 4–6 concrete experience bullets and a short Profile section (3–4 sentences).',
            ],
            'text_extractable' => [
                'id' => $id,
                'action' => 'rewrite',
                'title' => $isFr ? 'Passez à un CV texte extractible' : 'Switch to an extractable text CV',
                'why' => $isFr
                    ? 'Les CV scannés / image seule sont presque invisibles pour les ATS.'
                    : 'Scanned / image-only CVs are almost invisible to ATS tools.',
                'example' => $isFr
                    ? 'Recréez le CV dans Word/Google Docs puis exportez en PDF texte (sélectionnable à la souris).'
                    : 'Rebuild the CV in Word/Google Docs, then export a text PDF (mouse-selectable).',
            ],
            default => null,
        };
    }

    /**
     * @return array{name: ?string, email: ?string, phone: ?string, location: ?string, linkedin: ?string}
     */
    private function context(string $text): array
    {
        $email = $this->firstMatch('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text);
        $phone = $this->firstMatch('/(?:\+|00)?(?:\d[\s().\-]?){8,}\d/', $text);
        $phone = $phone ? preg_replace('/\s+/', ' ', trim($phone)) : null;
        $linkedin = $this->firstMatch('/(?:https?:\/\/)?(?:www\.)?linkedin\.com\/[^\s]+/i', $text);
        if ($linkedin && ! str_starts_with(strtolower($linkedin), 'http')) {
            $linkedin = 'https://'.$linkedin;
        }

        return [
            'name' => $this->guessName($text, $email),
            'email' => $email,
            'phone' => $phone,
            'location' => $this->detectLocation($text),
            'linkedin' => $linkedin,
        ];
    }

    /**
     * @param  list<array{action: string, title: string, why: string, example: ?string}>  $suggestions
     */
    private function suggestionsAsPlainText(array $suggestions, string $locale): string
    {
        $isFr = $locale !== 'en';
        $lines = [
            $isFr ? 'Suggestions ATS — à appliquer sur votre CV actuel' : 'ATS suggestions — apply these on your current CV',
            $isFr ? 'Ce n’est pas un CV réécrit : ce sont des modifications concrètes.' : 'This is not a rewritten CV: these are concrete edits.',
            '',
        ];

        $labels = [
            'add' => $isFr ? 'À AJOUTER' : 'ADD',
            'remove' => $isFr ? 'À SUPPRIMER' : 'REMOVE',
            'rewrite' => $isFr ? 'À REFORMULER' : 'REWRITE',
            'move' => $isFr ? 'À DÉPLACER' : 'MOVE',
        ];

        foreach ($suggestions as $index => $suggestion) {
            $n = $index + 1;
            $action = $labels[$suggestion['action']] ?? strtoupper($suggestion['action']);
            $lines[] = "{$n}. [{$action}] {$suggestion['title']}";
            $lines[] = ($isFr ? 'Pourquoi : ' : 'Why: ').$suggestion['why'];
            if (! empty($suggestion['example'])) {
                $lines[] = ($isFr ? 'Exemple / texte proposé :' : 'Example / suggested text:');
                $lines[] = $suggestion['example'];
            }
            $lines[] = '';
        }

        return trim(implode("\n", $lines));
    }

    private function detectLocation(string $text): ?string
    {
        if (preg_match('/\b(Casablanca|Rabat|Marrakech|Tanger|F[eè]s|Agadir|Paris|Lyon|Marseille|Remote|T[eé]l[eé]travail|Maroc|Morocco|France)\b/iu', $text, $m)) {
            return $m[0];
        }

        return null;
    }

    private function guessName(string $text, ?string $email): ?string
    {
        $lines = preg_split("/\R/u", trim($text)) ?: [];
        foreach (array_slice($lines, 0, 5) as $line) {
            $trim = trim($line);
            if ($trim === '' || str_contains($trim, '@') || preg_match('/\d{5,}/', $trim)) {
                continue;
            }
            if (preg_match('/^[A-ZÀ-ÖØ-Þ][\p{L}\'\-]+(?:\s+[A-ZÀ-ÖØ-Þ][\p{L}\'\-]+){1,3}$/u', $trim)) {
                return $trim;
            }
        }

        if ($email && preg_match('/^([a-z0-9._%+\-]+)@/i', $email, $m)) {
            $local = str_replace(['.', '_', '-'], ' ', $m[1]);

            return mb_convert_case($local, MB_CASE_TITLE, 'UTF-8');
        }

        return null;
    }

    private function firstMatch(string $pattern, string $text): ?string
    {
        return preg_match($pattern, $text, $m) ? trim($m[0]) : null;
    }
}
