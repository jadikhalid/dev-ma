<?php

namespace App\Services;

use App\Mail\NewInboxMessageMail;
use App\Models\Conversation;
use App\Models\DirectHireRequest;
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
            $excludedIds = $this->directHireConversationIdsFor($user);

            $query->where('company_user_id', $user->id)
                ->whereNull('company_hidden_at')
                ->when($excludedIds !== [], fn ($q) => $q->whereNotIn('id', $excludedIds));
        } elseif ($user->canAccessStaffMessaging()) {
            $adminIds = User::query()->where('role', 'admin')->pluck('id');

            $query->where(function ($q) use ($adminIds) {
                $q->where('channel', Conversation::CHANNEL_STAFF);

                if ($adminIds->isNotEmpty()) {
                    $q->orWhere(function ($outreach) use ($adminIds) {
                        $outreach->where('channel', Conversation::CHANNEL_TALENT)
                            ->whereIn('company_user_id', $adminIds);
                    });
                }
            });
        } elseif ($user->isTalent()) {
            $excludedIds = $this->directHireConversationIdsFor($user);

            $query->where('channel', Conversation::CHANNEL_TALENT)
                ->where('talent_user_id', $user->id)
                ->whereNull('talent_hidden_at')
                ->when($excludedIds !== [], fn ($q) => $q->whereNotIn('id', $excludedIds));
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
                ->whereNull('company_hidden_at')
                ->whereNotIn('id', $this->directHireConversationIdsFor($user))
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('company_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'company_last_read_at');
                })
                ->count();
        }

        if ($user->canAccessStaffMessaging()) {
            $adminIds = User::query()->where('role', 'admin')->pluck('id');

            return Conversation::query()
                ->where(function ($q) use ($adminIds) {
                    $q->where('channel', Conversation::CHANNEL_STAFF);

                    if ($adminIds->isNotEmpty()) {
                        $q->orWhere(function ($outreach) use ($adminIds) {
                            $outreach->where('channel', Conversation::CHANNEL_TALENT)
                                ->whereIn('company_user_id', $adminIds);
                        });
                    }
                })
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->where(function ($staff) {
                        $staff->where('channel', Conversation::CHANNEL_STAFF)
                            ->where(function ($read) {
                                $read->whereNull('talent_last_read_at')
                                    ->orWhereColumn('last_message_at', '>', 'talent_last_read_at');
                            });
                    })->orWhere(function ($outreach) {
                        $outreach->where('channel', Conversation::CHANNEL_TALENT)
                            ->where(function ($read) {
                                $read->whereNull('company_last_read_at')
                                    ->orWhereColumn('last_message_at', '>', 'company_last_read_at');
                            });
                    });
                })
                ->count();
        }

        if ($user->isTalent()) {
            return Conversation::query()
                ->where('channel', Conversation::CHANNEL_TALENT)
                ->where('talent_user_id', $user->id)
                ->whereNull('talent_hidden_at')
                ->whereNotIn('id', $this->directHireConversationIdsFor($user))
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('talent_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'talent_last_read_at');
                })
                ->count();
        }

        return 0;
    }

    /**
     * Mark all visible inbox conversations as read for the current viewer.
     */
    public function markAllReadFor(User $user): void
    {
        $now = now();

        if ($user->isCompany()) {
            Conversation::query()
                ->where('company_user_id', $user->id)
                ->whereNull('company_hidden_at')
                ->whereNotIn('id', $this->directHireConversationIdsFor($user))
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('company_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'company_last_read_at');
                })
                ->update(['company_last_read_at' => $now]);

            return;
        }

        if ($user->canAccessStaffMessaging()) {
            $adminIds = User::query()->where('role', 'admin')->pluck('id');

            Conversation::query()
                ->where('channel', Conversation::CHANNEL_STAFF)
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('talent_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'talent_last_read_at');
                })
                ->update(['talent_last_read_at' => $now]);

            if ($adminIds->isNotEmpty()) {
                Conversation::query()
                    ->where('channel', Conversation::CHANNEL_TALENT)
                    ->whereIn('company_user_id', $adminIds)
                    ->whereNotNull('last_message_at')
                    ->where(function ($q) {
                        $q->whereNull('company_last_read_at')
                            ->orWhereColumn('last_message_at', '>', 'company_last_read_at');
                    })
                    ->update(['company_last_read_at' => $now]);
            }

            return;
        }

        if ($user->isTalent()) {
            Conversation::query()
                ->where('channel', Conversation::CHANNEL_TALENT)
                ->where('talent_user_id', $user->id)
                ->whereNull('talent_hidden_at')
                ->whereNotIn('id', $this->directHireConversationIdsFor($user))
                ->whereNotNull('last_message_at')
                ->where(function ($q) {
                    $q->whereNull('talent_last_read_at')
                        ->orWhereColumn('last_message_at', '>', 'talent_last_read_at');
                })
                ->update(['talent_last_read_at' => $now]);
        }
    }

    /**
     * @return list<int>
     */
    public function directHireConversationIdsFor(User $user): array
    {
        $query = DirectHireRequest::query()->whereNotNull('conversation_id');

        if ($user->isTalent()) {
            $query->where('talent_user_id', $user->id);
        } elseif ($user->isCompany()) {
            $org = $user->companyOrganization();
            $query->where('hire_origin', DirectHireRequest::ORIGIN_COMPANY)
                ->where(function ($q) use ($user, $org) {
                    $q->where('company_user_id', $user->id);
                    if ($org) {
                        $q->orWhere('company_profile_id', $org->id);
                    }
                });
        } else {
            return [];
        }

        return $query->pluck('conversation_id')->unique()->filter()->values()->all();
    }

    /**
     * Label for activity feeds: "Person de Company" when a company user is the sender.
     */
    public function companySenderActivityLabel(User $sender): string
    {
        if (! $sender->isCompany()) {
            return $sender->name;
        }

        $company = $sender->companyOrganization()?->displayName();

        if (! filled($company)) {
            $company = $sender->companyDisplayName();
        }

        $person = $sender->companyMailPersonName();

        if ($person !== '' && strcasecmp($person, (string) $company) !== 0) {
            return __('talenma.mail.inbox_message.sender_with_company', [
                'person' => $person,
                'company' => $company,
            ]);
        }

        return (string) $company;
    }

    public function assertCanAccess(User $user, Conversation $conversation): void
    {
        abort_unless($conversation->isParticipant($user), 403);

        if ($user->isCompany() && $conversation->company_hidden_at) {
            abort(404);
        }

        if ($user->isTalent() && $conversation->talent_hidden_at) {
            abort(404);
        }

        // Direct-hire threads are no longer part of the general inbox.
        $linked = DirectHireRequest::query()
            ->where('conversation_id', $conversation->id)
            ->exists();

        abort_if($linked, 404);
    }

    /**
     * Masque définitivement le fil côté entreprise (le talent conserve l’échange).
     */
    public function hideForCompany(User $company, Conversation $conversation): void
    {
        abort_unless($company->isCompany() && $company->isApproved(), 403);
        abort_unless($conversation->isCompanySideParticipant($company), 403);

        if ($conversation->company_hidden_at) {
            return;
        }

        $conversation->forceFill(['company_hidden_at' => now()])->save();
    }

    /**
     * Masque définitivement le fil côté talent (l’entreprise conserve l’échange).
     */
    public function hideForTalent(User $talent, Conversation $conversation): void
    {
        abort_unless($talent->isTalent() && $talent->isApproved(), 403);
        abort_unless(
            $conversation->channel === Conversation::CHANNEL_TALENT
            && (int) $conversation->talent_user_id === (int) $talent->id,
            403,
        );

        if ($conversation->talent_hidden_at) {
            return;
        }

        $conversation->forceFill(['talent_hidden_at' => now()])->save();
    }

    /**
     * Masque le fil pour le viewer courant (entreprise ou talent).
     */
    public function hideForViewer(User $viewer, Conversation $conversation): void
    {
        if ($viewer->isCompany()) {
            $this->hideForCompany($viewer, $conversation);

            return;
        }

        if ($viewer->isTalent()) {
            $this->hideForTalent($viewer, $conversation);

            return;
        }

        abort(403);
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
            // Chaque nouvel envoi (nouvel objet) ouvre un fil distinct.
            // Pour poursuivre un échange existant, répondre dans le fil (colonne gauche).
            $conversation = Conversation::query()->create([
                'company_user_id' => $company->id,
                'talent_user_id' => $talent->id,
                'channel' => Conversation::CHANNEL_TALENT,
                'subject' => $subject,
            ]);

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
     * First inbox touch for a company: staff welcome thread + email with short usage tips.
     * Idempotent via users.inbox_welcome_sent_at.
     */
    public function ensureCompanyInboxWelcome(User $company): ?Conversation
    {
        if (! $company->isCompany() || ! $company->isApproved() || $company->inbox_welcome_sent_at) {
            return null;
        }

        try {
            $admin = $this->resolveAdminRecipient();
        } catch (ValidationException) {
            return null;
        }

        if ((int) $admin->id === (int) $company->id) {
            return null;
        }

        try {
            return DB::transaction(function () use ($company, $admin) {
                $locked = User::query()->whereKey($company->id)->lockForUpdate()->first();

                if (! $locked || $locked->inbox_welcome_sent_at) {
                    return null;
                }

                $conversation = Conversation::query()->firstOrCreate(
                    [
                        'company_user_id' => $company->id,
                        'talent_user_id' => $admin->id,
                        'channel' => Conversation::CHANNEL_STAFF,
                    ],
                    [
                        'subject' => __('talenma.inbox.welcome_subject'),
                    ],
                );

                if ($conversation->wasRecentlyCreated || blank($conversation->subject)) {
                    $conversation->forceFill([
                        'subject' => __('talenma.inbox.welcome_subject'),
                    ])->save();
                }

                $alreadyWelcomed = $conversation->messages()
                    ->where('sender_user_id', $admin->id)
                    ->exists();

                if (! $alreadyWelcomed) {
                    // Welcome stays in-app only — never notify by email.
                    $this->postMessage(
                        $conversation,
                        $admin,
                        __('talenma.inbox.welcome_body'),
                        [],
                        notify: false,
                    );
                }

                $locked->forceFill(['inbox_welcome_sent_at' => now()])->save();

                return $conversation->fresh(['messages.attachments', 'talent', 'company']);
            });
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * First inbox touch for a talent: platform welcome thread (no email).
     * Idempotent via users.inbox_welcome_sent_at.
     */
    public function ensureTalentInboxWelcome(User $talent): ?Conversation
    {
        if (! $talent->isTalent() || ! $talent->isApproved() || $talent->inbox_welcome_sent_at) {
            return null;
        }

        try {
            $admin = $this->resolveAdminRecipient();
        } catch (ValidationException) {
            return null;
        }

        if ((int) $admin->id === (int) $talent->id) {
            return null;
        }

        try {
            return DB::transaction(function () use ($talent, $admin) {
                $locked = User::query()->whereKey($talent->id)->lockForUpdate()->first();

                if (! $locked || $locked->inbox_welcome_sent_at) {
                    return null;
                }

                $conversation = Conversation::query()->firstOrCreate(
                    [
                        'company_user_id' => $admin->id,
                        'talent_user_id' => $talent->id,
                        'channel' => Conversation::CHANNEL_TALENT,
                    ],
                    [
                        'subject' => __('talenma.inbox.welcome_subject'),
                    ],
                );

                if ($conversation->wasRecentlyCreated || blank($conversation->subject)) {
                    $conversation->forceFill([
                        'subject' => __('talenma.inbox.welcome_subject'),
                    ])->save();
                }

                $alreadyWelcomed = $conversation->messages()
                    ->where('sender_user_id', $admin->id)
                    ->exists();

                if (! $alreadyWelcomed) {
                    $this->postMessage(
                        $conversation,
                        $admin,
                        __('talenma.inbox.welcome_body_talent'),
                        [],
                        notify: false,
                    );
                }

                $locked->forceFill(['inbox_welcome_sent_at' => now()])->save();

                return $conversation->fresh(['messages.attachments', 'talent', 'company']);
            });
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
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
        } elseif ($sender->canAccessStaffMessaging()) {
            abort_unless(
                $conversation->isStaffChannel() || $conversation->isPlatformTalentOutreach(),
                403,
            );
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
                    : (
                        ($conversation->isStaffChannel() && $viewer->isCompany())
                        || ($conversation->isPlatformTalentOutreach() && $viewer->isTalent())
                            ? __('talenma.inbox.staff_role_label')
                            : null
                    ),
                ...$this->counterpartVisual($conversation, $viewer, $counterpart),
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
                ...$this->counterpartVisual($conversation, $viewer, $counterpart),
            ],
            'show_url' => route('inbox.show', $conversation),
            'destroy_url' => ($viewer->isCompany() || (
                $viewer->isTalent() && $conversation->channel === Conversation::CHANNEL_TALENT
            ))
                ? route('inbox.destroy', $conversation)
                : null,
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

        if ($viewer->isTalent() && ($conversation->isPlatformTalentOutreach() || $counterpart?->isAdmin())) {
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

    /**
     * @return array{avatar_url: ?string, initials: string}
     */
    private function counterpartVisual(Conversation $conversation, User $viewer, ?User $counterpart): array
    {
        if ($conversation->isStaffChannel() || ($viewer->isTalent() && $conversation->isPlatformTalentOutreach())) {
            return [
                'avatar_url' => null,
                'initials' => 'TM',
            ];
        }

        if ($viewer->isTalent() && $counterpart?->isCompany()) {
            $org = $counterpart->companyOrganization() ?? $counterpart->companyProfile;

            return [
                'avatar_url' => $org?->logoUrl() ?? $counterpart->avatarUrl(),
                'initials' => $org?->initials() ?? $counterpart->initials(),
            ];
        }

        return [
            'avatar_url' => $counterpart?->avatarUrl(),
            'initials' => $counterpart?->initials() ?? '?',
        ];
    }

    private function assertCompanyCanMessage(User $company): void
    {
        abort_unless($company->canAccessTalentPool(), 403);
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
        bool $notify = true,
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
        $readColumn = $conversation->isCompanySideParticipant($sender)
            ? 'company_last_read_at'
            : 'talent_last_read_at';

        $conversation->forceFill([
            'last_message_at' => $now,
            $readColumn => $now,
        ])->save();

        if ($notify) {
            $this->notifyRecipient($conversation, $message, $sender);
        }

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
        $recipient = $conversation->isCompanySideParticipant($sender)
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
