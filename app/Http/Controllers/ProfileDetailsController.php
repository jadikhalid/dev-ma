<?php

namespace App\Http\Controllers;

use App\Services\ProfessionCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileDetailsController extends Controller
{
    public function __construct(private ProfessionCatalogService $professionCatalog) {}

    public function edit()
    {
        $user = Auth::user();

        if (! $user->isTalent()) {
            return redirect()->route('dashboard');
        }

        $profile = $user->profile ?: $user->profile()->create();
        $profile->load(['professionSector', 'profession', 'documents']);

        $slugs = $this->professionCatalog->slugsFromProfile(
            $profile->profession_sector_id,
            $profile->profession_id,
        );

        return view('talent.profile', [
            'user' => $user,
            'profile' => $profile,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'sectorSlug' => old('sector', $slugs['sector']),
            'professionSlug' => old('profession', $slugs['profession']),
            'specialization' => old('specialization', $profile->specialization ?? ''),
            'cities' => ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir', 'Fès', 'Meknès', 'Oujda'],
            'workModeOptions' => $this->workModeOptions(),
            'languageOptions' => $this->languageOptions(),
            'educationOptions' => $this->educationOptions(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (! $user->isTalent()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'sector' => ['required', 'string', 'max:64'],
            'profession' => ['required', 'string', 'max:64'],
            'specialization' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:30', 'max:5000'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:50'],
            'education_level' => ['required', 'string', Rule::in(array_keys($this->educationOptions()))],
            'certifications' => ['nullable', 'string', 'max:2000'],
            'daily_rate_eur' => ['required', 'integer', 'min:10', 'max:5000'],
            'availability' => ['required', 'string', Rule::in(['disponible', 'sous 2 semaines', 'mission en cours'])],
            'work_modes' => ['required', 'array', 'min:1'],
            'work_modes.*' => ['string', Rule::in(array_keys($this->workModeOptions()))],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['string', Rule::in(array_keys($this->languageOptions()))],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'skills' => ['nullable', 'string', 'max:500'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $professionData = $this->professionCatalog->resolveSelection(
            $data['sector'],
            $data['profession'],
            $data['specialization'],
        );

        if (! empty($data['skills'])) {
            $data['skills'] = array_values(array_filter(array_map('trim', explode(',', $data['skills']))));
        } else {
            $data['skills'] = [];
        }

        $payload = array_merge(
            collect($data)->except(['sector', 'profession'])->all(),
            $professionData,
        );

        $user->profile()->updateOrCreate(['user_id' => $user->id], $payload);

        return redirect()->route('profile.details.edit')->with('status', 'profile-updated');
    }

    private function workModeOptions(): array
    {
        return [
            'remote' => __('talenma.talent.work_mode_remote'),
            'visa_sponsorship' => __('talenma.talent.work_mode_visa'),
            'local' => __('talenma.talent.work_mode_local'),
        ];
    }

    private function languageOptions(): array
    {
        return [
            'fr' => __('talenma.talent.lang_fr'),
            'en' => __('talenma.talent.lang_en'),
            'ar' => __('talenma.talent.lang_ar'),
            'es' => __('talenma.talent.lang_es'),
            'de' => __('talenma.talent.lang_de'),
        ];
    }

    private function educationOptions(): array
    {
        return [
            'bac+2' => __('talenma.talent.education_bac2'),
            'bac+3' => __('talenma.talent.education_bac3'),
            'bac+5' => __('talenma.talent.education_bac5'),
            'doctorate' => __('talenma.talent.education_doctorate'),
            'other' => __('talenma.talent.education_other'),
        ];
    }
}
