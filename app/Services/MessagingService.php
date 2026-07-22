<?php

namespace App\Services;

use App\Mail\NewInboxMessageMail;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class MessagingService
{
    public const MAX_ATTACHMENTS = 3;

    public const MAX_ATTACHMENT_BYTES = 1024 * 1024;

    /** @var list<string> */
    public const ALLOWED_ATTACHMENT_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function __construct(
        private CompanyProfileCompletionService $companyCompletion,
    ) {}

    /**
     * @return Collection<int, Conversation>
     */
    public function conversationsFor(User $user): Collection
    {
        $query = Conversation::query()
            ->with([
                'company.companyProfile',
                'talent.profile',
                'latestMessage.sender',
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        if ($user->isCompany()) {
            $query->where('company_user_id', $user->id);
        } elseif ($user->isTalent()) {
            $query->where('channel', Conversation::CHANNEL_TALENT)
                ->where('talent_user_id', $user->id);
        } elseif ($user->isStaff()) {
            $query->where('channel', Conversation::CHANNEL_STAFF);
        } else {
            return collect();
        }

        return $query->get();
    }

    public function unreadCountFor(User $user): int
    {
        if ($user->isCompany()) {
            return Conversation::query()
                ->where('company_user_id', $user->id)
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('company_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'company_last_read_at');
                })
                ->count();
        }

        if ($user->isTalent()) {
            return Conversation::query()
                ->where('channel', Conversation::CHANNEL_TALENT)
                ->where('talent_user_id', $user->id)
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('talent_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'talent_last_read_at');
                })
                ->count();
        }

        if ($user->isStaff()) {
            return Conversation::query()
                ->where('channel', Conversation::CHANNEL_STAFF)
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('talent_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'talent_last_read_at');
                })
                ->count();
        }

        return 0;
    }

    public function assertCanAccess(User $user, Conversation $conversation): void
    {
        abort_unless($conversation->isParticipant($user), 403);
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    public function startConversation(
        User $company,
        User $talent,
        string $subject,
        string $body,
        array $files = [],
    ): Conversation {
        $this->assertCompanyCanMessage($company);
        $this->assertTalentIsContactable($talent);

        return DB::transaction(function () use ($company, $talent, $subject, $body, $files) {
            $conversation = Conversation::query()->firstOrCreate(
                [
                    'company_user_id' => $company->id,
                    'talent_user_id' => $talent->id,
                    'channel' => Conversation::CHANNEL_TALENT,
                ],
                [
                    'subject' => $subject,
                ],
            );

            if (blank($conversation->subject)) {
                $conversation->forceFill(['subject' => $subject])->save();
            }

            $this->postMessage($conversation, $company, $body, $files);

            return $conversation->fresh(['messages.attachments', 'talent', 'company']);
        });
    }

    /**
     * Company → admin InMail (implantation / accompagnement).
     *
     * @param  list<UploadedFile>  $files
     */
    public function startStaffConversation(
        User $company,
        string $subject,
        string $body,
        array $files = [],
    ): Conversation {
        abort_unless($company->isCompany() && $company->isApproved(), 403);

        $admin = $this->resolveAdminRecipient();

        return DB::transaction(function () use ($company, $admin, $subject, $body, $files) {
            $conversation = Conversation::query()->firstOrCreate(
                [
                    'company_user_id' => $company->id,
                    'talent_user_id' => $admin->id,
                    'channel' => Conversation::CHANNEL_STAFF,
                ],
                [
                    'subject' => $subject,
                ],
            );

            if (blank($conversation->subject) || $conversation->wasRecentlyCreated) {
                $conversation->forceFill(['subject' => $subject])->save();
            }

            $this->postMessage($conversation, $company, $body, $files);

            return $conversation->fresh(['messages.attachments', 'talent', 'company']);
        });
    }

    public function resolveAdminRecipient(): User
    {
        $admin = User::query()
            ->where('role', 'admin')
            ->orderBy('id')
            ->first();

        if (! $admin) {
            throw ValidationException::withMessages([
                'body' => __('talenma.services.accompagnement_no_admin'),
            ]);
        }

        return $admin;
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    public function reply(
        Conversation $conversation,
        User $sender,
        string $body,
        array $files = [],
    ): Message {
        $this->assertCanAccess($sender, $conversation);

        if ($sender->isCompany()) {
            if ($conversation->isStaffChannel()) {
                abort_unless($sender->isApproved(), 403);
            } else {
                $this->assertCompanyCanMessage($sender);
            }
        } elseif ($sender->isStaff()) {
            abort_unless($conversation->isStaffChannel(), 403);
        }

        return $this->postMessage($conversation, $sender, $body, $files);
    }

    /**
     * @return array<string, mixed>
     */
    public function presentConversation(Conversation $conversation, User $viewer): array
    {
        $conversation->loadMissing([
            'company.companyProfile',
            'talent.profile',
            'messages.sender',
            'messages.attachments',
        ]);

        $counterpart = $conversation->counterpartFor($viewer);
        $counterpartName = $this->counterpartDisplayName($conversation, $viewer, $counterpart);

        return [
            'id' => $conversation->id,
            'subject' => $conversation->subject,
            'channel' => $conversation->channel,
            'unread' => $conversation->unreadFor($viewer),
            'last_message_at' => optional($conversation->last_message_at)?->toIso8601String(),
            'counterpart' => [
                'id' => $counterpart?->id,
                'name' => $counterpartName,
                'role_label' => $viewer->isCompany() && ! $conversation->isStaffChannel()
                    ? collect([
                        $counterpart?->profile?->professionLabel(),
                        $counterpart?->profile?->sectorLabel(),
                    ])->filter()->implode(' - ')
                    : ($conversation->isStaffChannel() && $viewer->isCompany()
                        ? __('talenma.inbox.staff_role_label')
                        : null),
            ],
            'messages' => $conversation->messages->map(fn (Message $message) => $this->presentMessage($message, $viewer))->values(),
            'show_url' => route('inbox.show', $conversation),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function presentConversationSummary(Conversation $conversation, User $viewer): array
    {
        $conversation->loadMissing([
            'company.companyProfile',
            'talent.profile',
            'latestMessage',
        ]);

        $counterpart = $conversation->counterpartFor($viewer);
        $latest = $conversation->latestMessage;

        return [
            'id' => $conversation->id,
            'subject' => $conversation->subject,
            'channel' => $conversation->channel,
            'unread' => $conversation->unreadFor($viewer),
            'last_message_at' => optional($conversation->last_message_at)?->toIso8601String(),
            'last_message_preview' => $latest
                ? \Illuminate\Support\Str::limit($latest->body, 100)
                : null,
            'counterpart' => [
                'id' => $counterpart?->id,
                'name' => $this->counterpartDisplayName($conversation, $viewer, $counterpart),
            ],
            'show_url' => route('inbox.show', $conversation),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function presentMessage(Message $message, User $viewer): array
    {
        $message->loadMissing(['sender', 'attachments']);

        return [
            'id' => $message->id,
            'body' => $message->body,
            'created_at' => $message->created_at?->toIso8601String(),
            'created_at_human' => $message->created_at?->diffForHumans(),
            'is_mine' => (int) $message->sender_user_id === (int) $viewer->id,
            'sender_name' => $message->sender?->name,
            'attachments' => $message->attachments->map(fn (MessageAttachment $attachment) => [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'size_label' => $attachment->formattedSize(),
                'url' => route('inbox.attachments.show', $attachment),
            ])->values(),
        ];
    }

    private function counterpartDisplayName(Conversation $conversation, User $viewer, ?User $counterpart): string
    {
        if ($conversation->isStaffChannel() && $viewer->isCompany()) {
            return __('talenma.inbox.staff_counterpart');
        }

        if ($viewer->isCompany()) {
            return $counterpart?->profile?->visibleDisplayName($counterpart)
                ?? $counterpart?->publicDisplayName()
                ?? __('talenma.inbox.unknown_counterpart');
        }

        return $counterpart?->name
            ?: __('talenma.inbox.unknown_counterpart');
    }

    private function assertCompanyCanMessage(User $company): void
    {
        abort_unless($company->isCompany() && $company->isApproved(), 403);

        $ready = $this->companyCompletion->assess($company->companyProfile)['is_catalog_ready'] ?? false;

        abort_unless($ready, 403, __('talenma.dashboard.company.profile_incomplete'));
    }

    private function assertTalentIsContactable(User $talent): void
    {
        abort_unless(
            $talent->isTalent()
            && $talent->approval_status === User::APPROVAL_APPROVED,
            404,
        );

        $talent->loadMissing('profile');

        abort_unless(
            $talent->profile
            && $talent->profile->profession_id
            && filled($talent->profile->bio),
            404,
        );
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    private function postMessage(
        Conversation $conversation,
        User $sender,
        string $body,
        array $files = [],
    ): Message {
        $this->validateAttachments($files);

        $message = $conversation->messages()->create([
            'sender_user_id' => $sender->id,
            'body' => $body,
        ]);

        foreach ($files as $file) {
            $path = $file->store(
                'message-attachments/'.$conversation->id.'/'.$message->id,
                'local',
            );

            $message->attachments()->create([
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize() ?: 0,
            ]);
        }

        $now = now();
        $readColumn = (int) $conversation->company_user_id === (int) $sender->id
            ? 'company_last_read_at'
            : 'talent_last_read_at';

        $conversation->forceFill([
            'last_message_at' => $now,
            $readColumn => $now,
        ])->save();

        $this->notifyRecipient($conversation, $message, $sender);

        return $message->load('attachments', 'sender');
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    private function validateAttachments(array $files): void
    {
        if (count($files) > self::MAX_ATTACHMENTS) {
            throw ValidationException::withMessages([
                'attachments' => __('talenma.inbox.attachments_max', ['max' => self::MAX_ATTACHMENTS]),
            ]);
        }

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            if ($file->getSize() > self::MAX_ATTACHMENT_BYTES) {
                throw ValidationException::withMessages([
                    'attachments' => __('talenma.inbox.attachment_too_large'),
                ]);
            }

            $mime = $file->getMimeType() ?: '';

            if (! in_array($mime, self::ALLOWED_ATTACHMENT_MIMES, true)) {
                throw ValidationException::withMessages([
                    'attachments' => __('talenma.inbox.attachment_invalid_type'),
                ]);
            }
        }
    }

    private function notifyRecipient(Conversation $conversation, Message $message, User $sender): void
    {
        $recipient = (int) $conversation->company_user_id === (int) $sender->id
            ? $conversation->talent
            : $conversation->company;

        if (! $recipient?->email) {
            return;
        }

        try {
            Mail::to($recipient->email)->send(new NewInboxMessageMail(
                recipient: $recipient,
                sender: $sender,
                conversation: $conversation,
                message: $message,
            ));
        } catch (\Throwable) {
            // Never block messaging on mail failures.
        }
    }
}
