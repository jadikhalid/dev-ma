<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.inbox.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.inbox.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border overflow-hidden min-h-[28rem]">
            @if ($conversations->isEmpty())
                <div class="py-20 text-center px-6">
                    <p class="text-lg font-medium text-gray-900">{{ __('talenma.inbox.empty') }}</p>
                    <p class="mt-2 text-sm text-gray-500">{{ __('talenma.inbox.empty_desc') }}</p>
                    @if (Auth::user()->isCompany())
                        <a href="{{ route('company.search') }}" class="mt-6 inline-flex rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                            {{ __('talenma.inbox.browse_talents') }}
                        </a>
                    @endif
                </div>
            @else
                <div class="divide-y">
                    @foreach ($conversations as $item)
                        <a
                            href="{{ $item['show_url'] }}"
                            class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 {{ ! empty($item['unread']) ? 'bg-indigo-50/40' : '' }}"
                        >
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="truncate font-semibold text-gray-900">{{ $item['counterpart']['name'] }}</p>
                                    @if (! empty($item['unread']))
                                        <span class="inline-flex h-2 w-2 rounded-full bg-indigo-600"></span>
                                    @endif
                                </div>
                                <p class="mt-0.5 truncate text-sm text-indigo-700">{{ $item['subject'] }}</p>
                                <p class="mt-1 truncate text-sm text-gray-500">{{ $item['last_message_preview'] }}</p>
                            </div>
                            @if (! empty($item['last_message_at']))
                                <time class="shrink-0 text-xs text-gray-400">
                                    {{ \Illuminate\Support\Carbon::parse($item['last_message_at'])->diffForHumans() }}
                                </time>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
