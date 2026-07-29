@php
    $statusTone = [
        'pending' => 'bg-sky-50 text-sky-800 border-sky-200',
        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-200',
        'completed_successful' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'completed_unsuccessful' => 'bg-rose-50 text-rose-800 border-rose-200',
        'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
    ];
    $modeTone = [
        'named' => 'bg-violet-50 text-violet-800 border-violet-200',
        'open' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.recruitment.admin_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.recruitment.admin_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-wrap gap-2">
            @foreach ([
                'all' => __('talenma.recruitment.mode_filter_all'),
                'named' => __('talenma.recruitment.mode_named'),
                'open' => __('talenma.recruitment.mode_open'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.recruitment.index', ['filter' => $filter, 'mode' => $key]) }}"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold border transition',
                        'bg-violet-600 text-white border-violet-600' => $mode === $key,
                        'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $mode !== $key,
                    ])
                >
                    {{ $label }}
                    <span @class([
                        'text-xs px-1.5 py-0.5 rounded-full',
                        'bg-white/20' => $mode === $key,
                        'bg-gray-100 text-gray-600' => $mode !== $key,
                    ])>{{ $modeCounts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ([
                'pending' => __('talenma.recruitment.status_pending'),
                'in_progress' => __('talenma.recruitment.status_in_progress'),
                'completed_successful' => __('talenma.recruitment.status_completed_successful'),
                'completed_unsuccessful' => __('talenma.recruitment.status_completed_unsuccessful'),
                'all' => __('talenma.recruitment.filter_all'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.recruitment.index', ['filter' => $key, 'mode' => $mode]) }}"
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
                {{ __('talenma.recruitment.admin_empty') }}
            </div>
        @else
            <div class="space-y-4">
                @foreach ($requests as $req)
                    <article class="bg-white rounded-2xl border p-5 sm:p-6 space-y-4">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-semibold text-gray-900 truncate">{{ $req->subject }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $modeTone[$req->mode] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                        {{ $req->modeLabel() }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $statusTone[$req->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                        {{ $req->statusLabel() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $req->company?->name ?? '—' }}
                                    · {{ $req->created_at?->translatedFormat('d M Y, H:i') }}
                                    @if ($req->isNamed() && $req->talent)
                                        · {{ __('talenma.recruitment.inbox_talent', ['name' => $req->talent->name]) }}
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.recruitment.show', $req) }}" class="inline-flex shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                {{ __('talenma.recruitment.admin_open_thread') }} →
                            </a>
                        </div>

                        <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 text-sm text-gray-700 whitespace-pre-line">{{ $req->message }}</div>

                        @if (filled($req->admin_comment))
                            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3 text-sm text-indigo-950">
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">{{ __('talenma.recruitment.admin_comment_label') }}</p>
                                <p class="mt-1 whitespace-pre-line">{{ $req->admin_comment }}</p>
                                @if ($req->statusUpdatedBy || $req->status_updated_at)
                                    <p class="mt-2 text-xs text-indigo-700/80">
                                        {{ $req->statusUpdatedBy?->name }}
                                        @if ($req->status_updated_at)
                                            — {{ $req->status_updated_at->translatedFormat('d M Y, H:i') }}
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('admin.recruitment.status', $req) }}"
                            class="grid gap-3 sm:grid-cols-[14rem_1fr_auto] sm:items-end border-t border-gray-100 pt-4"
                        >
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="filter" value="{{ $filter }}">
                            <input type="hidden" name="mode" value="{{ $mode }}">

                            <div>
                                <x-input-label for="status-{{ $req->id }}" :value="__('talenma.recruitment.admin_status')" />
                                <select
                                    id="status-{{ $req->id }}"
                                    name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                    required
                                >
                                    @foreach ($statuses as $status)
                                        <option
                                            value="{{ $status }}"
                                            @selected($req->normalizeStatus() === $status)
                                        >
                                            {{ __('talenma.recruitment.status_'.$status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="comment-{{ $req->id }}" :value="__('talenma.recruitment.admin_comment')" />
                                <textarea
                                    id="comment-{{ $req->id }}"
                                    name="admin_comment"
                                    rows="2"
                                    maxlength="2000"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                    placeholder="{{ __('talenma.recruitment.admin_comment_placeholder') }}"
                                >{{ old('admin_comment', $req->admin_comment) }}</textarea>
                            </div>

                            <x-primary-button class="justify-center sm:mb-0.5">
                                {{ __('talenma.recruitment.admin_save') }}
                            </x-primary-button>
                        </form>
                    </article>
                @endforeach
            </div>

            <div>
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
