@php
    $statusBadge = match ($newsletter->status) {
        'sent' => 'bg-emerald-50 text-emerald-700',
        'scheduled' => 'bg-sky-50 text-sky-800',
        'sending' => 'bg-indigo-50 text-indigo-800',
        'cancelled' => 'bg-slate-100 text-slate-600',
        default => 'bg-amber-50 text-amber-800',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ $newsletter->title }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $newsletter->subject }}</p>
                <div class="mt-2 flex flex-wrap gap-2 items-center">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">{{ $newsletter->statusLabel() }}</span>
                    <span class="text-xs text-gray-500">{{ __('talenma.newsletter.admin_subtitle', ['count' => $recipientCount]) }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($newsletter->isEditable())
                    <a href="{{ route('admin.newsletter.edit', $newsletter) }}" class="inline-flex px-4 py-2 border rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('talenma.newsletter.edit') }}</a>
                @endif
                <a href="{{ route('admin.newsletter.index') }}" class="inline-flex px-4 py-2 text-sm font-medium text-indigo-700 hover:text-indigo-900">← {{ __('talenma.newsletter.back') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if ($newsletter->isEditable())
            <div class="rounded-2xl border bg-white p-5 sm:p-6 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wide text-slate-600">{{ __('talenma.newsletter.send_actions') }}</h3>
                <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                    <form method="POST" action="{{ route('admin.newsletter.send', $newsletter) }}" onsubmit="return confirm(@js(__('talenma.newsletter.send_confirm')))">
                        @csrf
                        <button type="submit" class="inline-flex px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                            {{ __('talenma.newsletter.send_now') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.newsletter.schedule', $newsletter) }}" class="flex flex-wrap gap-2 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-600" for="scheduled_at">{{ __('talenma.newsletter.field_scheduled_at') }}</label>
                            <input id="scheduled_at" type="datetime-local" name="scheduled_at" required class="mt-1 rounded-lg border-gray-300 text-sm" value="{{ old('scheduled_at') }}">
                        </div>
                        <button type="submit" class="inline-flex px-4 py-2.5 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50">
                            {{ __('talenma.newsletter.schedule') }}
                        </button>
                    </form>
                    @if ($newsletter->isCancellable())
                        <form method="POST" action="{{ route('admin.newsletter.cancel', $newsletter) }}">
                            @csrf
                            <button type="submit" class="inline-flex px-4 py-2.5 border text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50">
                                {{ __('talenma.newsletter.cancel_schedule') }}
                            </button>
                        </form>
                    @endif
                </div>
                <x-input-error :messages="$errors->get('scheduled_at')" />
            </div>
        @endif

        <div class="rounded-2xl border bg-white overflow-hidden">
            <div class="px-5 py-3 border-b bg-slate-50">
                <h3 class="text-sm font-bold text-slate-700">{{ __('talenma.newsletter.preview_title') }}</h3>
            </div>
            <div class="p-4 bg-slate-100">
                <div class="mx-auto max-w-[560px] rounded-xl overflow-hidden border bg-white shadow-sm">
                    {!! $previewHtml !!}
                </div>
            </div>
        </div>

        @if (! in_array($newsletter->status, ['sent', 'sending'], true))
            <form method="POST" action="{{ route('admin.newsletter.destroy', $newsletter) }}" onsubmit="return confirm(@js(__('talenma.newsletter.delete_confirm')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-900">{{ __('talenma.newsletter.delete') }}</button>
            </form>
        @endif
    </div>
</x-app-layout>
