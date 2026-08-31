<?php

namespace App\Services;

use App\Models\TalentCvDraft;
use App\Models\User;
use App\Support\TalentCv\TalentCvDraftDefaults;
use App\Support\TalentCv\TalentCvPhotoResolver;
use App\Support\TalentCv\TalentCvTemplateCatalog;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class TalentCvBuilderService
{
    public function draftForUser(User $user): TalentCvDraft
    {
        $existing = TalentCvDraft::query()->where('user_id', $user->id)->first();

        if ($existing) {
            $prefilled = TalentCvDraftDefaults::mergeEmptyWithSample(
                $existing->data ?? [],
                $existing->locale ?? TalentCvDraft::LOCALE_FR
            );

            $normalizedTemplate = TalentCvTemplateCatalog::normalizeTemplate((string) ($existing->template ?? ''));

            if ($prefilled !== $existing->data || $normalizedTemplate !== $existing->template) {
                $existing->data = $prefilled;
                $existing->template = $normalizedTemplate;
                $existing->save();
            }

            return $existing;
        }

        $locale = App::getLocale() === 'en' ? TalentCvDraft::LOCALE_EN : TalentCvDraft::LOCALE_FR;

        return TalentCvDraft::query()->create([
            'user_id' => $user->id,
            'template' => TalentCvDraft::TEMPLATE_CLASSIC,
            'locale' => $locale,
            'data' => TalentCvDraftDefaults::fromUser($user, $locale),
        ]);
    }

    /** @param  array<string, mixed>  $payload */
    public function updateDraft(TalentCvDraft $draft, array $payload): TalentCvDraft
    {
        if (isset($payload['template']) && TalentCvTemplateCatalog::isValidTemplate((string) $payload['template'])) {
            $draft->template = (string) $payload['template'];
        }

        if (isset($payload['locale']) && in_array($payload['locale'], [TalentCvDraft::LOCALE_FR, TalentCvDraft::LOCALE_EN], true)) {
            $draft->locale = (string) $payload['locale'];
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            $draft->data = TalentCvDraftDefaults::merge($payload['data']);
        }

        $draft->save();

        return $draft->fresh();
    }

    public function previewView(TalentCvDraft $draft, User $user): View
    {
        return view(TalentCvTemplateCatalog::viewName($draft->template), $this->templateData($draft, $user));
    }

    public function exportPdf(TalentCvDraft $draft, User $user): Response
    {
        $html = view(
            TalentCvTemplateCatalog::viewName($draft->template),
            $this->templateData($draft, $user)
        )->render();

        return app(TalentCvPdfExporter::class)->download($html, $this->safeFilename($draft));
    }

    /** @return array<string, mixed> */
    private function templateData(TalentCvDraft $draft, User $user): array
    {
        $photoResolver = app(TalentCvPhotoResolver::class);

        return [
            'data' => $draft->data,
            'locale' => $draft->locale,
            'preview' => true,
            'user' => $user,
            'photoSrc' => $photoResolver->resolve($draft->data ?? [], $user),
        ];
    }

    private function safeFilename(TalentCvDraft $draft): string
    {
        $base = preg_replace('/[^a-zA-Z0-9_-]+/', '-', (string) ($draft->data['full_name'] ?? 'cv')) ?: 'cv';

        return strtolower(trim($base, '-')).'-'.$draft->locale.'.pdf';
    }
}
