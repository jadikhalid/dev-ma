@php
    $isSystem = (bool) ($msg->is_system ?? false);
@endphp

@if ($isSystem)
    <div class="flex justify-center min-w-0" data-chat-message-id="{{ $msg->id }}">
        <div class="max-w-[min(100%,28rem)] rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-sm text-amber-950 shadow-sm min-w-0 break-words">
            <p class="text-[11px] font-semibold text-amber-700 mb-1">{{ __('talenma.direct_hire.chat_system_label') }}</p>
            <p class="whitespace-pre-line leading-relaxed break-words">{{ $msg->body }}</p>
            <p class="mt-1.5 text-[10px] text-amber-700/70">{{ $msg->created_at?->translatedFormat('d M Y H:i') }}</p>
        </div>
    </div>
@else
    @php
        $mine = (int) $msg->sender_user_id === (int) $viewer->id;
        $senderLabel = $mine
            ? __('talenma.direct_hire.chat_you')
            : ($msg->sender?->isCompany()
                ? $directHire->companyDisplayName()
                : ($msg->sender?->name ?? ''));
    @endphp
    <div @class(['flex min-w-0', 'justify-end' => $mine, 'justify-start' => ! $mine]) data-chat-message-id="{{ $msg->id }}">
        <div @class([
            'max-w-[min(100%,24rem)] sm:max-w-[85%] rounded-2xl px-3.5 py-2.5 text-sm shadow-sm min-w-0 break-words',
            'bg-indigo-600 text-white rounded-br-md' => $mine,
            'bg-white border border-gray-200 text-gray-800 rounded-bl-md' => ! $mine,
        ])>
            <p @class([
                'text-[11px] font-semibold mb-1 break-words',
                'text-indigo-100' => $mine,
                'text-gray-500' => ! $mine,
            ])>{{ $senderLabel }}</p>
            <p class="whitespace-pre-line leading-relaxed break-words">{{ $msg->body }}</p>
            <p @class([
                'mt-1.5 text-[10px]',
                'text-indigo-200' => $mine,
                'text-gray-400' => ! $mine,
            ])>{{ $msg->created_at?->translatedFormat('d M Y H:i') }}</p>
        </div>
    </div>
@endif
