@php
    $initialConversation = (isset($conversation) && is_array($conversation)) ? $conversation : null;
    $viewer = Auth::user();
    $canCompose = $viewer?->isCompany() ?? false;
    $canDelete = ($viewer?->isCompany() || $viewer?->isTalent()) ?? false;
    $deleteBodyKey = $viewer?->isTalent()
        ? 'talenma.inbox.delete_confirm_body_talent'
        : 'talenma.inbox.delete_confirm_body';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-slate-900">{{ __('talenma.inbox.title') }}</h2>
                @if (__('talenma.inbox.subtitle') !== '')
                    <p class="text-sm text-slate-500">{{ __('talenma.inbox.subtitle') }}</p>
                @endif
            </div>
            @if ($canCompose)
                <button
                    type="button"
                    onclick="window.dispatchEvent(new CustomEvent('inbox-open-compose'))"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-indigo-200 bg-white text-indigo-700 shadow-sm transition hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    aria-label="{{ __('talenma.inbox.compose_open_aria') }}"
                    title="{{ __('talenma.inbox.compose_open_aria') }}"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            @endif
        </div>
    </x-slot>

    <x-process-help topic="inbox" />

    <div
        class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4"
        x-data="inboxWorkspace({
            conversations: @js($conversations->values()),
            conversation: @js($initialConversation),
            indexUrl: @js(route('inbox.index')),
            composeUrl: @js($canCompose ? route('inbox.store') : null),
            talentSearchUrl: @js($canCompose ? route('inbox.talent-suggestions') : null),
            canCompose: @js($canCompose),
            canDelete: @js($canDelete),
            csrf: @js(csrf_token()),
            labels: @js([
                'replyPlaceholder' => __('talenma.inbox.reply_placeholder'),
                'send' => __('talenma.inbox.send'),
                'attach' => __('talenma.inbox.attach'),
                'sending' => __('talenma.inbox.sending'),
                'error' => __('talenma.inbox.error'),
                'attachmentsHint' => __('talenma.inbox.attachments_hint'),
                'empty' => __('talenma.inbox.empty'),
                'emptyDesc' => __('talenma.inbox.empty_desc'),
                'selectConversation' => __('talenma.inbox.select_conversation'),
                'selectConversationDesc' => __('talenma.inbox.select_conversation_desc'),
                'back' => __('talenma.inbox.back'),
                'composeSubjectRequired' => __('talenma.inbox.compose_subject_required'),
                'composeMinBody' => __('talenma.inbox.compose_min_body'),
                'composeTalentRequired' => __('talenma.inbox.compose_talent_required'),
                'composeBodyRequired' => __('talenma.inbox.compose_body_required'),
                'talentSearchLoading' => __('talenma.inbox.talent_search_loading'),
                'talentSearchEmpty' => __('talenma.inbox.talent_search_empty'),
                'sent' => __('talenma.inbox.sent'),
                'networkError' => __('talenma.common.network_error'),
                'deleteAria' => __('talenma.inbox.delete_aria'),
                'deleteBadge' => __('talenma.inbox.delete_confirm_badge'),
                'deleteTitle' => __('talenma.inbox.delete_confirm_title'),
                'deleteBody' => __($deleteBodyKey),
                'deleteConfirm' => __('talenma.inbox.delete_confirm_btn'),
                'deleteCancel' => __('talenma.inbox.delete_confirm_cancel'),
            ]),
        })"
        @inbox-open-compose.window="openCompose()"
    >
        <div class="rounded-2xl border border-indigo-100/80 bg-white shadow-sm shadow-indigo-600/5 overflow-hidden min-h-[32rem] h-[min(78vh,46rem)] grid lg:grid-cols-[minmax(0,23rem)_minmax(0,1fr)]">
            {{-- Left: conversation list --}}
            <aside
                class="border-b lg:border-b-0 lg:border-r border-indigo-100/80 flex-col min-h-0 bg-gradient-to-b from-slate-50/90 to-white"
                :class="hasSelection ? 'hidden lg:flex' : 'flex'"
            >
                <div class="shrink-0 px-4 py-3.5 border-b border-indigo-100/80 bg-indigo-100/80">
                    <p class="text-xs font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.inbox.conversations') }}</p>
                </div>

                <div class="flex-1 overflow-y-auto min-h-0">
                    <template x-if="conversations.length === 0">
                        <div class="px-5 py-16 text-center flex flex-col items-center">
                            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400 ring-1 ring-indigo-100" aria-hidden="true">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                            </span>
                            <p class="mt-4 text-sm font-semibold text-slate-800" x-text="labels.empty"></p>
                            <p class="mt-1 text-xs text-slate-500 max-w-[14rem]" x-text="labels.emptyDesc"></p>
                        </div>
                    </template>

                    <ul x-show="conversations.length > 0" class="divide-y divide-slate-100 min-h-full">
                        <template x-for="item in conversations" :key="item.id">
                            <li class="group relative flex items-stretch">
                                <button
                                    type="button"
                                    data-inbox-item
                                    class="min-w-0 flex-1 text-left block px-4 py-3.5 transition border-l-2 cursor-pointer"
                                    :class="isActive(item)
                                        ? 'bg-indigo-50 border-indigo-600'
                                        : (item.unread ? 'bg-indigo-50/30 border-transparent hover:bg-slate-50' : 'border-transparent hover:bg-slate-50')"
                                    @click.stop="selectConversation(item)"
                                >
                                    <div class="flex items-start justify-between gap-2 pointer-events-none">
                                        <div class="min-w-0 flex-1 pr-8">
                                            <div class="flex items-center gap-2">
                                                <p
                                                    class="truncate text-sm font-semibold"
                                                    :class="isActive(item) ? 'text-indigo-900' : 'text-gray-900'"
                                                    x-text="item.counterpart?.name"
                                                ></p>
                                                <span
                                                    x-show="item.unread && ! isActive(item)"
                                                    class="inline-flex h-2 w-2 shrink-0 rounded-full bg-indigo-600"
                                                ></span>
                                            </div>
                                            <p class="mt-0.5 truncate text-xs text-slate-500" x-text="item.subject"></p>
                                            <p class="mt-1 truncate text-xs text-slate-400" x-text="item.last_message_preview"></p>
                                        </div>
                                        <time
                                            x-show="item.last_message_at"
                                            class="shrink-0 text-[10px] text-slate-400 pt-0.5"
                                            x-text="item.last_message_at ? new Date(item.last_message_at).toLocaleDateString() : ''"
                                        ></time>
                                    </div>
                                </button>
                                <button
                                    type="button"
                                    x-show="canDelete && item.destroy_url"
                                    x-cloak
                                    class="absolute right-2 top-1/2 z-10 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400"
                                    :aria-label="labels.deleteAria"
                                    :title="labels.deleteAria"
                                    @click.stop="requestDelete(item)"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
            </aside>

            {{-- Right: thread --}}
            <section
                data-inbox-thread
                class="flex-col min-h-0 min-w-0 relative bg-white"
                :class="hasSelection ? 'flex' : 'hidden lg:flex'"
            >
                <div
                    x-show="selecting"
                    x-cloak
                    class="absolute inset-0 z-20 flex items-center justify-center bg-white/75 backdrop-blur-[1px]"
                    aria-hidden="true"
                >
                    <svg class="h-7 w-7 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <template x-if="hasSelection && conversation">
                    <div class="flex flex-col min-h-0 flex-1">
                        <div class="shrink-0 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 via-white to-slate-50 px-4 sm:px-5 py-3.5">
                            <button
                                type="button"
                                class="lg:hidden inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 mb-2.5"
                                @click="clearSelection()"
                            >
                                <span aria-hidden="true">←</span>
                                <span x-text="labels.back"></span>
                            </button>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900 truncate" x-text="conversation.counterpart?.name"></p>
                                <p
                                    class="text-xs font-medium text-indigo-600 truncate"
                                    x-show="conversation.counterpart?.role_label"
                                    x-text="conversation.counterpart?.role_label"
                                ></p>
                                <p class="mt-0.5 text-sm text-slate-500 truncate" x-text="conversation.subject"></p>
                            </div>
                        </div>

                        <div x-ref="thread" class="flex-1 overflow-y-auto px-4 sm:px-5 py-4 space-y-3 min-h-0 bg-slate-50/80">
                            <template x-for="message in messages" :key="message.id">
                                <div class="flex" :class="message.is_mine ? 'justify-end' : 'justify-start'">
                                    <div
                                        class="max-w-[min(100%,22rem)] sm:max-w-[78%] rounded-2xl px-3.5 py-2.5 text-sm shadow-sm min-w-0"
                                        :class="message.is_mine
                                            ? 'bg-indigo-600 text-white rounded-br-md shadow-indigo-600/15'
                                            : 'bg-white border border-slate-200/90 text-slate-800 rounded-bl-md'"
                                    >
                                        <p class="whitespace-pre-line leading-relaxed break-words" x-text="message.body"></p>
                                        <div class="mt-2 space-y-1" x-show="message.attachments?.length">
                                            <template x-for="file in message.attachments" :key="file.id">
                                                <a
                                                    :href="file.url"
                                                    target="_blank"
                                                    class="block text-xs font-medium underline underline-offset-2"
                                                    :class="message.is_mine ? 'text-indigo-100' : 'text-indigo-700'"
                                                >
                                                    <span x-text="file.original_name"></span>
                                                    <span x-text="' (' + file.size_label + ')'"></span>
                                                </a>
                                            </template>
                                        </div>
                                        <p
                                            class="mt-1.5 text-[10px] font-medium tabular-nums"
                                            :class="message.is_mine ? 'text-indigo-200' : 'text-slate-400'"
                                            x-text="message.created_at_human"
                                        ></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <form class="shrink-0 border-t border-indigo-100 bg-white p-3 sm:p-4 space-y-3" @submit.prevent="sendReply()">
                            <div class="rounded-2xl bg-slate-50 p-2 ring-1 ring-slate-200 focus-within:ring-2 focus-within:ring-indigo-400 focus-within:bg-white transition">
                                <textarea
                                    x-model="body"
                                    rows="3"
                                    class="block w-full resize-none border-0 bg-transparent px-2.5 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0"
                                    :placeholder="labels.replyPlaceholder"
                                    required
                                ></textarea>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                                <div class="min-w-0">
                                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                                        <input type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" @change="onFiles($event)">
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-indigo-200 transition" x-text="labels.attach"></span>
                                    </label>
                                    <p class="mt-1 text-[11px] text-slate-400" x-text="labels.attachmentsHint"></p>
                                    <ul class="mt-1 text-xs text-slate-600 space-y-0.5" x-show="files.length">
                                        <template x-for="(file, index) in files" :key="file.name + index">
                                            <li class="flex items-center gap-2">
                                                <span class="truncate" x-text="file.name"></span>
                                                <button type="button" class="font-bold text-rose-600 hover:text-rose-700" @click="removeFile(index)">×</button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-700 disabled:opacity-50 transition"
                                    :disabled="sending || ! body.trim()"
                                >
                                    <span x-text="sending ? labels.sending : labels.send"></span>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                    </svg>
                                </button>
                            </div>
                            <p x-show="error" class="text-sm text-rose-600" x-text="error"></p>
                        </form>
                    </div>
                </template>

                <template x-if="! hasSelection">
                    <div class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center bg-gradient-to-br from-indigo-50/40 via-white to-slate-50">
                        <template x-if="conversations.length === 0">
                            <div class="flex flex-col items-center max-w-sm">
                                <span class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-white text-indigo-500 ring-1 ring-indigo-100 shadow-sm shadow-indigo-600/10" aria-hidden="true">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.91a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                                    </svg>
                                </span>
                                <p class="mt-5 text-base font-semibold text-slate-800" x-text="labels.empty"></p>
                                <p class="mt-2 text-sm text-slate-500 leading-relaxed" x-text="labels.emptyDesc"></p>
                            </div>
                        </template>
                        <template x-if="conversations.length > 0">
                            <div class="flex flex-col items-center max-w-sm">
                                <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400/50 ring-1 ring-indigo-100/60" aria-hidden="true">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                                    </svg>
                                </span>
                                <p class="mt-5 text-base font-semibold text-slate-900" x-text="labels.selectConversation"></p>
                                <p class="mt-2 text-sm text-slate-500 leading-relaxed" x-text="labels.selectConversationDesc"></p>
                            </div>
                        </template>
                    </div>
                </template>
            </section>
        </div>

        @if ($canCompose)
            {{-- Bottom sheet: composer un message à un talent --}}
            <div
                x-show="composeOpen"
                x-cloak
                class="fixed inset-0 z-[60]"
                role="dialog"
                aria-modal="true"
                aria-labelledby="inbox-compose-title"
                data-inbox-compose
            >
                <div
                    x-show="composeOpen"
                    x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 bg-gray-900/40"
                    @click="closeCompose()"
                ></div>

                <div
                    x-show="composeOpen"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-y-full"
                    x-transition:enter-end="translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-y-0"
                    x-transition:leave-end="translate-y-full"
                    class="absolute bottom-0 right-0 flex w-full max-w-md flex-col rounded-t-2xl bg-white shadow-2xl ring-1 ring-black/5 sm:bottom-3 sm:right-3 sm:max-h-[min(88vh,42rem)] sm:max-w-lg sm:rounded-2xl"
                    @click.stop
                >
                    <div class="mx-auto mt-3 h-1.5 w-10 shrink-0 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>

                    <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4">
                        <div class="min-w-0">
                            <h3 id="inbox-compose-title" class="text-lg font-bold text-gray-900">{{ __('talenma.inbox.compose_title') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('talenma.inbox.compose_sheet_desc') }}</p>
                        </div>
                        <button
                            type="button"
                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                            @click="closeCompose()"
                            aria-label="{{ __('talenma.common.close') }}"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div id="inbox-compose-sheet" class="relative min-h-0 flex-1 overflow-y-auto px-5 py-5">
                        <form
                            class="space-y-3"
                            @submit.prevent="sendCompose()"
                            novalidate
                            data-error-message="{{ __('talenma.inbox.error') }}"
                            data-network-error-message="{{ __('talenma.common.network_error') }}"
                        >
                            <div class="relative" @click.outside="closeTalentSuggestions()">
                                <label class="block text-xs font-medium text-gray-600" for="inbox-compose-talent">{{ __('talenma.inbox.compose_talent') }}</label>
                                <input
                                    id="inbox-compose-talent"
                                    type="search"
                                    x-model="talentQuery"
                                    x-ref="talentInput"
                                    @input="onTalentInput()"
                                    @keydown="onTalentKeydown($event)"
                                    @focus="onTalentFocus()"
                                    maxlength="100"
                                    autocomplete="off"
                                    role="combobox"
                                    aria-controls="inbox-talent-listbox"
                                    :aria-expanded="talentSuggestionsOpen"
                                    aria-autocomplete="list"
                                    :disabled="composeSending"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                    placeholder="{{ __('talenma.inbox.compose_talent_placeholder') }}"
                                >
                                <input
                                    type="hidden"
                                    name="talent_id"
                                    :value="selectedTalentId ?? ''"
                                    data-required
                                    data-required-message="{{ __('talenma.inbox.compose_talent_required') }}"
                                >

                                <div
                                    x-show="talentSuggestionsOpen"
                                    x-cloak
                                    id="inbox-talent-listbox"
                                    role="listbox"
                                    class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-lg"
                                >
                                    <template x-if="talentLoading">
                                        <p class="px-3 py-2 text-xs text-slate-500" x-text="labels.talentSearchLoading"></p>
                                    </template>
                                    <template x-if="! talentLoading && talentResults.length === 0">
                                        <p class="px-3 py-2 text-xs text-slate-500" x-text="labels.talentSearchEmpty"></p>
                                    </template>
                                    <template x-for="(item, index) in talentResults" :key="item.id">
                                        <button
                                            type="button"
                                            role="option"
                                            class="flex w-full flex-col items-start gap-0.5 px-3 py-2 text-left text-sm transition"
                                            :class="index === talentActiveIndex ? 'bg-indigo-50 text-indigo-900' : 'text-slate-800 hover:bg-slate-50'"
                                            @mousedown.prevent="selectTalent(item)"
                                            @mouseenter="talentActiveIndex = index"
                                        >
                                            <span class="font-medium" x-text="item.label"></span>
                                            <span class="text-xs text-slate-500" x-show="item.subtitle" x-text="item.subtitle"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600" for="inbox-compose-subject">{{ __('talenma.inbox.compose_subject') }}</label>
                                <input
                                    id="inbox-compose-subject"
                                    name="subject"
                                    type="text"
                                    x-model="composeSubject"
                                    maxlength="255"
                                    data-required
                                    data-required-message="{{ __('talenma.inbox.compose_subject_required') }}"
                                    :disabled="composeSending"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                    placeholder="{{ __('talenma.inbox.compose_subject_placeholder') }}"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600" for="inbox-compose-body">{{ __('talenma.inbox.compose_body') }}</label>
                                <textarea
                                    id="inbox-compose-body"
                                    name="body"
                                    x-model="composeBody"
                                    rows="5"
                                    maxlength="5000"
                                    data-required
                                    data-required-message="{{ __('talenma.inbox.compose_body_required') }}"
                                    data-min-length="20"
                                    data-min-length-message="{{ __('talenma.inbox.compose_min_body') }}"
                                    :disabled="composeSending"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                    placeholder="{{ __('talenma.inbox.compose_body_placeholder') }}"
                                ></textarea>
                            </div>

                            <div>
                                <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-gray-600" :class="composeSending && 'pointer-events-none opacity-60'">
                                    <input type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" @change="onComposeFiles($event)" :disabled="composeSending">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ __('talenma.inbox.attach') }}</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-400">{{ __('talenma.inbox.attachments_hint') }}</p>
                                <ul class="mt-1 space-y-1 text-xs text-gray-600" x-show="composeFiles.length">
                                    <template x-for="(file, index) in composeFiles" :key="file.name + index">
                                        <li class="flex items-center gap-2">
                                            <span class="truncate" x-text="file.name"></span>
                                            <button type="button" class="text-red-600" @click="removeComposeFile(index)" :disabled="composeSending">×</button>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-60"
                                    :disabled="composeSending"
                                    x-text="composeSending ? labels.sending : @js(__('talenma.inbox.compose_send'))"
                                ></button>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-60"
                                    @click="closeCompose()"
                                    :disabled="composeSending"
                                >{{ __('talenma.inbox.compose_cancel') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <template x-teleport="body">
            <div
                x-show="deleteConfirming"
                x-cloak
                class="fixed inset-0 z-[80] flex items-center justify-center p-4"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="inbox-delete-title"
                data-inbox-compose
                @keydown.escape.window="closeDeleteConfirm()"
            >
                <div class="absolute inset-0 bg-slate-900/50" @click="closeDeleteConfirm()" aria-hidden="true"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl ring-2 ring-rose-200">
                    <p class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-rose-700 ring-1 ring-rose-100" x-text="labels.deleteBadge"></p>
                    <p id="inbox-delete-title" class="mt-3 text-base font-semibold text-slate-900" x-text="labels.deleteTitle"></p>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600" x-text="labels.deleteBody"></p>
                    <div class="mt-5 flex flex-wrap justify-end gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-60"
                            @click="closeDeleteConfirm()"
                            :disabled="deleteSending"
                            x-text="labels.deleteCancel"
                        ></button>
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-rose-700 disabled:opacity-60"
                            @click="confirmDelete()"
                            :disabled="deleteSending"
                            x-text="deleteSending ? labels.sending : labels.deleteConfirm"
                        ></button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
