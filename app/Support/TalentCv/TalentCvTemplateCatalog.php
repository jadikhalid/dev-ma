<?php

namespace App\Support\TalentCv;

use App\Models\TalentCvDraft;

class TalentCvTemplateCatalog
{
    /** @return list<string> */
    public static function templateKeys(): array
    {
        return [
            TalentCvDraft::TEMPLATE_CLASSIC,
            TalentCvDraft::TEMPLATE_MODERN,
            TalentCvDraft::TEMPLATE_EXECUTIVE,
        ];
    }

    public static function isValidTemplate(string $template): bool
    {
        return in_array($template, self::templateKeys(), true);
    }

    public static function normalizeTemplate(string $template): string
    {
        return self::isValidTemplate($template)
            ? $template
            : TalentCvDraft::TEMPLATE_CLASSIC;
    }

    /** @return array<string, string> */
    public static function templateLabels(): array
    {
        return [
            TalentCvDraft::TEMPLATE_CLASSIC => __('talenma.cv_builder.templates.classic'),
            TalentCvDraft::TEMPLATE_MODERN => __('talenma.cv_builder.templates.modern'),
            TalentCvDraft::TEMPLATE_EXECUTIVE => __('talenma.cv_builder.templates.executive'),
        ];
    }

    public static function viewName(string $template): string
    {
        return 'talent.cv-builder.templates.'.self::normalizeTemplate($template);
    }
}
