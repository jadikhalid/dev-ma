@php
    $statusBadge = fn (string $status) => match ($status) {
        'sent' => 'bg-emerald-50 text-emerald-700',
        'scheduled' => 'bg-sky-50 text-sky-800',
        'sending' => 'bg-indigo-50 text-indigo-800',
        'cancelled' => 'bg-slate-100 text-slate-600',
        default => 'bg-amber-50 text-amber-800',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.newsletter.admin_title') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.newsletter.admin_subtitle', ['count' => $recipientCount]) }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.newsletter.subscribers.index') }}" class="inline-flex justify-center px-4 py-2.5 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50">
                    {{ __('talenma.newsletter.manage_list') }}
                </a>
                <a href="{{ route('admin.newsletter.create') }}" class="inline-flex justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                    {{ __('talenma.newsletter.create') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
        @forelse ($newsletters as $newsletter)
            <a href="{{ route('admin.newsletter.show', $newsletter) }}" class="block rounded-xl border bg-white p-5 hover:border-indigo-300 transition">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-gray-900">{{ $newsletter->title }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $newsletter->subject }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $newsletter->created_at?->translatedFormat('d M Y, H:i') }}
                            @if ($newsletter->scheduled_at)
                                · {{ __('talenma.newsletter.scheduled_at_label') }} {{ $newsletter->scheduled_at->translatedFormat('d M Y, H:i') }}
                            @endif
                            @if ($newsletter->sent_at)
                                · {{ __('talenma.newsletter.sent_at_label') }} {{ $newsletter->sent_at->translatedFormat('d M Y, H:i') }}
                                · {{ __('talenma.newsletter.recipients_label', ['count' => $newsletter->recipient_count]) }}
                            @endif
                        </p>
                    </div>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge($newsletter->status) }}">
                        {{ $newsletter->statusLabel() }}
                    </span>
                </div>
            </a>
        @empty
            <div class="rounded-xl border bg-white p-8 text-center text-sm text-gray-500">
                {{ __('talenma.newsletter.empty') }}
            </div>
        @endforelse

        <div>{{ $newsletters->links() }}</div>
    </div>
</x-app-layout>
