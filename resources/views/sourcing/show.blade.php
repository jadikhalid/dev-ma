@php
    $statusTone = match ($recruitment->status) {
        'pending' => 'bg-sky-50 text-sky-800 border-sky-200',
        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-200',
        'completed_successful', 'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'completed_unsuccessful', 'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
    $isNamed = $recruitment->isNamed();
    $backRoute = $isStaff
        ? route('admin.recruitment.index')
        : route('sourcing.index');
    $backLabel = $isStaff
        ? __('talenma.recruitment.show_back_admin')
        : __('talenma.recruitment.show_back');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.recruitment.show_title') }}</h2>
                <p class="text-sm text-gray-500 truncate">{{ $recruitment->displayTitle() }}</p>
                @if ($isStaff)
                    <p class="text-sm text-gray-500">{{ $recruitment->companyDisplayName() }}</p>
                @endif
            </div>
            <span id="sourcing-status-badge" class="inline-flex items-center rounded-md border px-2 py-1 text-xs font-bold uppercase tracking-wider {{ $statusTone }}">
                {{ $recruitment->statusLabel() }}
            </span>
        </div>
        <div class="mt-2">
            <a href="{{ $backRoute }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                {{ $backLabel }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] gap-5 lg:gap-6 items-start">
            <div class="space-y-5 min-w-0">
                {{-- Contexte --}}
                <section @class([
                    'overflow-hidden rounded-2xl shadow-sm',
                    'border border-violet-100 bg-gradient-to-br from-violet-50/80 via-white to-slate-50 shadow-violet-600/5' => $isNamed,
                    'border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 shadow-indigo-600/5' => ! $isNamed,
                ])>
                    <div @class([
                        'border-b bg-white/60 px-5 py-4 sm:px-6',
                        'border-violet-100/80' => $isNamed,
                        'border-indigo-100/80' => ! $isNamed,
                    ])>
                        <div class="flex items-start gap-3 min-w-0">
                            <span @class([
                                'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-white shadow-sm',
                                'bg-violet-600 shadow-violet-600/20' => $isNamed,
                                'bg-indigo-600 shadow-indigo-600/20' => ! $isNamed,
                            ]) aria-hidden="true">
                                @if ($isNamed)
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <p @class([
                                    'text-xs font-semibold uppercase tracking-wide',
                                    'text-violet-700/80' => $isNamed,
                                    'text-indigo-700/80' => ! $isNamed,
                                ])>{{ __('talenma.recruitment.context_label') }}</p>
                                <h3 class="mt-0.5 text-base sm:text-lg font-bold tracking-tight text-slate-900">{{ $recruitment->subject }}</h3>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span @class([
                                        'inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider',
                                        'bg-violet-50 text-violet-800 border-violet-200' => $isNamed,
                                        'bg-indigo-50 text-indigo-800 border-indigo-200' => ! $isNamed,
                                    ])>
                                        {{ $recruitment->modeLabel() }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        {{ $recruitment->created_at?->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @if ($isNamed && $recruitment->talent)
                                        <span class="text-xs text-slate-500">
                                            · {{ __('talenma.recruitment.inbox_talent', ['name' => $recruitment->talent->name]) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <blockquote @class([
                            'relative overflow-hidden rounded-xl border border-slate-200 px-5 py-5 sm:px-6 sm:py-6 shadow-sm ring-1 ring-slate-900/5',
                            'bg-gradient-to-br from-slate-50 via-white to-violet-50/40' => $isNamed,
                            'bg-gradient-to-br from-slate-50 via-white to-indigo-50/40' => ! $isNamed,
                        ])>
                            <span @class([
                                'absolute left-0 top-0 bottom-0 w-1',
                                'bg-violet-500' => $isNamed,
                                'bg-indigo-500' => ! $isNamed,
                            ]) aria-hidden="true"></span>
                            <span @class([
                                'select-none pointer-events-none absolute top-3 right-4 text-6xl leading-none font-serif',
                                'text-violet-200/80' => $isNamed,
                                'text-indigo-200/80' => ! $isNamed,
                            ]) aria-hidden="true">„</span>
                            <p class="relative text-[0.95rem] sm:text-base text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $recruitment->message }}</p>
                        </blockquote>
                    </div>
                </section>

                {{-- Historique --}}
                <section class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm shadow-slate-900/5 space-y-5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.recruitment.status_panel_title') }}</h3>
                            <p class="text-xs text-slate-500">{{ __('talenma.recruitment.history_subtitle') }}</p>
                        </div>
                    </div>

                    <ol id="sourcing-status-history" class="relative space-y-0 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-slate-200">
                        @forelse ($recruitment->statusEvents as $event)
                            @include('sourcing._status-event', [
                                'event' => $event,
                                'recruitment' => $recruitment,
                                'isStaff' => $isStaff,
                            ])
                        @empty
                            <li data-history-empty class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-6 text-center text-sm text-slate-500">
                                {{ __('talenma.recruitment.history_empty') }}
                            </li>
                        @endforelse
                    </ol>

                    @php
                        $historyHasComment = $recruitment->statusEvents->contains(fn ($event) => filled($event->comment));
                    @endphp
                    @if (! $isStaff && ! $historyHasComment && ! filled($recruitment->admin_comment))
                        <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-3 text-sm text-slate-500">
                            {{ __('talenma.recruitment.status_no_comment') }}
                        </p>
                    @endif

                    @if ($isStaff && count($statuses) > 0)
                        <div
                            id="sourcing-status-form-card"
                            class="relative"
                            x-data="sourcingStatusForm({
                                currentStatus: @js($recruitment->normalizeStatus()),
                                messages: {
                                    inProgressTitle: @js(__('talenma.recruitment.admin_status_confirm_in_progress_title')),
                                    inProgressBody: @js(__('talenma.recruitment.admin_status_confirm_in_progress_body')),
                                    closedSuccessfulTitle: @js(__('talenma.recruitment.admin_status_confirm_closed_successful_title')),
                                    closedSuccessfulBody: @js(__('talenma.recruitment.admin_status_confirm_closed_successful_body')),
                                    closedUnsuccessfulTitle: @js(__('talenma.recruitment.admin_status_confirm_closed_unsuccessful_title')),
                                    closedUnsuccessfulBody: @js(__('talenma.recruitment.admin_status_confirm_closed_unsuccessful_body')),
                                    confirmBtn: @js(__('talenma.recruitment.admin_status_confirm_btn')),
                                    confirmCancel: @js(__('talenma.recruitment.admin_status_confirm_cancel')),
                                    error: @js(__('talenma.recruitment.admin_status_error')),
                                    networkError: @js(__('talenma.common.network_error')),
                                    statusRequired: @js(__('talenma.recruitment.admin_status_required')),
                                    keepInProgress: @js(__('talenma.recruitment.admin_status_keep_in_progress')),
                                },
                            })"
                        >
                            <form
                                method="POST"
                                action="{{ route('admin.recruitment.status', $recruitment) }}"
                                class="space-y-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4"
                                @submit.prevent="requestSubmit($event)"
                                novalidate
                            >
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="redirect_to" value="show">

                                <div>
                                    <x-input-label for="status-{{ $recruitment->id }}" :value="__('talenma.recruitment.admin_status')" />
                                    <select
                                        id="status-{{ $recruitment->id }}"
                                        name="status"
                                        x-ref="status"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                        required
                                    >
                                        @if ($recruitment->normalizeStatus() === 'in_progress')
                                            <option value="in_progress" selected>
                                                {{ __('talenma.recruitment.admin_status_keep_in_progress') }}
                                            </option>
                                        @endif
                                        @foreach ($statuses as $status)
                                            <option
                                                value="{{ $status }}"
                                                @selected($recruitment->normalizeStatus() === $status)
                                            >
                                                {{ __('talenma.recruitment.status_'.$status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="comment-{{ $recruitment->id }}" :value="__('talenma.recruitment.admin_comment')" />
                                    <textarea
                                        id="comment-{{ $recruitment->id }}"
                                        name="admin_comment"
                                        rows="3"
                                        maxlength="2000"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                        placeholder="{{ __('talenma.recruitment.admin_comment_placeholder') }}"
                                    >{{ old('admin_comment', $recruitment->admin_comment) }}</textarea>
                                </div>

                                <x-primary-button type="submit" x-bind:disabled="loading">
                                    {{ __('talenma.recruitment.admin_save') }}
                                </x-primary-button>
                            </form>

                            <div
                                x-show="confirming"
                                x-cloak
                                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4"
                                @keydown.escape.window="closeConfirm()"
                            >
                                <div
                                    class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-200"
                                    @click.outside="closeConfirm()"
                                    role="dialog"
                                    aria-modal="true"
                                >
                                    <h3 class="text-base font-semibold text-slate-900" x-text="confirmTitle"></h3>
                                    <p class="mt-2 text-sm text-slate-600" x-text="confirmBody"></p>
                                    <div class="mt-5 flex flex-wrap justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50"
                                            @click="closeConfirm()"
                                            :disabled="loading"
                                            x-text="messages.confirmCancel"
                                        ></button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 rounded-lg border border-transparent bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                                            @click="confirmSubmit()"
                                            :disabled="loading"
                                            x-text="messages.confirmBtn"
                                        ></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            <div class="min-w-0">
                @include('sourcing._chat', ['recruitment' => $recruitment, 'sidebar' => true])
            </div>
        </div>
    </div>
</x-app-layout>
