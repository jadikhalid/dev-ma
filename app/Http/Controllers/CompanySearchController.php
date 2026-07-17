<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use App\Models\Profile;
use App\Models\ProfileDocument;
use App\Models\User;
use App\Services\CompanyProfileCompletionService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanySearchController extends Controller
{
    public function __construct(
        private CompanyProfileCompletionService $profileCompletion,
        private ProfessionCatalogService $professionCatalog,
    ) {}

    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        $completion = $this->profileCompletion->assess($request->user()->companyProfile);

        if (! $completion['is_catalog_ready']) {
            return redirect()
                ->route('dashboard')
                ->with('toast_error', __('talenma.dashboard.company.profile_incomplete'));
        }

        $talents = $this->filteredTalentsQuery($request)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'talents' => $talents->getCollection()->map(fn (User $talent) => $this->presentTalent($talent))->values(),
                'meta' => [
                    'total' => $talents->total(),
                    'current_page' => $talents->currentPage(),
                    'last_page' => $talents->lastPage(),
                    'per_page' => $talents->perPage(),
                    'from' => $talents->firstItem(),
                    'to' => $talents->lastItem(),
                ],
            ]);
        }

        $sectors = $this->professionCatalog->sectorsForLocale();

        return view('company.search', [
            'talents' => $talents,
            'sectors' => $sectors,
            'filters' => [
                'sector' => (string) $request->input('sector', ''),
                'profession' => (string) $request->input('profession', ''),
                'experience' => (string) $request->input('experience', 'all'),
                'status' => (string) $request->input('status', 'all'),
                'keyword' => (string) $request->input('keyword', ''),
            ],
        ]);
    }

    public function show(Request $request, User $talent)
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        $completion = $this->profileCompletion->assess($request->user()->companyProfile);

        if (! $completion['is_catalog_ready']) {
            return redirect()
                ->route('dashboard')
                ->with('toast_error', __('talenma.dashboard.company.profile_incomplete'));
        }

        if ($talent->role !== 'dev' || $talent->approval_status !== User::APPROVAL_APPROVED) {
            abort(404);
        }

        $talent->load(['profile.profession', 'profile.professionSector', 'profile.documents']);

        if ($request->wantsJson()) {
            return response()->json($this->presentTalentProfile($talent));
        }

        return view('company.talent-show', compact('talent'));
    }

    public function showCv(Request $request, User $talent): StreamedResponse
    {
        $user = $request->user();

        abort_unless($user && $user->isCompany() && $user->isApproved(), 403);

        if ($talent->role !== 'dev' || $talent->approval_status !== User::APPROVAL_APPROVED) {
            abort(404);
        }

        $talent->loadMissing('profile.documents');

        $cv = $talent->profile?->cvDocument();

        abort_unless($cv instanceof ProfileDocument, 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($cv->path), 404);

        return $disk->response(
            $cv->path,
            $cv->original_name,
            [
                'Content-Type' => $cv->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$cv->original_name.'"',
            ],
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<User>
     */
    private function filteredTalentsQuery(Request $request)
    {
        $query = User::where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->with(['profile.profession', 'profile.professionSector'])
            ->whereHas('profile', fn ($q) => $q->whereNotNull('profession_id')->whereNotNull('bio'));

        if ($request->filled('sector')) {
            $query->whereHas('profile.professionSector', fn ($q) => $q->where('slug', $request->sector));
        }

        if ($request->filled('profession')) {
            $profession = Profession::query()
                ->where('slug', $request->profession)
                ->where('is_active', true)
                ->first();

            if ($profession) {
                $query->whereHas('profile', fn ($q) => $q->where('profession_id', $profession->id));
            }
        }

        $experience = (string) $request->input('experience', 'all');

        if ($experience !== '' && $experience !== 'all') {
            $query->whereHas('profile', function ($q) use ($experience) {
                match ($experience) {
                    '0-1' => $q->whereBetween('experience_years', [0, 1]),
                    '1-5' => $q->where('experience_years', '>', 1)->where('experience_years', '<=', 5),
                    '5-10' => $q->where('experience_years', '>', 5)->where('experience_years', '<=', 10),
                    '10+' => $q->where('experience_years', '>', 10),
                    default => null,
                };
            });
        }

        $status = (string) $request->input('status', 'all');

        if ($status !== '' && $status !== 'all' && array_key_exists($status, Profile::statusOptions())) {
            $query->whereHas('profile', fn ($q) => $q->where('availability', $status));
        }

        if ($request->filled('keyword')) {
            $keywords = array_values(array_filter(array_map(
                fn (string $keyword) => trim($keyword),
                explode(',', (string) $request->keyword),
            )));

            foreach ($keywords as $keyword) {
                $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $keyword);

                $query->whereHas('profile', function ($q) use ($escaped) {
                    $q->where(function ($subQ) use ($escaped) {
                        $subQ->where('specialization', 'like', '%'.$escaped.'%')
                            ->orWhere('bio', 'like', '%'.$escaped.'%')
                            ->orWhere('skills', 'like', '%'.$escaped.'%');
                    });
                });
            }
        }

        return $query;
    }

    /**
     * @return array<string, mixed>
     */
    private function presentTalent(User $talent): array
    {
        $profile = $talent->profile;
        $experienceYears = $profile?->experience_years;

        return [
            'id' => $talent->id,
            'name' => $talent->name,
            'avatar_url' => $talent->avatarUrl(),
            'initials' => $talent->initials(),
            'profession_label' => $profile?->professionLabel(),
            'sector_label' => $profile?->sectorLabel(),
            'specialization' => $profile?->specialization,
            'city' => $profile?->city,
            'country' => $profile?->country,
            'skills' => $profile?->skills ?? [],
            'experience_years' => $experienceYears,
            'experience_label' => $experienceYears !== null
                ? __('talenma.talents.experience', ['years' => $experienceYears])
                : null,
            'profile_url' => route('company.talent.show', $talent),
            'recruitment_url' => route('recruitment.create', $talent).'?mode=intermediary',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentTalentProfile(User $talent): array
    {
        $profile = $talent->profile;
        $keywords = collect(explode(',', (string) $profile?->specialization))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->merge(is_array($profile?->skills) ? $profile->skills : [])
            ->unique()
            ->values();
        $cv = $profile?->cvDocument();

        return [
            'name' => $talent->name,
            'avatar_url' => $talent->avatarUrl(),
            'initials' => $talent->initials(),
            'profession_label' => $profile?->professionLabel(),
            'sector_label' => $profile?->sectorLabel(),
            'city' => $profile?->city,
            'country' => $profile?->country,
            'experience_label' => $profile?->experience_years !== null
                ? __('talenma.talents.experience', ['years' => $profile->experience_years])
                : null,
            'availability_label' => $profile?->statusLabel(),
            'availability_tone' => $profile?->statusTone(),
            'keywords' => $keywords,
            'work_modes' => $profile?->workModeLabels() ?? [],
            'languages' => $profile?->languageLabels() ?? [],
            'bio' => $profile?->bio,
            'education_label' => $profile?->education_level
                ? __('talenma.talent.education_'.$profile->education_level)
                : null,
            'certifications' => $profile?->certifications,
            'linkedin_url' => $profile?->linkedin_url,
            'github_url' => $profile?->github_url,
            'portfolio_url' => $profile?->portfolio_url,
            'cv_url' => $cv ? route('company.talent.cv', $talent) : null,
            'talent_id' => $talent->id,
            'compose_url' => route('inbox.store'),
        ];
    }
}
