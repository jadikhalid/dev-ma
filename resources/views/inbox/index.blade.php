@php
    $initialConversation = (isset($conversation) && is_array($conversation)) ? $conversation : null;
    $viewer = Auth::user();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ __('talenma.inbox.title') }}</h2>
            @if (__('talenma.inbox.subtitle') !== '')
                <p class="text-sm text-slate-500">{{ __('talenma.inbox.subtitle') }}</p>
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
            ]),
        })"
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
                            <li>
                                <button
                                    type="button"
                                    data-inbox-item
                                    class="w-full text-left block px-4 py-3.5 transition border-l-2 cursor-pointer"
                                    :class="isActive(item)
                                        ? 'bg-indigo-50 border-indigo-600'
                                        : (item.unread ? 'bg-indigo-50/30 border-transparent hover:bg-slate-50' : 'border-transparent hover:bg-slate-50')"
                                    @click.stop="selectConversation(item)"
                                >
                                    <div class="flex items-start justify-between gap-2 pointer-events-none">
                                        <div class="min-w-0 flex-1">
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
    </div>
</x-app-layout>
