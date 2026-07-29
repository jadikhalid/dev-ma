@php
    $tone = match ($recruitment->status) {
        'pending' => 'bg-sky-50 text-sky-800 border-sky-200',
        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-200',
        'completed_successful', 'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'completed_unsuccessful', 'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp
<a
    href="{{ route('sourcing.show', $recruitment) }}"
    class="group block rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-indigo-200"
>
    <div class="flex flex-col gap-1.5 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $recruitment->displayTitle() }}</span>
                <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $tone }}">
                    {{ $recruitment->statusLabel() }}
                </span>
            </div>
            @if (filled($recruitment->admin_comment))
                <p class="mt-1.5 text-xs text-slate-600 line-clamp-2">{{ $recruitment->admin_comment }}</p>
            @endif
        </div>
        <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5">{{ $recruitment->created_at?->translatedFormat('d M Y') }}</time>
    </div>
</a>
