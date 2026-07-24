@php
    $statusTone = [
        'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
        'in_progress' => 'bg-sky-50 text-sky-800 border-sky-200',
        'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
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
                'pending' => __('talenma.recruitment.status_pending'),
                'in_progress' => __('talenma.recruitment.status_in_progress'),
                'completed' => __('talenma.recruitment.status_completed'),
                'cancelled' => __('talenma.recruitment.status_cancelled'),
                'all' => __('talenma.recruitment.filter_all'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.recruitment.index', ['filter' => $key]) }}"
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
                    @php
                        $conversationId = $conversationIds[$req->company_user_id] ?? null;
                    @endphp
                    <article class="bg-white rounded-2xl border p-5 sm:p-6 space-y-4">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-semibold text-gray-900 truncate">{{ $req->subject }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $statusTone[$req->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                        {{ $req->statusLabel() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $req->company?->name ?? '—' }}
                                    · {{ $req->created_at?->translatedFormat('d M Y, H:i') }}
                                    @if ($req->professionSector)
                                        · {{ $req->professionSector->localizedName(app()->getLocale()) }}
                                    @endif
                                    @if ($req->talent)
                                        · {{ __('talenma.recruitment.inbox_talent', ['name' => $req->talent->name]) }}
                                    @endif
                                </p>
                            </div>
                            @if ($conversationId)
                                <a href="{{ route('inbox.show', $conversationId) }}" class="inline-flex shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                    {{ __('talenma.recruitment.admin_open_thread') }} →
                                </a>
                            @endif
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

                            <div>
                                <x-input-label for="status-{{ $req->id }}" :value="__('talenma.recruitment.admin_status')" />
                                <select
                                    id="status-{{ $req->id }}"
                                    name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                    required
                                >
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected($req->status === $status)>
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
