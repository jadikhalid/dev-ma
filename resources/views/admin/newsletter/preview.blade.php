<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.newsletter.preview_title') }}</h2>
    </x-slot>
    <div class="py-8 max-w-3xl mx-auto px-4">
        <div class="rounded-xl overflow-hidden border bg-white shadow-sm">
            {!! $previewHtml !!}
        </div>
    </div>
</x-app-layout>
