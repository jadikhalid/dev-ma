@php
    $tone = match ($directHire->statusTone()) {
        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
        'violet' => 'bg-violet-50 text-violet-800 border-violet-200',
        'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };
@endphp
<span
    id="direct-hire-status-badge"
    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $tone }}"
>
    {{ $directHire->statusLabel() }}
</span>
