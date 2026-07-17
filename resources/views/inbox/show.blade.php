<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('inbox.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('talenma.inbox.back') }}</a>
                <h2 class="mt-1 text-xl font-bold">{{ $conversation['subject'] }}</h2>
                <p class="text-sm text-gray-500">{{ $conversation['counterpart']['name'] }}</p>
            </div>
        </div>
    </x-slot>

    <div
        class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="inboxThread({
            conversationId: @js($conversation['id']),
            pollUrl: @js(route('inbox.show', $conversation['id'])),
            replyUrl: @js(route('inbox.messages.store', $conversation['id'])),
            initialMessages: @js($conversation['messages']),
            csrf: @js(csrf_token()),
            labels: @js([
                'replyPlaceholder' => __('talenma.inbox.reply_placeholder'),
                'send' => __('talenma.inbox.send'),
                'attach' => __('talenma.inbox.attach'),
                'sending' => __('talenma.inbox.sending'),
                'error' => __('talenma.inbox.error'),
                'attachmentsHint' => __('talenma.inbox.attachments_hint'),
            ]),
        })"
    >
        <div class="grid lg:grid-cols-12 gap-6">
            <aside class="hidden lg:block lg:col-span-4">
                <div class="bg-white rounded-2xl border overflow-hidden max-h-[70vh] overflow-y-auto">
                    @forelse ($conversations as $item)
                        <a
                            href="{{ $item['show_url'] }}"
                            class="block border-b px-4 py-3 hover:bg-gray-50 {{ (int) $item['id'] === (int) $conversation['id'] ? 'bg-indigo-50' : '' }}"
                        >
                            <div class="flex items-center gap-2">
                                <p class="truncate text-sm font-semibold text-gray-900">{{ $item['counterpart']['name'] }}</p>
                                @if (! empty($item['unread']) && (int) $item['id'] !== (int) $conversation['id'])
                                    <span class="inline-flex h-2 w-2 rounded-full bg-indigo-600"></span>
                                @endif
                            </div>
                            <p class="mt-0.5 truncate text-xs text-gray-500">{{ $item['subject'] }}</p>
                        </a>
                    @empty
                        <p class="px-4 py-8 text-sm text-gray-500">{{ __('talenma.inbox.empty') }}</p>
                    @endforelse
                </div>
            </aside>

            <section class="lg:col-span-8 bg-white rounded-2xl border flex flex-col min-h-[32rem] max-h-[75vh]">
                <div class="border-b px-5 py-4">
                    <p class="font-semibold text-gray-900">{{ $conversation['counterpart']['name'] }}</p>
                    @if (! empty($conversation['counterpart']['role_label']))
                        <p class="text-sm text-indigo-600">{{ $conversation['counterpart']['role_label'] }}</p>
                    @endif
                    <p class="mt-1 text-sm text-gray-500">{{ $conversation['subject'] }}</p>
                </div>

                <div x-ref="thread" class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
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
                                <p
                                    class="mt-2 text-[11px] opacity-70"
                                    x-text="message.created_at_human"
                                ></p>
                            </div>
                        </div>
                    </template>
                </div>

                <form class="border-t p-4 space-y-3" @submit.prevent="sendReply()">
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
            </section>
        </div>
    </div>
</x-app-layout>
