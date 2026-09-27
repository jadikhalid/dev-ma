<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\TalentCv\TalentCvMarketingPreview;
use App\Support\TalentCv\TalentCvTemplateCatalog;
use Illuminate\View\View;

class CvTemplatePromoController extends Controller
{
    public function __invoke(): View
    {
        $labels = TalentCvTemplateCatalog::templateLabels();

        $templates = collect(TalentCvTemplateCatalog::templateKeys())
            ->map(fn (string $key) => [
                'key' => $key,
                'label' => $labels[$key],
                'preview_url' => TalentCvMarketingPreview::imagePath($key),
                'promo_url' => route('promo.cv-template', $key),
                'share_title' => __('talenma.promo.cv_template.title', ['template' => $labels[$key]]),
            ]);

        return view('admin.cv-templates', [
            'templates' => $templates,
        ]);
    }
}
