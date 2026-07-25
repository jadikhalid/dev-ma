@php
    $viewer = Auth::user();
    $canChat = ! $directHire->isTerminal();
    $sidebar = $sidebar ?? false;
    $storeRoute = $viewer->isTalent()
        ? route('talent.direct-hire.messages.store', $directHire)
        : route('company.direct-hire.messages.store', $directHire);
@endphp

<section
    id="direct-hire-chat"
    @class([
        'bg-white rounded-2xl border scroll-mt-24 flex flex-col min-w-0',
        'lg:sticky lg:top-24 lg:max-h-[calc(100dvh-7.5rem)] overflow-hidden' => $sidebar,
        'overflow-hidden' => ! $sidebar,
    ])
>
    <div class="px-4 sm:px-5 py-4 border-b border-gray-100 shrink-0">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.chat_title') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.direct_hire.chat_subtitle') }}</p>
    </div>

    <div
        id="direct-hire-chat-messages"
        @class([
        'overflow-y-auto overflow-x-hidden px-4 sm:px-5 py-4 space-y-3 bg-slate-50/60 min-w-0',
        'max-h-[min(24rem,50dvh)]' => ! $sidebar,
        'flex-1 min-h-0' => $sidebar,
    ])>
        @forelse ($directHire->messages as $msg)
            @include('direct-hire._chat-message', [
                'msg' => $msg,
                'directHire' => $directHire,
                'viewer' => $viewer,
            ])
        @empty
            <p id="direct-hire-chat-empty" class="text-sm text-gray-500 text-center py-6">{{ __('talenma.direct_hire.chat_empty') }}</p>
        @endforelse
    </div>

    @if ($canChat)
        <form method="POST" action="{{ $storeRoute }}" class="border-t border-gray-100 p-3 sm:p-4 space-y-3 shrink-0 min-w-0">
            @csrf
            <textarea
                name="body"
                rows="3"
                required
                minlength="2"
                maxlength="5000"
                class="block w-full max-w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="{{ __('talenma.direct_hire.chat_placeholder') }}"
            >{{ old('body') }}</textarea>
            <x-input-error :messages="$errors->get('body')" />
            <div class="flex justify-end">
                <x-primary-button>{{ __('talenma.direct_hire.chat_send') }}</x-primary-button>
            </div>
        </form>
    @else
        <div class="border-t border-gray-100 px-4 sm:px-5 py-3 shrink-0">
            <p class="text-xs text-gray-500">{{ __('talenma.direct_hire.chat_closed') }}</p>
        </div>
    @endif
</section>
