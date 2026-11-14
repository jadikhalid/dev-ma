<aside class="order-3 min-w-0 space-y-4 lg:order-none" data-talent-actions-column>
    {{-- Mes Activités (indicateurs) --}}
    <section class="space-y-3" data-talent-stats-block>
        <div class="flex min-h-[3.25rem] items-center rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4">
            <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.talent.stats.title') }}</h2>
        </div>

        @include('dashboard.partials.talent-stats-cards')
    </section>

    @include('dashboard.partials.talent-apps-section')
</aside>
