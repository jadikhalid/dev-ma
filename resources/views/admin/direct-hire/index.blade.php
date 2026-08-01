@php
    $toneClasses = fn (string $tone) => match ($tone) {
        'amber' => [
            'bar' => 'bg-amber-500',
            'badge' => 'bg-amber-50 text-amber-900 ring-amber-200',
            'hover' => 'hover:ring-amber-200 hover:bg-amber-50/40',
        ],
        'violet' => [
            'bar' => 'bg-violet-500',
            'badge' => 'bg-violet-50 text-violet-900 ring-violet-200',
            'hover' => 'hover:ring-violet-200 hover:bg-violet-50/40',
        ],
        'sky' => [
            'bar' => 'bg-sky-500',
            'badge' => 'bg-sky-50 text-sky-900 ring-sky-200',
            'hover' => 'hover:ring-sky-200 hover:bg-sky-50/40',
        ],
        'emerald' => [
            'bar' => 'bg-emerald-500',
            'badge' => 'bg-emerald-50 text-emerald-900 ring-emerald-200',
            'hover' => 'hover:ring-emerald-200 hover:bg-emerald-50/40',
        ],
        'rose' => [
            'bar' => 'bg-rose-500',
            'badge' => 'bg-rose-50 text-rose-900 ring-rose-200',
            'hover' => 'hover:ring-rose-200 hover:bg-rose-50/40',
        ],
        default => [
            'bar' => 'bg-slate-400',
            'badge' => 'bg-slate-100 text-slate-800 ring-slate-200',
            'hover' => 'hover:ring-slate-300 hover:bg-slate-50',
        ],
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.direct_hire.admin_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.admin_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="rounded-2xl border border-indigo-100 bg-white p-4 sm:p-5">
            <div class="flex flex-wrap items-end gap-3">
                <div class="min-w-0 flex-1">
                    <label for="admin-dh-talent-query" class="block text-sm font-semibold text-slate-800">{{ __('talenma.direct_hire.admin_start_label') }}</label>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('talenma.direct_hire.admin_start_hint') }}</p>
                </div>
            </div>
            <div
                class="relative mt-3 max-w-xl"
                x-data="adminDirectHireTalentSearch({
                    url: @js(route('admin.direct-hire.talent-search')),
                    placeholder: @js(__('talenma.direct_hire.admin_talent_search_placeholder')),
                    loadingLabel: @js(__('talenma.direct_hire.admin_talent_search_loading')),
                    emptyLabel: @js(__('talenma.direct_hire.admin_talent_search_empty')),
                    minChars: 2,
                })"
                @click.outside="close()"
            >
                <div class="flex flex-wrap gap-3">
                    <div class="relative min-w-0 flex-1">
                        <input
                            id="admin-dh-talent-query"
                            type="search"
                            x-model="query"
                            x-ref="input"
                            @input="onInput()"
                            @keydown="onKeydown($event)"
                            @focus="onFocus()"
                            placeholder="{{ __('talenma.direct_hire.admin_talent_search_placeholder') }}"
                            maxlength="100"
                            autocomplete="off"
                            role="combobox"
                            aria-controls="admin-dh-talent-listbox"
                            :aria-expanded="open"
                            aria-autocomplete="list"
                            class="block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <div
                            id="admin-dh-talent-listbox"
                            x-show="open && (loading || results.length > 0 || (query.trim().length >= minChars && !loading))"
                            x-cloak
                            role="listbox"
                            class="absolute z-30 mt-1 max-h-64 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-lg ring-1 ring-slate-900/5"
                        >
                            <p x-show="loading" class="px-3 py-2 text-sm text-slate-500" x-text="loadingLabel"></p>
                            <p
                                x-show="!loading && results.length === 0 && query.trim().length >= minChars"
                                class="px-3 py-2 text-sm text-slate-500"
                                x-text="emptyLabel"
                            ></p>
                            <template x-for="(item, index) in results" :key="item.id">
                                <button
                                    type="button"
                                    role="option"
                                    class="flex w-full flex-col items-start gap-0.5 px-3 py-2 text-left hover:bg-indigo-50"
                                    :class="{ 'bg-indigo-50': index === activeIndex }"
                                    @mousedown.prevent="select(item)"
                                    @mouseenter="activeIndex = index"
                                >
                                    <span class="text-sm font-semibold text-slate-900" x-text="item.label"></span>
                                    <span class="text-xs text-slate-500" x-text="item.email"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <x-primary-button type="button" x-on:click="continueSelected()" x-bind:disabled="!selectedId">
                        {{ __('talenma.direct_hire.admin_start_btn') }}
                    </x-primary-button>
                </div>
                <p x-show="selectedId" x-cloak class="mt-2 text-xs text-emerald-700">
                    {{ __('talenma.direct_hire.admin_talent_selected_prefix') }}
                    <span class="font-semibold" x-text="selectedLabel"></span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ([
                'all' => __('talenma.direct_hire.origin_filter_all'),
                \App\Models\DirectHireRequest::ORIGIN_STAFF_INTERNAL => __('talenma.direct_hire.origin_staff_internal'),
                \App\Models\DirectHireRequest::ORIGIN_STAFF_ON_BEHALF => __('talenma.direct_hire.origin_staff_on_behalf'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.direct-hire.index', ['filter' => $filter, 'origin' => $key]) }}"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold border transition',
                        'bg-violet-600 text-white border-violet-600' => $origin === $key,
                        'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $origin !== $key,
                    ])
                >
                    {{ $label }}
                    <span @class([
                        'text-xs px-1.5 py-0.5 rounded-full',
                        'bg-white/20' => $origin === $key,
                        'bg-gray-100 text-gray-600' => $origin !== $key,
                    ])>{{ $originCounts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ([
                'open' => __('talenma.direct_hire.admin_filter_open'),
                'closed' => __('talenma.direct_hire.admin_filter_closed'),
                'all' => __('talenma.direct_hire.admin_filter_all'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.direct-hire.index', ['filter' => $key, 'origin' => $origin]) }}"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold border transition',
                        'bg-indigo-600 text-white border-indigo-600' => $filter === $key,
                        'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $filter !== $key,
                    ])
                >
                    {{ $label }}
                    <span @class([
                        'text-xs px-1.5 py-0.5 rounded-full',
                        'bg-white/20' => $filter === $key,
                        'bg-gray-100 text-gray-600' => $filter !== $key,
                    ])>{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        @if ($requests->isEmpty())
            <div class="bg-white rounded-2xl border p-8 text-center text-sm text-gray-500">
                {{ __('talenma.direct_hire.admin_empty') }}
            </div>
        @else
            <ul class="space-y-2.5">
                @foreach ($requests as $hire)
                    @php $tones = $toneClasses($hire->statusTone()); @endphp
                    <li>
                        <a
                            href="{{ route('admin.direct-hire.show', $hire) }}"
                            class="group relative flex overflow-hidden rounded-xl bg-white ring-1 ring-slate-200/90 shadow-sm transition duration-150 {{ $tones['hover'] }} hover:-translate-y-px hover:shadow"
                        >
                            <span class="absolute inset-y-0 left-0 w-1 {{ $tones['bar'] }}" aria-hidden="true"></span>
                            <div class="flex min-w-0 flex-1 flex-col gap-1 py-3 pl-4 pr-3">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="min-w-0 truncate text-sm font-semibold text-slate-900 group-hover:text-indigo-800">
                                        {{ $hire->shortSubject() }}
                                    </p>
                                    <span class="inline-flex shrink-0 items-center gap-1 rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $tones['badge'] }}">
                                        {{ $hire->statusLabel() }}
                                        @if ($hire->hasUnseenChangesForStaff())
                                            <span class="relative flex h-1.5 w-1.5" title="{{ __('talenma.direct_hire.nav_new') }}">
                                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                            </span>
                                        @endif
                                    </span>
                                </div>
                                <p class="truncate text-xs text-slate-600">
                                    <span class="font-medium text-slate-800">{{ $hire->talentDisplayName() }}</span>
                                    <span class="text-slate-300"> · </span>
                                    {{ $hire->hireOriginLabel() }}
                                    @if ($hire->isStaffOnBehalf())
                                        <span class="text-slate-300"> · </span>
                                        {{ $hire->companyDisplayName() }}
                                    @endif
                                </p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div>
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
