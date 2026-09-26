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

        <div class="flex flex-wrap gap-2">
            @foreach ([
                'all' => ['label' => __('talenma.newsletter.filter_all'), 'count' => $activeCount],
                'registered' => ['label' => __('talenma.newsletter.filter_registered'), 'count' => $registeredCount],
                'guests' => ['label' => __('talenma.newsletter.filter_guests'), 'count' => $guestsCount],
            ] as $key => $tab)
                <a
                    href="{{ route('admin.newsletter.subscribers.index', array_filter(['filter' => $key === 'all' ? null : $key, 'q' => $q !== '' ? $q : null])) }}"
                    @class([
                        'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-sm font-semibold border transition',
                        'bg-indigo-600 text-white border-indigo-600' => $filter === $key,
                        'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $filter !== $key,
                    ])
                >
                    <span>{{ $tab['label'] }}</span>
                    <span @class([
                        'tabular-nums text-xs rounded-full px-1.5 py-0.5',
                        'bg-white/20 text-white' => $filter === $key,
                        'bg-gray-100 text-gray-600' => $filter !== $key,
                    ])>{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.newsletter.subscribers.index') }}" class="flex gap-2">
            @if ($filter !== 'all')
                <input type="hidden" name="filter" value="{{ $filter }}">
            @endif
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('talenma.newsletter.subscribers_search') }}" class="flex-1 rounded-lg border-gray-300 text-sm">
            <button type="submit" class="inline-flex px-4 py-2 border rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('talenma.newsletter.subscribers_search_btn') }}</button>
        </form>

        <div class="rounded-2xl border bg-white overflow-hidden divide-y">
            @forelse ($subscribers as $subscriber)
                @php $isRegistered = $subscriber->isRegistered(); @endphp
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $subscriber->email }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">
                            {{ $subscriber->sourceLabel() }}
                            ·
                            <span @class([
                                'font-semibold',
                                'text-indigo-700' => $isRegistered,
                                'text-slate-600' => ! $isRegistered,
                            ])>
                                {{ $isRegistered ? __('talenma.newsletter.subscriber_registered') : __('talenma.newsletter.subscriber_guest') }}
                            </span>
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
                    @else
                        <div class="flex items-center gap-3 shrink-0">
                            <form method="POST" action="{{ route('admin.newsletter.subscribers.reactivate', $subscriber) }}">
                                @csrf
                                <button type="submit" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">{{ __('talenma.newsletter.subscriber_reactivate') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.newsletter.subscribers.purge', $subscriber) }}" onsubmit="return confirm(@js(__('talenma.newsletter.subscriber_purge_confirm')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-900">{{ __('talenma.newsletter.subscriber_purge') }}</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-500">{{ __('talenma.newsletter.subscribers_empty') }}</div>
            @endforelse
        </div>

        <div>{{ $subscribers->links() }}</div>
    </div>
</x-app-layout>
