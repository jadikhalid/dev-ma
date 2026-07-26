@php
    $viewer = Auth::user();
    $canChat = ! $directHire->isTerminal();
    $sidebar = $sidebar ?? false;
    $storeRoute = $viewer->isTalent()
        ? route('talent.direct-hire.messages.store', $directHire)
        : route('company.direct-hire.messages.store', $directHire);
    $peerName = $viewer->isTalent()
        ? $directHire->companyDisplayName()
        : $directHire->talentDisplayName();
@endphp

<section
    id="direct-hire-chat"
    x-data="directHireChat(@js(old('body', '')))"
    x-init="scrollToEnd()"
    class="bg-white rounded-2xl border border-indigo-100 shadow-sm shadow-indigo-600/5 scroll-mt-24 flex flex-col min-w-0 overflow-hidden {{ $sidebar ? 'lg:sticky lg:top-24 lg:h-[calc(100dvh-7.5rem)]' : '' }}"
>
    <header class="shrink-0 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 via-white to-slate-50 px-4 sm:px-5 py-3.5">
        <div class="flex items-center gap-3 min-w-0">
            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.direct_hire.chat_title') }}</h3>
                <p class="text-xs text-slate-500 truncate">{{ __('talenma.direct_hire.chat_with', ['name' => $peerName]) }}</p>
            </div>
            @if ($canChat)
                <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-emerald-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    {{ __('talenma.direct_hire.chat_open_badge') }}
                </span>
            @else
                <span class="shrink-0 inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-600 ring-1 ring-slate-200">
                    {{ __('talenma.direct_hire.chat_closed_badge') }}
                </span>
            @endif
        </div>
    </header>

    <div
        id="direct-hire-chat-messages"
        x-ref="messages"
        class="overflow-y-auto overflow-x-hidden px-3 sm:px-4 py-4 space-y-3 min-w-0 flex-1 min-h-0 bg-slate-50 {{ $sidebar ? '' : 'max-h-[min(22rem,45dvh)]' }}"
    >
        @forelse ($directHire->messages as $msg)
            @include('direct-hire._chat-message', [
                'msg' => $msg,
                'directHire' => $directHire,
                'viewer' => $viewer,
            ])
        @empty
            <div id="direct-hire-chat-empty" class="flex h-full min-h-[10rem] flex-col items-center justify-center px-4 text-center">
                <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-indigo-500 ring-1 ring-indigo-100 shadow-sm" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </span>
                <p class="text-sm font-medium text-slate-700">{{ __('talenma.direct_hire.chat_empty') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('talenma.direct_hire.chat_empty_hint') }}</p>
            </div>
        @endforelse
    </div>

    @if ($canChat)
        <form
            method="POST"
            action="{{ $storeRoute }}"
            class="shrink-0 border-t border-indigo-100 bg-white p-3 sm:p-4 min-w-0"
            x-on:submit="sending = true"
        >
            @csrf
            <label for="direct-hire-chat-body" class="sr-only">{{ __('talenma.direct_hire.chat_placeholder') }}</label>
            <div class="flex items-end gap-2 rounded-2xl bg-slate-50 p-2 ring-1 ring-slate-200 focus-within:ring-2 focus-within:ring-indigo-400 focus-within:bg-white transition">
                <textarea
                    id="direct-hire-chat-body"
                    name="body"
                    rows="1"
                    required
                    minlength="2"
                    maxlength="2000"
                    x-model="body"
                    x-ref="composer"
                    x-on:keydown="onKeydown($event)"
                    x-on:input="resizeComposer()"
                    x-bind:readonly="sending"
                    class="block max-h-[140px] min-h-[2.5rem] w-full flex-1 resize-none border-0 bg-transparent px-2.5 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0"
                    placeholder="{{ __('talenma.direct_hire.chat_placeholder') }}"
                ></textarea>
                <button
                    type="submit"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 disabled:opacity-50"
                    x-bind:disabled="sending || ! canSend"
                    title="{{ __('talenma.direct_hire.chat_send') }}"
                >
                    <span class="sr-only">{{ __('talenma.direct_hire.chat_send') }}</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </button>
            </div>
            <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                <p class="text-[11px] text-slate-400">{{ __('talenma.direct_hire.chat_composer_hint') }}</p>
                <p
                    class="text-[11px] tabular-nums"
                    x-bind:class="nearLimit ? 'font-semibold text-amber-600' : 'text-slate-400'"
                    x-text="characterCount + ' / ' + maxLength"
                ></p>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('body')" />
        </form>
    @else
        <div class="shrink-0 border-t border-slate-200 bg-slate-50 px-4 sm:px-5 py-3.5">
            <p class="text-xs text-slate-500">{{ __('talenma.direct_hire.chat_closed') }}</p>
        </div>
    @endif
</section>
