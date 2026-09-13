<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\Newsletter;
use App\Models\SocialPost;
use App\Models\User;
use App\Services\NewsletterDeliveryService;
use App\Services\NewsletterRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function __construct(
        private NewsletterDeliveryService $delivery,
        private NewsletterRenderer $renderer,
    ) {}

    public function index(): View
    {
        $newsletters = Newsletter::query()
            ->with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.newsletter.index', [
            'newsletters' => $newsletters,
            'recipientCount' => $this->delivery->recipientCount(),
        ]);
    }

    public function create(): View
    {
        return view('admin.newsletter.form', [
            'newsletter' => new Newsletter([
                'locale' => app()->getLocale() === 'en' ? Newsletter::LOCALE_EN : Newsletter::LOCALE_FR,
                'status' => Newsletter::STATUS_DRAFT,
                'body_blocks' => [
                    [
                        'type' => Newsletter::BLOCK_HEADER,
                        'title' => '',
                        'subtitle' => '',
                    ],
                ],
            ]),
            'recipientCount' => $this->delivery->recipientCount(),
            'picker' => $this->pickerPayload(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $newsletter = Newsletter::query()->create([
            ...$data,
            'status' => Newsletter::STATUS_DRAFT,
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('toast_success', __('talenma.newsletter.saved'));
    }

    public function edit(Newsletter $newsletter): View|RedirectResponse
    {
        if (! $newsletter->isEditable()) {
            return redirect()
                ->route('admin.newsletter.show', $newsletter)
                ->with('toast_error', __('talenma.newsletter.not_editable'));
        }

        return view('admin.newsletter.form', [
            'newsletter' => $newsletter,
            'recipientCount' => $this->delivery->recipientCount(),
            'picker' => $this->pickerPayload(),
        ]);
    }

    public function update(Request $request, Newsletter $newsletter): RedirectResponse
    {
        if (! $newsletter->isEditable()) {
            return back()->with('toast_error', __('talenma.newsletter.not_editable'));
        }

        $newsletter->update([
            ...$this->validated($request),
            'status' => $newsletter->status === Newsletter::STATUS_SCHEDULED
                ? Newsletter::STATUS_DRAFT
                : ($newsletter->status === Newsletter::STATUS_CANCELLED
                    ? Newsletter::STATUS_DRAFT
                    : $newsletter->status),
            'scheduled_at' => null,
        ]);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('toast_success', __('talenma.newsletter.saved'));
    }

    public function show(Newsletter $newsletter): View
    {
        return view('admin.newsletter.show', [
            'newsletter' => $newsletter,
            'previewHtml' => $this->renderer->renderHtml($newsletter),
            'recipientCount' => $this->delivery->recipientCount(),
            'deliveryProgress' => $this->delivery->progress($newsletter),
        ]);
    }

    public function preview(Request $request, Newsletter $newsletter): View
    {
        if ($request->filled('body_blocks') || $request->filled('subject')) {
            $data = $this->validated($request, forPreview: true);
            $newsletter->fill($data);
        }

        return view('admin.newsletter.preview', [
            'newsletter' => $newsletter,
            'previewHtml' => $this->renderer->renderHtml($newsletter),
        ]);
    }

    public function send(Request $request, Newsletter $newsletter): RedirectResponse
    {
        if (in_array($newsletter->status, [Newsletter::STATUS_SENT, Newsletter::STATUS_SENDING], true)) {
            return back()->with('toast_error', __('talenma.newsletter.already_sent'));
        }

        if ($newsletter->normalizedBlocks() === []) {
            return back()->with('toast_error', __('talenma.newsletter.blocks_required'));
        }

        $count = $this->delivery->sendNow($newsletter->fresh());

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('toast_success', __('talenma.newsletter.sending_started', ['count' => $count]));
    }

    public function schedule(Request $request, Newsletter $newsletter): RedirectResponse
    {
        if (in_array($newsletter->status, [Newsletter::STATUS_SENT, Newsletter::STATUS_SENDING], true)) {
            return back()->with('toast_error', __('talenma.newsletter.already_sent'));
        }

        if ($newsletter->normalizedBlocks() === []) {
            return back()->with('toast_error', __('talenma.newsletter.blocks_required'));
        }

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $this->delivery->schedule($newsletter, new \DateTimeImmutable($data['scheduled_at']));

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('toast_success', __('talenma.newsletter.scheduled'));
    }

    public function cancel(Newsletter $newsletter): RedirectResponse
    {
        $this->delivery->cancelSchedule($newsletter);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('toast_success', __('talenma.newsletter.cancelled'));
    }

    public function destroy(Newsletter $newsletter): RedirectResponse
    {
        if (in_array($newsletter->status, [Newsletter::STATUS_SENDING, Newsletter::STATUS_SENT], true)) {
            return back()->with('toast_error', __('talenma.newsletter.cannot_delete_sent'));
        }

        $newsletter->delete();

        return redirect()
            ->route('admin.newsletter.index')
            ->with('toast_success', __('talenma.newsletter.deleted'));
    }

    public function searchJobs(Request $request): JsonResponse
    {
        $q = trim($request->string('q')->toString());

        $jobs = JobPosting::query()
            ->with(['companyProfile.user', 'professionSector'])
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhere('external_company_name', 'like', '%'.$q.'%');
                });
            })
            ->latest('published_at')
            ->limit(20)
            ->get()
            ->map(fn (JobPosting $job) => [
                'id' => $job->id,
                'label' => $job->title.' — '.$job->advertiserName(),
            ]);

        return response()->json(['items' => $jobs]);
    }

    public function searchBlog(Request $request): JsonResponse
    {
        $q = trim($request->string('q')->toString());

        $posts = BlogPost::query()
            ->published()
            ->when($q !== '', fn ($query) => $query->where('title', 'like', '%'.$q.'%'))
            ->latest('published_at')
            ->limit(20)
            ->get(['id', 'title'])
            ->map(fn (BlogPost $post) => [
                'id' => $post->id,
                'label' => $post->title,
            ]);

        return response()->json(['items' => $posts]);
    }

    public function searchTalents(Request $request): JsonResponse
    {
        $q = trim($request->string('q')->toString());

        $talents = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', '%'.$q.'%')
                        ->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                });
            })
            ->latest('approved_at')
            ->limit(20)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'label' => $user->formalDisplayName(),
            ]);

        return response()->json(['items' => $talents]);
    }

    public function searchCompanies(Request $request): JsonResponse
    {
        $q = trim($request->string('q')->toString());

        $companies = CompanyProfile::query()
            ->with('user')
            ->whereHas('user', fn ($user) => $user
                ->where('role', 'company')
                ->where('approval_status', User::APPROVAL_APPROVED))
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('representative_name', 'like', '%'.$q.'%')
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$q.'%'));
                });
            })
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(fn (CompanyProfile $profile) => [
                'id' => $profile->id,
                'label' => $profile->displayName(),
            ]);

        return response()->json(['items' => $companies]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $forPreview = false): array
    {
        $data = $request->validate([
            'title' => [$forPreview ? 'nullable' : 'required', 'string', 'max:255'],
            'subject' => [$forPreview ? 'nullable' : 'required', 'string', 'max:255'],
            'locale' => ['required', 'string', Rule::in([Newsletter::LOCALE_FR, Newsletter::LOCALE_EN])],
            'body_blocks' => ['nullable'],
        ]);

        $blocks = $data['body_blocks'] ?? [];
        if (is_string($blocks)) {
            $decoded = json_decode($blocks, true);
            $blocks = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($blocks)) {
            $blocks = [];
        }

        $data['body_blocks'] = array_values(array_filter(
            $blocks,
            fn ($block) => is_array($block) && in_array($block['type'] ?? null, Newsletter::BLOCK_TYPES, true)
        ));

        if (! $forPreview) {
            $data['title'] = $data['title'] ?? '';
            $data['subject'] = $data['subject'] ?? '';
        }

        return $data;
    }

    /**
     * @return array{jobs: list<array{id:int,label:string}>, blog: list<array{id:int,label:string}>, social: list<array{id:int,label:string}>, talents: list<array{id:int,label:string}>, companies: list<array{id:int,label:string}>}
     */
    private function pickerPayload(): array
    {
        return [
            'jobs' => JobPosting::query()
                ->with(['companyProfile.user'])
                ->where('status', JobPosting::STATUS_PUBLISHED)
                ->latest('published_at')
                ->limit(30)
                ->get()
                ->map(fn (JobPosting $job) => [
                    'id' => $job->id,
                    'label' => $job->title.' — '.$job->advertiserName(),
                ])->values()->all(),
            'blog' => BlogPost::query()
                ->published()
                ->latest('published_at')
                ->limit(30)
                ->get(['id', 'title'])
                ->map(fn (BlogPost $post) => [
                    'id' => $post->id,
                    'label' => $post->title,
                ])->values()->all(),
            'social' => SocialPost::query()
                ->latest('id')
                ->limit(30)
                ->get(['id', 'title'])
                ->map(fn (SocialPost $post) => [
                    'id' => $post->id,
                    'label' => $post->title,
                ])->values()->all(),
            'talents' => User::query()
                ->where('role', 'dev')
                ->where('approval_status', User::APPROVAL_APPROVED)
                ->latest('approved_at')
                ->limit(30)
                ->get()
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'label' => $user->formalDisplayName(),
                ])->values()->all(),
            'companies' => CompanyProfile::query()
                ->with('user')
                ->whereHas('user', fn ($user) => $user
                    ->where('role', 'company')
                    ->where('approval_status', User::APPROVAL_APPROVED))
                ->latest('id')
                ->limit(30)
                ->get()
                ->map(fn (CompanyProfile $profile) => [
                    'id' => $profile->id,
                    'label' => $profile->displayName(),
                ])->values()->all(),
        ];
    }
}
