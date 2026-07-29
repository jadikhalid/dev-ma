@php
    $mine = (int) $msg->sender_user_id === (int) $viewer->id;
    $senderLabel = $mine
        ? __('talenma.recruitment.chat_you')
        : ($msg->sender?->isStaff()
            ? __('talenma.recruitment.chat_peer_team')
            : ($msg->sender?->isCompany()
                ? $recruitment->companyDisplayName()
                : ($msg->sender?->name ?? __('talenma.recruitment.party_deleted'))));
@endphp
<div
    @class([
        'flex min-w-0',
        'justify-end' => $mine,
        'justify-start' => ! $mine,
    ])
>
    <div @class([
        'max-w-[min(100%,20rem)] sm:max-w-[78%] rounded-2xl px-3.5 py-2.5 text-sm shadow-sm min-w-0 break-words',
        'bg-indigo-600 text-white rounded-br-md' => $mine,
        'bg-white border border-slate-200/90 text-slate-800 rounded-bl-md' => ! $mine,
    ])>
        <p @class([
            'text-[11px] font-semibold mb-1 break-words',
            'text-indigo-100' => $mine,
            'text-slate-500' => ! $mine,
        ])>{{ $senderLabel }}</p>
        <p class="whitespace-pre-line leading-relaxed break-words text-[13px] sm:text-sm">{{ $msg->body }}</p>
        <p @class([
            'mt-1.5 text-[10px] font-medium tabular-nums',
            'text-indigo-200' => $mine,
            'text-slate-400' => ! $mine,
        ])>{{ $msg->created_at?->translatedFormat('d M Y H:i') }}</p>
    </div>
</div>
