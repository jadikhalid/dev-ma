@php
    $user = auth()->user();
    $offersUrl = ($user && $user->isTalent() && $user->isApproved())
        ? route('talent.jobs.index')
        : route('career.offers');
@endphp

<section class="mt-10 lg:mt-16" aria-labelledby="home-talent-banner-title">
    <div class="relative aspect-[937/305] overflow-hidden rounded-[28px] sm:rounded-[40px]">
        <img
            src="{{ asset('images/home/talent-world.jpg') }}?v=2"
            alt=""
            class="absolute inset-0 h-full w-full object-cover"
        >
        <h2 id="home-talent-banner-title" class="sr-only">
            {{ __('talenma.home.talent_banner_title') }} {{ __('talenma.home.talent_banner_accent') }}
        </h2>
        <a
            href="{{ $offersUrl }}"
            class="absolute left-[3%] top-[72%] h-[18%] w-[20%] rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
            aria-label="{{ __('talenma.home.talent_banner_cta') }}"
        ></a>
    </div>
</section>
