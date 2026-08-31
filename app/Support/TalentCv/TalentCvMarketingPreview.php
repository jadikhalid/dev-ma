<?php

namespace App\Support\TalentCv;

use App\Models\TalentCvDraft;

class TalentCvMarketingPreview
{
    public static function imagePath(string $template = TalentCvDraft::TEMPLATE_MODERN, ?string $locale = null): string
    {
        $template = TalentCvTemplateCatalog::normalizeTemplate($template);
        $locale = ($locale ?? app()->getLocale()) === 'en' ? 'en' : 'fr';

        $filename = match ($template) {
            TalentCvDraft::TEMPLATE_CLASSIC => "marketing-preview-classic-{$locale}.png",
            TalentCvDraft::TEMPLATE_MODERN => "marketing-preview-modern-{$locale}.png",
            default => "marketing-preview-executive-{$locale}.png",
        };

        return asset('images/cv-builder/'.$filename);
    }

    /** @return array{width: int, height: int} */
    public static function imageDimensions(string $template = TalentCvDraft::TEMPLATE_MODERN, ?string $locale = null): array
    {
        $template = TalentCvTemplateCatalog::normalizeTemplate($template);
        $locale = ($locale ?? app()->getLocale()) === 'en' ? 'en' : 'fr';

        $filename = match ($template) {
            TalentCvDraft::TEMPLATE_CLASSIC => "marketing-preview-classic-{$locale}.png",
            TalentCvDraft::TEMPLATE_MODERN => "marketing-preview-modern-{$locale}.png",
            default => "marketing-preview-executive-{$locale}.png",
        };

        $path = public_path('images/cv-builder/'.$filename);

        if (! is_file($path)) {
            return ['width' => 820, 'height' => 792];
        }

        $size = getimagesize($path);

        return [
            'width' => (int) ($size[0] ?? 820),
            'height' => (int) ($size[1] ?? 792),
        ];
    }
}
