<?php

namespace App\Http\Controllers;

use App\Services\CompanyBannerService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompanyProfileController extends Controller
{
    private const SECTIONS = ['banner', 'identity', 'presentation', 'hiring'];

    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private CompanyBannerService $banners,
    ) {}

    public function edit(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->canManageCompanyProfile()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('profile.edit', ['panel' => 'company']);
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if (! $user->canManageCompanyProfile()) {
            return redirect()->route('dashboard');
        }

        if ($request->input('section') === 'banner') {
            return $this->bannerForbiddenResponse($request);
        }

        $profile = $user->companyProfile()->firstOrCreate(['user_id' => $user->id]);

        return $this->updateForProfile($request, $profile);
    }

    public function updateForProfile(Request $request, \App\Models\CompanyProfile $profile, ?string $redirectRoute = null): RedirectResponse|JsonResponse
    {
        $section = $request->validate([
            'section' => ['required', 'string', Rule::in(self::SECTIONS)],
        ])['section'];

        if ($section === 'banner' && ! Auth::user()?->isStaff()) {
            return $this->bannerForbiddenResponse($request);
        }

        $data = $request->validate($this->rulesForSection($section), [
            'banner.image' => __('talenma.company.banner_invalid_type'),
            'banner.mimes' => __('talenma.company.banner_invalid_type'),
            'banner.max' => __('talenma.company.banner_too_large'),
        ]);

        if ($section === 'banner') {
            if ($request->hasFile('banner')) {
                $this->banners->store($profile, $request->file('banner'));
            } elseif ($request->boolean('remove_banner')) {
                $this->banners->delete($profile);
            }
        } else {
            $profile->update($this->payloadForSection($section, $data));
        }

        $message = __('talenma.company.section_updated.'.$section);

        if ($request->wantsJson()) {
            $extra = $section === 'banner' ? ['reload' => true] : [];

            if ($section === 'identity') {
                $profile->refresh();

                $extra = [
                    'sector_label' => $profile->sector ?: '—',
                    'location_label' => collect([$profile->city, $profile->countryLabel()])->filter()->implode(', '),
                    'reload' => $request->boolean('from_dashboard'),
                ];
            }

            return response()->json(array_merge(['message' => $message], $extra));
        }

        if ($redirectRoute) {
            return redirect()
                ->to($redirectRoute)
                ->with('status', 'company-profile-updated')
                ->with('updated_section', $section)
                ->with('toast_success', $message);
        }

        return redirect()
            ->route('profile.edit', ['panel' => 'company'])
            ->with('status', 'company-profile-updated')
            ->with('updated_section', $section);
    }

    /**
     * @return array{
     *     profile: \App\Models\CompanyProfile,
     *     memberships: \Illuminate\Database\Eloquent\Collection,
     *     professionSectors: \Illuminate\Support\Collection,
     *     sectorSlug: string,
     *     employeeCountOptions: array<string, string>,
     *     countryOptions: array<string, string>,
     *     citiesByCountry: array<string, list<string>>
     * }
     */
    public function panelData(\App\Models\CompanyProfile $profile): array
    {
        $profile->loadMissing(['memberships.user']);

        return [
            'profile' => $profile,
            'memberships' => $profile->memberships,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'sectorSlug' => old('sector', $this->professionCatalog->sectorSlugFromLabel($profile->sector) ?? ''),
            'employeeCountOptions' => $this->employeeCountOptions(),
            'countryOptions' => \App\Models\CompanyProfile::countryOptions(),
            'citiesByCountry' => \App\Models\CompanyProfile::citiesByCountry(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rulesForSection(string $section): array
    {
        return match ($section) {
            'banner' => [
                'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.(CompanyBannerService::MAX_BYTES / 1024)],
                'remove_banner' => ['nullable', 'boolean'],
            ],
            'identity' => [
                'sector' => [
                    'required',
                    'string',
                    'max:64',
                    Rule::exists('profession_sectors', 'slug')->where(fn ($query) => $query->where('is_active', true)),
                ],
                'employee_count' => ['nullable', 'string', Rule::in(array_keys($this->employeeCountOptions()))],
                'country' => ['required', 'string', Rule::in(\App\Models\CompanyProfile::COUNTRY_CODES)],
                'city' => [
                    'required',
                    'string',
                    'max:100',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        $country = request()->input('country');
                        $allowed = \App\Models\CompanyProfile::citiesForCountry(is_string($country) ? $country : null);

                        if ($allowed === [] || ! in_array($value, $allowed, true)) {
                            $fail(__('talenma.company.city_invalid'));
                        }
                    },
                ],
                'website' => ['nullable', 'url', 'max:255'],
            ],
            'presentation' => [
                'description' => ['required', 'string', 'min:50', 'max:5000'],
            ],
            'hiring' => [
                'hiring_needs' => ['required', 'string', 'min:20', 'max:5000'],
            ],
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadForSection(string $section, array $data): array
    {
        return match ($section) {
            'identity' => [
                'sector' => $this->professionCatalog->sectorLabelFromSlug($data['sector']) ?? $data['sector'],
                'employee_count' => $data['employee_count'] ?? null,
                'country' => $data['country'],
                'city' => $data['city'],
                'website' => $data['website'] ?? null,
            ],
            'presentation' => [
                'description' => $data['description'],
            ],
            'hiring' => [
                'hiring_needs' => $data['hiring_needs'],
            ],
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private function employeeCountOptions(): array
    {
        return [
            '1-10' => '1-10',
            '11-50' => '11-50',
            '51-200' => '51-200',
            '200+' => '200+',
        ];
    }

    private function bannerForbiddenResponse(Request $request): RedirectResponse|JsonResponse
    {
        $message = __('talenma.company.banner_staff_only');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()
            ->back()
            ->with('toast_error', $message);
    }
}
