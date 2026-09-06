<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.newsletter.subscribers_title') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.newsletter.subscribers_subtitle', ['count' => $activeCount]) }}</p>
            </div>
            <a href="{{ route('admin.newsletter.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                ← {{ __('talenma.newsletter.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="rounded-2xl border bg-white p-5 sm:p-6 space-y-3">
            <h3 class="text-sm font-bold uppercase tracking-wide text-slate-600">{{ __('talenma.newsletter.subscribers_add_title') }}</h3>
            <p class="text-sm text-slate-500">{{ __('talenma.newsletter.subscribers_add_help') }}</p>
            <form method="POST" action="{{ route('admin.newsletter.subscribers.store') }}" class="space-y-3">
                @csrf
                <textarea
                    name="emails"
                    rows="4"
                    required
                    class="w-full rounded-lg border-gray-300 text-sm"
                    placeholder="exemple@mail.com&#10;autre@mail.com"
                >{{ old('emails') }}</textarea>
                <x-input-error :messages="$errors->get('emails')" />
                <x-primary-button type="submit">{{ __('talenma.newsletter.subscribers_add_submit') }}</x-primary-button>
            </form>
        </div>

        <form method="GET" action="{{ route('admin.newsletter.subscribers.index') }}" class="flex gap-2">
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('talenma.newsletter.subscribers_search') }}" class="flex-1 rounded-lg border-gray-300 text-sm">
            <button type="submit" class="inline-flex px-4 py-2 border rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('talenma.newsletter.subscribers_search_btn') }}</button>
        </form>

        <div class="rounded-2xl border bg-white overflow-hidden divide-y">
            @forelse ($subscribers as $subscriber)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $subscriber->email }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">
                            {{ $subscriber->sourceLabel() }}
                            @if ($subscriber->isActive())
                                · {{ __('talenma.newsletter.subscriber_active') }}
                                @if ($subscriber->subscribed_at)
                                    · {{ $subscriber->subscribed_at->translatedFormat('d M Y') }}
                                @endif
                            @else
                                · {{ __('talenma.newsletter.subscriber_inactive') }}
                            @endif
                        </p>
                    </div>
                    @if ($subscriber->isActive())
                        <form method="POST" action="{{ route('admin.newsletter.subscribers.destroy', $subscriber) }}" onsubmit="return confirm(@js(__('talenma.newsletter.subscriber_remove_confirm')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-900">{{ __('talenma.newsletter.subscriber_remove') }}</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-500">{{ __('talenma.newsletter.subscribers_empty') }}</div>
            @endforelse
        </div>

        <div>{{ $subscribers->links() }}</div>
    </div>
</x-app-layout>
