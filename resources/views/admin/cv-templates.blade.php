<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.promo.admin_cv_templates_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.promo.admin_cv_templates_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($templates as $template)
                <article class="rounded-2xl border border-gray-100 bg-white p-4 flex gap-4" data-cv-template-promo="{{ $template['key'] }}">
                    <img
                        src="{{ $template['preview_url'] }}"
                        alt="{{ $template['label'] }}"
                        class="h-32 w-24 shrink-0 rounded-lg object-contain bg-slate-50 ring-1 ring-slate-200"
                        loading="lazy"
                    >
                    <div class="min-w-0 flex-1 space-y-2">
                        <h3 class="text-base font-semibold text-gray-900">{{ $template['label'] }}</h3>
                        <x-share-link
                            :url="$template['promo_url']"
                            :title="$template['share_title']"
                            :label="__('talenma.promo.admin_link_label')"
                        />
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</x-app-layout>
