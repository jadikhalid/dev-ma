<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Services\MessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InboxController extends Controller
{
    public function __construct(private MessagingService $messaging) {}

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCompany() && ! $user->isTalent() && ! $user->canAccessStaffMessaging()) {
            return redirect()->route('dashboard');
        }

        $this->messaging->markAllReadFor($user);

        $conversations = $this->messaging->conversationsFor($user)
            ->map(fn (Conversation $conversation) => $this->messaging->presentConversationSummary($conversation, $user));

        return view('inbox.index', [
            'conversations' => $conversations,
            'conversation' => null,
            'unreadCount' => 0,
        ]);
    }

    public function show(Request $request, Conversation $conversation): View|JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCompany() && ! $user->isTalent() && ! $user->canAccessStaffMessaging()) {
            return redirect()->route('dashboard');
        }

        $this->messaging->assertCanAccess($user, $conversation);
        $conversation->markReadFor($user);

        $payload = $this->messaging->presentConversation($conversation->fresh(), $user);

        if ($request->wantsJson()) {
            return response()->json([
                ...$payload,
                'unread_count' => $this->messaging->unreadCountFor($user),
            ]);
        }

        $conversations = $this->messaging->conversationsFor($user)
            ->map(fn (Conversation $item) => $this->messaging->presentConversationSummary($item, $user));

        return view('inbox.index', [
            'conversation' => $payload,
            'conversations' => $conversations,
            'unreadCount' => $this->messaging->unreadCountFor($user),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->isCompany(), 403);

        $data = $request->validate([
            'talent_id' => ['required', 'integer', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:20', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:'.MessagingService::MAX_ATTACHMENTS],
            'attachments.*' => [
                'file',
                'max:1024',
                'mimetypes:'.implode(',', MessagingService::ALLOWED_ATTACHMENT_MIMES),
            ],
        ]);

        $talent = User::query()->findOrFail($data['talent_id']);
        $files = array_values($request->file('attachments', []) ?: []);

        $conversation = $this->messaging->startConversation(
            $user,
            $talent,
            $data['subject'],
            $data['body'],
            $files,
        );

        if ($request->wantsJson()) {
            return response()->json([
                'conversation' => $this->messaging->presentConversation($conversation, $user),
                'message' => __('talenma.inbox.sent'),
                'show_url' => route('inbox.show', $conversation),
            ], 201);
        }

        return redirect()
            ->route('inbox.show', $conversation)
            ->with('toast_success', __('talenma.inbox.sent'));
    }

    public function searchTalents(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user?->isCompany() && $user->isApproved(), 403);

        $data = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $term = trim($data['q']);
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        $results = User::query()
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->whereHas('profile', function ($query) {
                $query->whereNotNull('profession_id')
                    ->whereNotNull('bio')
                    ->where('bio', '!=', '');
            })
            ->with('profile.profession')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like);
            })
            ->orderBy('name')
            ->limit(12)
            ->get()
            ->map(fn (User $talent) => [
                'id' => $talent->id,
                'label' => $talent->profile?->visibleDisplayName($talent) ?? $talent->publicDisplayName(),
                'subtitle' => $talent->profile?->professionLabel(),
            ])
            ->values()
            ->all();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function storeMessage(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCompany() && ! $user->isTalent() && ! $user->canAccessStaffMessaging()) {
            abort(403);
        }

        $this->messaging->assertCanAccess($user, $conversation);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:'.MessagingService::MAX_ATTACHMENTS],
            'attachments.*' => [
                'file',
                'max:1024',
                'mimetypes:'.implode(',', MessagingService::ALLOWED_ATTACHMENT_MIMES),
            ],
        ]);

        $files = array_values($request->file('attachments', []) ?: []);
        $message = $this->messaging->reply($conversation, $user, $data['body'], $files);
        $conversation->markReadFor($user);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $this->messaging->presentMessage($message, $user),
                'conversation' => $this->messaging->presentConversation($conversation->fresh(), $user),
            ], 201);
        }

        return redirect()
            ->route('inbox.show', $conversation)
            ->with('toast_success', __('talenma.inbox.reply_sent'));
    }

    public function destroy(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user?->isCompany() || $user?->isTalent(), 403);

        $this->messaging->hideForViewer($user, $conversation);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('talenma.inbox.delete_success'),
                'unread_count' => $this->messaging->unreadCountFor($user),
            ]);
        }

        return redirect()
            ->route('inbox.index')
            ->with('toast_success', __('talenma.inbox.delete_success'));
    }

    public function showAttachment(Request $request, MessageAttachment $attachment): StreamedResponse
    {
        $user = $request->user();
        $attachment->loadMissing('message.conversation');
        $conversation = $attachment->message?->conversation;

        abort_unless($conversation instanceof Conversation, 404);
        $this->messaging->assertCanAccess($user, $conversation);

        $disk = Storage::disk($attachment->disk ?: 'local');

        abort_unless($disk->exists($attachment->path), 404);

        return $disk->response(
            $attachment->path,
            $attachment->original_name,
            [
                'Content-Type' => $attachment->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$attachment->original_name.'"',
            ],
        );
    }
}
