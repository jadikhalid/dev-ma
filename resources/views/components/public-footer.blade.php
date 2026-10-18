@props([
    'newsletter' => true,
])

@php
    $onCompanyHost = \App\Support\PortalHost::isCompanyHost();
    $talentFooter = auth()->check() && auth()->user()->isTalent();
    $footerLinks = [
        ['href' => route('career.offers'), 'label' => __('talenma.nav.offers_link'), 'active' => request()->routeIs('career.offers'), 'talent_hidden' => true],
        ['href' => route('career.tools'), 'label' => __('talenma.nav.career_tools'), 'active' => request()->routeIs('career.tools'), 'talent_hidden' => true],
        ['href' => route('career.how'), 'label' => __('talenma.nav.how_it_works'), 'active' => request()->routeIs('career.how'), 'talent_hidden' => true],
        ['href' => route('company.about'), 'label' => __('talenma.footer.about'), 'active' => request()->routeIs('company.about'), 'blank' => ! $onCompanyHost],
        ['href' => route('faq'), 'label' => __('talenma.footer.faq'), 'active' => request()->routeIs('faq')],
        ['href' => route('contact'), 'label' => __('talenma.footer.contact'), 'active' => request()->routeIs('contact')],
        ['href' => route('company.offer'), 'label' => __('talenma.footer.company_space'), 'active' => request()->routeIs('company.offer'), 'blank' => ! $onCompanyHost],
    ];

    if ($talentFooter) {
        $footerLinks = array_values(array_filter(
            $footerLinks,
            fn (array $link): bool => empty($link['talent_hidden']),
        ));
    }
@endphp

<footer class="bg-gray-900 text-gray-300">
    <div class="home-shell py-12">
        <div class="flex flex-col gap-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <x-brand-logo white size="sm" :linked="false" />
                <x-social-links variant="dark" />
            </div>
            <nav aria-label="{{ __('talenma.footer.nav_label') }}" class="grid grid-cols-2 gap-x-6 gap-y-3 sm:flex sm:flex-wrap sm:gap-x-8 sm:gap-y-3">
                @foreach ($footerLinks as $link)
                    <a
                        href="{{ $link['href'] }}"
                        @class([
                            'text-sm font-medium leading-snug transition hover:text-white',
                            'text-white' => $link['active'],
                            'text-gray-300' => ! $link['active'],
                        ])
                        @if (! empty($link['blank'])) target="_blank" rel="noopener noreferrer" @endif
                        @if ($link['active']) aria-current="page" @endif
                    >{{ $link['label'] }}</a>
                @endforeach
            </nav>
            @if ($newsletter)
            <div class="max-w-md">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('talenma.newsletter.public_subscribe_title') }}</p>
                <p class="mt-1 text-sm text-gray-400">{{ __('talenma.newsletter.public_subscribe_help') }}</p>
                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="mt-3 flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input
                        type="email"
                        name="email"
                        required
                        maxlength="255"
                        placeholder="{{ __('talenma.newsletter.public_subscribe_placeholder') }}"
                        class="w-full rounded-lg border-gray-700 bg-gray-800 text-gray-100 text-sm placeholder:text-gray-500 focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('email') }}"
                    >
                    <button type="submit" class="inline-flex justify-center whitespace-nowrap rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                        {{ __('talenma.newsletter.public_subscribe_submit') }}
                    </button>
                </form>
                @error('email')
                    <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>
        <div class="mt-10 pt-6 border-t border-gray-800 text-sm text-gray-500 flex flex-col sm:flex-row justify-between gap-2">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <p>&copy; {{ date('Y') }} {{ __('talenma.footer.copyright') }}</p>
                <a href="{{ route('privacy') }}" class="text-gray-400 underline hover:text-white transition">{{ __('talenma.footer.privacy') }}</a>
            </div>
            <div class="sm:text-right rounded-lg border border-gray-700/80 bg-gray-800/50 px-3.5 py-2 text-sm leading-relaxed">
                <p>
                    <span class="text-gray-500">{{ __('talenma.footer.developed_by') }}</span>
                    <a href="{{ route('jadi-digital') }}" class="font-semibold text-indigo-300 hover:text-indigo-200 transition" data-footer-jadi-link>{{ __('talenma.footer.jadi_digital') }}</a>
                </p>
            </div>
        </div>
    </div>
</footer>
