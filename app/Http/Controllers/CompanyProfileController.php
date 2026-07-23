<?php

namespace App\Http\Controllers;

use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompanyProfileController extends Controller
{
    private const SECTIONS = ['identity', 'presentation', 'hiring'];

    public function __construct(
        private ProfessionCatalogService $professionCatalog,
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

        $section = $request->validate([
            'section' => ['required', 'string', Rule::in(self::SECTIONS)],
        ])['section'];

        $data = $request->validate($this->rulesForSection($section));
        $payload = $this->payloadForSection($section, $data);

        $profile = $user->companyProfile()->firstOrCreate(['user_id' => $user->id]);

        $profile->update($payload);

        $message = __('talenma.company.section_updated.'.$section);

        if ($request->wantsJson()) {
            $extra = [];

            if ($section === 'identity') {
                $profile->refresh();

                $extra = [
                    'sector_label' => $profile->sector ?: '—',
                    'location_label' => collect([$profile->city, $profile->countryLabel()])->filter()->implode(', '),
                ];
            }

            return response()->json(array_merge(['message' => $message], $extra));
        }

        return redirect()
            ->route('profile.edit', ['panel' => 'company'])
            ->with('status', 'company-profile-updated')
            ->with('updated_section', $section);
    }

    /**
     * @return array<string, mixed>
     */
    private function rulesForSection(string $section): array
    {
        return match ($section) {
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
}
