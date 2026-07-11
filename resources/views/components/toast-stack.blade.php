@props(['persistent' => false])

@php
    $initialToasts = [];

    if ($errors->any()) {
        foreach ($errors->all() as $message) {
            $initialToasts[] = [
                'type' => 'error',
                'message' => $message,
            ];
        }
    }

    if (session('toast_success')) {
        $initialToasts[] = [
            'type' => 'success',
            'message' => session('toast_success'),
        ];
    }

    if (session('toast_error')) {
        $initialToasts[] = [
            'type' => 'error',
            'message' => session('toast_error'),
        ];
    }

    $shouldRender = count($initialToasts) > 0 || $persistent;
@endphp

@if ($shouldRender)
    <div
        x-data="toastStack(@js($initialToasts))"
        @toast-push.window="push($event.detail.type ?? 'error', $event.detail.message)"
        class="fixed inset-0 z-[100] flex items-start justify-center p-4 sm:items-start sm:justify-end pointer-events-none"
        aria-live="polite"
        aria-atomic="true"
    >
        <div class="w-full max-w-md flex flex-col gap-3 pointer-events-none">
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="toast.visible"
                    x-cloak
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-full"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transform transition ease-in duration-250"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-full"
                    class="pointer-events-auto will-change-transform rounded-xl border shadow-lg px-4 py-3 flex items-start gap-3"
                    x-bind:class="toast.type === 'success'
                        ? 'bg-green-50 border-green-200 text-green-900'
                        : 'bg-red-50 border-red-200 text-red-900'"
                    role="alert"
                >
                    <div class="mt-0.5 shrink-0">
                        <svg x-show="toast.type === 'success'" class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <svg x-show="toast.type !== 'success'" class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="flex-1 text-sm font-medium leading-relaxed" x-text="toast.message"></div>
                    <button
                        type="button"
                        class="shrink-0 text-gray-400 hover:text-gray-600"
                        x-on:click="dismiss(toast.id)"
                        aria-label="{{ __('talenma.common.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>
@endif
