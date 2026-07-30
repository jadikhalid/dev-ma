@php
    $initialConversation = (isset($conversation) && is_array($conversation)) ? $conversation : null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.inbox.title') }}</h2>
            @if (__('talenma.inbox.subtitle') !== '')
                <p class="text-sm text-gray-500">{{ __('talenma.inbox.subtitle') }}</p>
            @endif
        </div>
    </x-slot>

    <div
        class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4"
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
        @if (Auth::user()?->isCompany() || Auth::user()?->isTalent())
            <div class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-gradient-to-br from-slate-50 via-white to-indigo-50/40 px-4 py-3.5 sm:max-w-3xl">
                <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200/80" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v.01M12 6a3.75 3.75 0 013.75 3.75c0 1.55-.94 2.4-1.88 3.12-.7.54-1.37 1.05-1.37 2.13v.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div class="min-w-0 pt-0.5">
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-indigo-700/80">{{ __('talenma.inbox.start_hint_label') }}</p>
                    <p class="mt-1 text-sm leading-relaxed text-slate-600">
                        {{ Auth::user()->isCompany() ? __('talenma.inbox.start_hint') : __('talenma.inbox.start_hint_talent') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border overflow-hidden min-h-[32rem] h-[min(75vh,44rem)] grid lg:grid-cols-[minmax(0,22rem)_minmax(0,1fr)]">
            {{-- Left: conversation list — whole row is clickable --}}
            <aside
                class="border-b lg:border-b-0 lg:border-r border-slate-200 flex-col min-h-0"
                :class="hasSelection ? 'hidden lg:flex' : 'flex'"
            >
                <div class="shrink-0 px-4 py-3 border-b border-slate-100 bg-slate-50/80">
                    <p class="text-xs font-bold uppercase tracking-[0.1em] text-slate-600">{{ __('talenma.inbox.conversations') }}</p>
                </div>

                <div class="flex-1 overflow-y-auto min-h-0">
                    <template x-if="conversations.length === 0">
                        <div class="px-5 py-14 text-center flex flex-col items-center">
                            <svg class="h-24 w-24 text-slate-400 opacity-25" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.15" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-900" x-text="labels.empty"></p>
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
                                                    x-text="item.subject"
                                                ></p>
                                                <span
                                                    x-show="item.unread && ! isActive(item)"
                                                    class="inline-flex h-2 w-2 shrink-0 rounded-full bg-indigo-600"
                                                ></span>
                                            </div>
                                            <p class="mt-0.5 truncate text-xs text-slate-500" x-text="item.counterpart?.name"></p>
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
                class="flex-col min-h-0 min-w-0 relative"
                :class="hasSelection ? 'flex' : 'hidden lg:flex'"
            >
                <div
                    x-show="selecting"
                    x-cloak
                    class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 backdrop-blur-[1px]"
                    aria-hidden="true"
                >
                    <svg class="h-7 w-7 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <template x-if="hasSelection && conversation">
                    <div class="flex flex-col min-h-0 flex-1">
                        <div class="shrink-0 border-b border-slate-100 px-4 sm:px-5 py-3.5">
                            <button
                                type="button"
                                class="lg:hidden inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 mb-2"
                                @click="clearSelection()"
                            >
                                <span aria-hidden="true">←</span>
                                <span x-text="labels.back"></span>
                            </button>
                            <p class="font-semibold text-gray-900 truncate" x-text="conversation.counterpart?.name"></p>
                            <p
                                class="text-sm text-indigo-600"
                                x-show="conversation.counterpart?.role_label"
                                x-text="conversation.counterpart?.role_label"
                            ></p>
                            <p class="mt-0.5 text-sm text-gray-500 truncate" x-text="conversation.subject"></p>
                        </div>

                        <div x-ref="thread" class="flex-1 overflow-y-auto px-4 sm:px-5 py-4 space-y-4 min-h-0">
                            <template x-for="message in messages" :key="message.id">
                                <div class="flex" :class="message.is_mine ? 'justify-end' : 'justify-start'">
                                    <div
                                        class="max-w-[85%] rounded-2xl px-4 py-3"
                                        :class="message.is_mine ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900'"
                                    >
                                        <p class="text-sm whitespace-pre-line" x-text="message.body"></p>
                                        <div class="mt-2 space-y-1" x-show="message.attachments?.length">
                                            <template x-for="file in message.attachments" :key="file.id">
                                                <a
                                                    :href="file.url"
                                                    target="_blank"
                                                    class="block text-xs underline"
                                                    :class="message.is_mine ? 'text-indigo-100' : 'text-indigo-700'"
                                                >
                                                    <span x-text="file.original_name"></span>
                                                    <span x-text="' (' + file.size_label + ')'"></span>
                                                </a>
                                            </template>
                                        </div>
                                        <p class="mt-2 text-[11px] opacity-70" x-text="message.created_at_human"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <form class="shrink-0 border-t border-slate-100 p-4 space-y-3" @submit.prevent="sendReply()">
                            <textarea
                                x-model="body"
                                rows="3"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :placeholder="labels.replyPlaceholder"
                                required
                            ></textarea>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                                <div>
                                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-gray-600">
                                        <input type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" @change="onFiles($event)">
                                        <span class="rounded-lg border px-3 py-1.5 hover:bg-gray-50" x-text="labels.attach"></span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-400" x-text="labels.attachmentsHint"></p>
                                    <ul class="mt-1 text-xs text-gray-600" x-show="files.length">
                                        <template x-for="(file, index) in files" :key="file.name + index">
                                            <li class="flex items-center gap-2">
                                                <span x-text="file.name"></span>
                                                <button type="button" class="text-red-600" @click="removeFile(index)">×</button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <button
                                    type="submit"
                                    class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                                    :disabled="sending || ! body.trim()"
                                    x-text="sending ? labels.sending : labels.send"
                                ></button>
                            </div>
                            <p x-show="error" class="text-sm text-red-600" x-text="error"></p>
                        </form>
                    </div>
                </template>

                <template x-if="! hasSelection">
                    <div class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center">
                        <template x-if="conversations.length === 0">
                            <div class="flex flex-col items-center">
                                <svg class="h-28 w-28 text-slate-400 opacity-25" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.15" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.91a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                                </svg>
                                <p class="mt-4 text-sm text-gray-500" x-text="labels.emptyDesc"></p>
                            </div>
                        </template>
                        <template x-if="conversations.length > 0">
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="labels.selectConversation"></p>
                                <p class="mt-2 text-sm text-gray-500" x-text="labels.selectConversationDesc"></p>
                            </div>
                        </template>
                    </div>
                </template>
            </section>
        </div>
    </div>
</x-app-layout>
