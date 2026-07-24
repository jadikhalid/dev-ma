<?php

namespace App\Http\Controllers;

use App\Models\ProfessionSector;
use App\Models\RecruitmentRequest;
use App\Models\User;
use App\Services\MessagingService;
use App\Services\ProfessionCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecruitmentRequestController extends Controller
{
    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private MessagingService $messaging,
    ) {}

    public function create(Request $request, ?User $talent = null): View|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        return view('recruitment.create', [
            'talent' => $talent?->load('profile'),
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'developer_user_id' => ['nullable', 'exists:users,id'],
            'role_title' => ['required', 'string', 'min:5', 'max:120'],
            'need' => ['required', 'string', 'min:50', 'max:5000'],
            'sector' => [
                'nullable',
                'string',
                'max:64',
                'exists:profession_sectors,slug',
            ],
        ], [
            'role_title.required' => __('talenma.recruitment.role_title_required'),
            'role_title.min' => __('talenma.recruitment.role_title_min'),
            'role_title.max' => __('talenma.recruitment.role_title_max'),
            'need.required' => __('talenma.recruitment.need_required'),
            'need.min' => __('talenma.recruitment.need_min'),
            'need.max' => __('talenma.recruitment.need_max'),
            'sector.exists' => __('talenma.recruitment.sector_invalid'),
        ]);

        $user = $request->user();
        $sector = null;

        if (filled($data['sector'] ?? null)) {
            $sector = ProfessionSector::query()
                ->where('slug', $data['sector'])
                ->where('is_active', true)
                ->first();
        }

        $sectorLabel = $sector?->localizedName(app()->getLocale());

        $recruitment = RecruitmentRequest::create([
            'company_user_id' => $user->id,
            'developer_user_id' => $data['developer_user_id'] ?? null,
            'profession_sector_id' => $sector?->id,
            'mode' => 'intermediary',
            'subject' => $data['role_title'],
            'message' => $data['need'],
            'status' => 'pending',
        ]);

        $recruitment->load('talent');

        $inboxBody = $this->formatStaffMessage(
            companyName: $user->isCompanyOwner() ? $user->name : ($user->companyOrganization()?->displayName() ?: $user->name),
            roleTitle: $data['role_title'],
            need: $data['need'],
            sectorLabel: $sectorLabel,
            talentName: $recruitment->talent?->name,
        );

        $conversation = $this->messaging->startStaffConversation(
            $user,
            __('talenma.recruitment.inbox_subject', ['title' => $data['role_title']]),
            $inboxBody,
        );

        $message = __('talenma.recruitment.sent');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'show_url' => route('inbox.show', $conversation),
            ]);
        }

        return redirect()
            ->route('inbox.show', $conversation)
            ->with('toast_success', $message);
    }

    private function formatStaffMessage(
        string $companyName,
        string $roleTitle,
        string $need,
        ?string $sectorLabel,
        ?string $talentName,
    ): string {
        $lines = [
            __('talenma.recruitment.inbox_intro'),
            '',
            __('talenma.recruitment.inbox_company', ['name' => $companyName]),
            __('talenma.recruitment.inbox_role', ['title' => $roleTitle]),
        ];

        if (filled($sectorLabel)) {
            $lines[] = __('talenma.recruitment.inbox_sector', ['sector' => $sectorLabel]);
        }

        if (filled($talentName)) {
            $lines[] = __('talenma.recruitment.inbox_talent', ['name' => $talentName]);
        }

        $lines[] = '';
        $lines[] = __('talenma.recruitment.inbox_need_label');
        $lines[] = $need;

        return implode("\n", $lines);
    }
}
