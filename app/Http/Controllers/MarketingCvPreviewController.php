<?php

namespace App\Http\Controllers;

use App\Support\TalentCv\TalentCvDraftDefaults;
use App\Support\TalentCv\TalentCvPhotoResolver;
use App\Support\TalentCv\TalentCvTemplateCatalog;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MarketingCvPreviewController extends Controller
{
    public function show(string $template): View|Response
    {
        abort_unless(TalentCvTemplateCatalog::isValidTemplate($template), 404);

        $locale = request()->query('locale') === 'en' ? 'en' : (app()->getLocale() === 'en' ? 'en' : 'fr');
        $data = TalentCvDraftDefaults::sampleData($locale);
        $photoResolver = app(TalentCvPhotoResolver::class);

        return view(TalentCvTemplateCatalog::viewName($template), [
            'data' => $data,
            'locale' => $locale,
            'preview' => true,
            'user' => null,
            'photoSrc' => $photoResolver->resolve($data, null),
        ]);
    }
}
