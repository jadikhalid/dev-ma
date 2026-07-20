<?php

namespace App\Http\Controllers;

use App\Services\ProfileDocumentService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileDetailsController extends Controller
{
    private const SECTIONS = ['profession', 'presentation', 'availability', 'links', 'documents', 'certifications', 'visibility'];

    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private ProfileDocumentService $profileDocuments,
    ) {}

    public function edit(): View|RedirectResponse
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
            'cvDocuments' => $profile->cvDocuments(),
            'cvLanguageOptions' => ProfileDocumentService::cvLanguageOptions(),
            'registrationDocuments' => $profile->registrationDocuments(),
            'workModeOptions' => $this->workModeOptions(),
            'languageOptions' => $this->languageOptions(),
            'educationOptions' => $this->educationOptions(),
            'countryOptions' => \App\Models\Profile::countryOptions(),
            'citiesByCountry' => \App\Models\Profile::citiesByCountry(),
        ]);
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if (! $user->isTalent()) {
            return redirect()->route('dashboard');
        }

        $section = $request->validate([
            'section' => ['required', 'string', Rule::in(self::SECTIONS)],
        ])['section'];

        $profile = $user->profile ?: $user->profile()->create();

        if ($section === 'documents') {
            $this->updateDocuments($request, $profile);

            return $this->sectionResponse($request, $section);
        }

        if ($section === 'certifications') {
            $this->updateCertifications($request, $profile);

            return $this->sectionResponse($request, $section);
        }

        $messages = $section === 'links' ? $this->linksValidationMessages() : [];
        $data = $request->validate($this->rulesForSection($section), $messages);
        $payload = $this->payloadForSection($section, $data);

        $user->profile()->updateOrCreate(['user_id' => $user->id], $payload);

        if ($section === 'presentation') {
            $this->maybeStoreCertificationDocuments($request, $profile);
        }

        $extra = [];

        if ($section === 'profession') {
            $profile->refresh()->load(['profession', 'professionSector']);

            $extra = [
                'profession_label' => $profile->professionLabel() ?? '—',
                'sector_label' => $profile->sectorLabel() ?? '—',
            ];
        }

        return $this->sectionResponse($request, $section, $extra);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function sectionResponse(Request $request, string $section, array $extra = []): RedirectResponse|JsonResponse
    {
        $message = __('talenma.talent.section_updated.'.$section);

        if ($request->wantsJson()) {
            return response()->json(array_merge(['message' => $message], $extra));
        }

        return redirect()
            ->route('profile.details.edit')
            ->with('toast_success', $message);
    }

    private function updateDocuments(Request $request, $profile): void
    {
        $data = $request->validate([
            'cv_language' => ProfileDocumentService::cvLanguageRule(),
            'cv' => ['required', 'file', 'max:'.ProfileDocumentService::MAX_FILE_SIZE, 'mimes:pdf,jpg,jpeg,png,webp'],
        ], [
            'cv_language.required' => __('talenma.talent.cv_language_required'),
            'cv_language.in' => __('talenma.talent.cv_language_invalid'),
            'cv.required' => __('talenma.talent.cv_required'),
            'cv.max' => __('talenma.auth.validation.documents_size'),
            'cv.mimes' => __('talenma.auth.validation.documents_type'),
        ]);

        $this->profileDocuments->storeCv($profile, $request->file('cv'), $data['cv_language']);
    }

    private function updateCertifications(Request $request, $profile): void
    {
        $request->validate([
            'certification_documents' => ['required', 'array', 'min:1', 'max:'.ProfileDocumentService::MAX_REGISTRATION],
            'certification_documents.*' => ['file', 'max:'.ProfileDocumentService::MAX_FILE_SIZE, 'mimes:pdf,jpg,jpeg,png,webp'],
        ], [
            'certification_documents.required' => __('talenma.talent.certifications_docs_required'),
            'certification_documents.min' => __('talenma.talent.certifications_docs_required'),
            'certification_documents.max' => __('talenma.auth.validation.documents_max'),
            'certification_documents.*.max' => __('talenma.auth.validation.documents_size'),
            'certification_documents.*.mimes' => __('talenma.auth.validation.documents_type'),
        ]);

        $this->profileDocuments->storeRegistrationDocs($profile, $request->file('certification_documents'));
    }

    private function maybeStoreCertificationDocuments(Request $request, $profile): void
    {
        if (! $request->hasFile('certification_documents')) {
            return;
        }

        $request->validate([
            'certification_documents' => ['array', 'max:'.ProfileDocumentService::MAX_REGISTRATION],
            'certification_documents.*' => ['file', 'max:'.ProfileDocumentService::MAX_FILE_SIZE, 'mimes:pdf,jpg,jpeg,png,webp'],
        ], [
            'certification_documents.max' => __('talenma.auth.validation.documents_max'),
            'certification_documents.*.max' => __('talenma.auth.validation.documents_size'),
            'certification_documents.*.mimes' => __('talenma.auth.validation.documents_type'),
        ]);

        $this->profileDocuments->storeRegistrationDocs($profile, $request->file('certification_documents'));
    }

    /**
     * @return array<string, mixed>
     */
    private function rulesForSection(string $section): array
    {
        return match ($section) {
            'profession' => [
                'sector' => ['required', 'string', 'max:64'],
                'profession' => ['required', 'string', 'max:64'],
                'specialization' => ['required', 'string', 'max:500'],
            ],
            'presentation' => [
                'bio' => ['required', 'string', 'min:30', 'max:5000'],
                'experience_years' => ['required', 'integer', 'min:0', 'max:50'],
                'education_level' => ['required', 'string', Rule::in(array_keys($this->educationOptions()))],
                'languages' => ['required', 'array', 'min:1'],
                'languages.*' => ['string', Rule::in(array_keys($this->languageOptions()))],
            ],
            'availability' => [
                'availability' => ['required', 'string', Rule::in(array_keys(\App\Models\Profile::statusOptions()))],
                'work_modes' => ['required', 'array', 'min:1'],
                'work_modes.*' => ['string', Rule::in(array_keys($this->workModeOptions()))],
            ],
            'visibility' => [
                'is_public' => ['nullable', 'boolean'],
            ],
            'links' => [
                'country' => ['nullable', 'string', Rule::in(array_keys(\App\Models\Profile::countryOptions()))],
                'city' => [
                    'nullable',
                    'string',
                    'max:100',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        if (! filled($value)) {
                            return;
                        }

                        $country = request()->input('country');
                        $allowed = \App\Models\Profile::citiesForCountry(is_string($country) ? $country : null);

                        if ($allowed === [] || ! in_array($value, $allowed, true)) {
                            $fail(__('talenma.talent.city_invalid'));
                        }
                    },
                ],
                'phone' => ['nullable', 'string', 'max:30', $this->phoneNumberRule()],
                'whatsapp' => ['nullable', 'string', 'max:30', $this->phoneNumberRule()],
                'github_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/([a-z0-9-]+\.)*github\.com(\/|$)/i'],
                'linkedin_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/([a-z0-9-]+\.)*linkedin\.com(\/|$)/i'],
                'portfolio_url' => ['nullable', 'url', 'max:255'],
            ],
            default => [],
        };
    }

    private function phoneNumberRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! filled($value) || ! is_string($value)) {
                return;
            }

            $trimmed = trim($value);

            if (! preg_match('/^\+?[0-9\s().\/-]+$/', $trimmed)) {
                $fail(__('talenma.talent.'.($attribute === 'whatsapp' ? 'whatsapp_invalid' : 'phone_invalid')));

                return;
            }

            $digits = preg_replace('/\D+/', '', $trimmed) ?? '';

            if (strlen($digits) < 8 || strlen($digits) > 15) {
                $fail(__('talenma.talent.'.($attribute === 'whatsapp' ? 'whatsapp_invalid' : 'phone_invalid')));
            }
        };
    }

    /**
     * @return array<string, string>
     */
    private function linksValidationMessages(): array
    {
        return [
            'github_url.url' => __('talenma.talent.github_invalid'),
            'github_url.regex' => __('talenma.talent.github_host_invalid'),
            'linkedin_url.url' => __('talenma.talent.linkedin_invalid'),
            'linkedin_url.regex' => __('talenma.talent.linkedin_host_invalid'),
            'portfolio_url.url' => __('talenma.talent.portfolio_invalid'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadForSection(string $section, array $data): array
    {
        return match ($section) {
            'profession' => $this->professionCatalog->resolveSelection(
                $data['sector'],
                $data['profession'],
                $data['specialization'],
            ),
            'presentation' => [
                'bio' => $data['bio'],
                'experience_years' => $data['experience_years'],
                'education_level' => $data['education_level'],
                'languages' => $data['languages'],
            ],
            'availability' => [
                'availability' => $data['availability'],
                'work_modes' => $data['work_modes'],
            ],
            'visibility' => [
                'is_public' => (bool) ($data['is_public'] ?? false),
            ],
            'links' => [
                'country' => filled($data['country'] ?? null) ? $data['country'] : null,
                'city' => filled($data['city'] ?? null) ? $data['city'] : null,
                'phone' => $data['phone'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'github_url' => $data['github_url'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'portfolio_url' => $data['portfolio_url'] ?? null,
            ],
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private function workModeOptions(): array
    {
        return [
            'remote' => __('talenma.talent.work_mode_remote'),
            'visa_sponsorship' => __('talenma.talent.work_mode_visa'),
            'local' => __('talenma.talent.work_mode_local'),
        ];
    }

    /**
     * @return array<string, string>
     */
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

    /**
     * @return array<string, string>
     */
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
