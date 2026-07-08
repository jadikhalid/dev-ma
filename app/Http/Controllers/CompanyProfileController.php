<?php

namespace App\Http\Controllers;

use App\Services\CompanyLogoService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    private const SECTIONS = ['identity', 'presentation', 'hiring', 'contact'];

    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private CompanyLogoService $logos,
    ) {}

    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isCompany()) {
            return redirect()->route('dashboard');
        }

        $profile = $user->companyProfile ?: $user->companyProfile()->create();

        return view('company.profile', [
            'user' => $user,
            'profile' => $profile,
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'sectorSlug' => old('sector', $this->professionCatalog->sectorSlugFromLabel($profile->sector) ?? ''),
            'employeeCountOptions' => $this->employeeCountOptions(),
            'europeanCities' => ['Paris', 'Lyon', 'Bordeaux', 'Nantes', 'Bruxelles', 'Genève', 'Madrid', 'Berlin', 'Amsterdam'],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isCompany()) {
            return redirect()->route('dashboard');
        }

        $section = $request->validate([
            'section' => ['required', 'string', Rule::in(self::SECTIONS)],
        ])['section'];

        $data = $request->validate($this->rulesForSection($section));
        $payload = $this->payloadForSection($section, $data);

        $profile = $user->companyProfile()->firstOrCreate(['user_id' => $user->id]);

        if ($section === 'identity') {
            if ($request->boolean('remove_logo')) {
                $this->logos->delete($profile);
            }

            if ($request->hasFile('logo')) {
                $this->logos->store($profile, $request->file('logo'));
            }
        }

        $profile->update($payload);

        return redirect()
            ->route('company.profile.edit')
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
                'company_name' => ['required', 'string', 'max:255'],
                'sector' => [
                    'required',
                    'string',
                    'max:64',
                    Rule::exists('profession_sectors', 'slug')->where(fn ($query) => $query->where('is_active', true)),
                ],
                'employee_count' => ['nullable', 'string', Rule::in(array_keys($this->employeeCountOptions()))],
                'country' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'website' => ['nullable', 'url', 'max:255'],
                'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'remove_logo' => ['nullable', 'boolean'],
            ],
            'presentation' => [
                'description' => ['required', 'string', 'min:50', 'max:5000'],
            ],
            'hiring' => [
                'hiring_needs' => ['required', 'string', 'min:20', 'max:5000'],
            ],
            'contact' => [
                'representative_name' => ['required', 'string', 'min:2', 'max:255'],
                'representative_email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'linkedin_url' => ['nullable', 'url', 'max:255'],
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
                'company_name' => $data['company_name'],
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
            'contact' => [
                'representative_name' => $data['representative_name'],
                'representative_email' => $data['representative_email'],
                'phone' => $data['phone'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
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
