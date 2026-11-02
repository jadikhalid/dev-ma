@extends('layouts.public')

@section('title', __('talenma.company_offer.home_meta_title'))
@section('meta_description', __('talenma.company_offer.home_meta_description'))

@push('head')
    <link href="https://fonts.bunny.net/css?family=lexend:300,400,500,600,700,800&display=swap" rel="stylesheet">
    <style>
        .company-home-page {
            font-family: 'Lexend', system-ui, sans-serif;
            color: #111827;
            overflow-x: clip;
        }
        .company-home-page a { text-decoration: none; }
        .company-home-page .ch-grad-text {
            background: linear-gradient(120deg, #2563EB 0%, #5546F0 45%, #8B5CF6 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .company-home-page .ch-btn-primary {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            padding: 15px 24px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            background: linear-gradient(120deg, #2563EB 0%, #5546F0 45%, #8B5CF6 100%);
            color: #FFFFFF;
            box-shadow: 0 14px 28px -14px rgba(79, 70, 229, 0.8);
        }
        .company-home-page .ch-btn-secondary {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            padding: 15px 24px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            background: #FFFFFF;
            color: #5546F0;
            border: 1.5px solid #C7D2FE;
        }
        .company-home-page .ch-card {
            background: #FFFFFF;
            box-shadow: 0 1px 2px rgba(17, 24, 39, 0.06), 0 12px 32px -12px rgba(49, 46, 129, 0.22);
        }
        .company-home-page .ch-feature-icon {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 12px;
            background: #EEF0FF;
            color: #5546F0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .company-home-page .ch-faq-item[open] summary .ch-faq-icon { transform: rotate(45deg); }
        .company-home-page .ch-faq-item summary::-webkit-details-marker { display: none; }
        .company-home-page .ch-faq-item summary { list-style: none; cursor: pointer; }
    </style>
@endpush

@php
    use App\Support\PortalHost;

    $demoUrl = route('company.demo');
    $trialUrl = route('company.trial');
    $offersUrl = route('company.offers');
    $talentSpaceUrl = PortalHost::wwwRootUrl();
    $contactUrl = route('contact');
    $heroImage = asset('images/company/equipe-recrutement.jpg');
@endphp

@section('content')
<div class="company-home-page">

{{-- Hero --}}
<section data-company-offer-hero class="relative overflow-hidden">
    <div class="home-shell relative grid grid-cols-1 items-start gap-5 py-10 sm:gap-6 sm:py-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,560px)] lg:gap-x-16 lg:gap-y-7 lg:py-16">
        <span class="order-1 text-[13px] font-bold tracking-[0.12em] text-[#5546F0] lg:col-start-1 lg:row-start-1">RECRUTEZ DES TALENTS MAROCAINS</span>
        <h1 class="order-2 m-0 text-[2.35rem] font-extrabold leading-[1.04] tracking-[-0.035em] sm:text-5xl lg:col-start-1 lg:row-start-2 lg:text-[64px]">
            Ne cherchez plus ailleurs les talents <span class="ch-grad-text">faits pour vous.</span>
        </h1>

        <div class="order-3 relative mx-auto h-[360px] w-full max-w-[560px] sm:h-[400px] lg:col-start-2 lg:row-span-5 lg:row-start-1 lg:mx-0 lg:h-[500px] lg:max-w-none">
            <div class="absolute bottom-14 left-[8%] right-0 top-0 overflow-hidden rounded-2xl shadow-[0_40px_70px_-30px_rgba(30,27,75,0.55)] sm:bottom-16 sm:left-[14%] sm:rounded-[28px] lg:bottom-[70px] lg:left-[110px] lg:rounded-[32px]">
                <img
                    src="{{ $heroImage }}"
                    alt="Équipe de recrutement qui analyse des résultats"
                    class="block h-full w-full object-cover"
                    style="object-position: 55% 30%"
                    width="560"
                    height="430"
                >
            </div>
            <div class="absolute bottom-0 left-0 w-[min(100%,260px)] sm:w-[min(100%,300px)] lg:w-[min(100%,360px)]">
                <div class="ch-card flex flex-col gap-1.5 rounded-[16px] p-2.5 shadow-[0_30px_60px_-24px_rgba(30,27,75,0.55)] sm:gap-2 sm:rounded-[18px] sm:p-3 lg:gap-3 lg:rounded-[22px] lg:p-[18px]">
                    <div class="flex items-center gap-1.5 rounded-lg bg-[#F5F6FF] px-2 py-1.5 text-[10px] text-[#374151] sm:gap-2 sm:rounded-xl sm:px-2.5 sm:text-[11px] lg:gap-2.5 lg:px-3.5 lg:py-2.5 lg:text-[13px]">
                        <span class="flex text-[#5546F0]"><svg class="h-3 w-3 lg:h-4 lg:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg></span>
                        Santé · 5+ ans · Casablanca
                    </div>
                    <span class="text-[10px] font-bold sm:text-[11px] lg:text-[13px]" title="Talents inscrits / Entreprises inscrites">128 talents trouvés :: {{ $talentCount }}/{{ $companyCount }}</span>
                    @foreach ([
                        ['SE', '#5546F0', 'Médecin généraliste', '8 ans · Casablanca'],
                        ['IA', '#7C3AED', 'Infirmière diplômée', '6 ans · Mohammedia'],
                        ['KM', '#2563EB', 'Ingénieur DevOps', '7 ans · Full remote'],
                    ] as [$initials, $bg, $role, $meta])
                        <div class="flex items-center gap-1.5 sm:gap-2 lg:gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[9px] font-bold text-white sm:h-7 sm:w-7 sm:text-[10px] lg:h-9 lg:w-9 lg:text-[11px]" style="background: {{ $bg }}">{{ $initials }}</span>
                            <span class="flex min-w-0 flex-1 flex-col gap-px">
                                <span class="text-[11px] font-bold leading-tight sm:text-xs lg:text-sm">{{ $role }}</span>
                                <span class="text-[9px] leading-tight text-[#6B7280] sm:text-[10px] lg:text-xs">{{ $meta }} · <span class="font-semibold text-[#16A34A]">Vérifié</span></span>
                            </span>
                            <span class="rounded-md bg-[#EEF0FF] px-1.5 py-1 text-[9px] font-bold text-[#5546F0] sm:rounded-lg sm:px-2 sm:text-[10px] lg:rounded-[10px] lg:px-2.5 lg:py-1.5 lg:text-xs">Contacter</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="absolute right-1 top-3 rounded-full bg-[#EBBA6E] px-2 py-1 text-[10px] font-extrabold text-[#0D0B2B] shadow-[0_16px_30px_-14px_rgba(0,0,0,0.4)] sm:right-2 sm:top-4 sm:px-2.5 sm:py-1.5 sm:text-[11px] lg:bottom-24 lg:right-[-10px] lg:top-auto lg:px-4 lg:py-2.5 lg:text-[13px]">
                Remote · Visa · Local
            </div>
        </div>

        <p class="order-4 m-0 max-w-[580px] text-lg font-light leading-[1.55] text-[#4B5563] lg:col-start-1 lg:row-start-3 lg:text-xl">
            Des talents marocains vérifiés et un ATS pour les sourcer, les shortlister et les recruter.
        </p>
        <div class="order-5 flex flex-wrap gap-3 lg:col-start-1 lg:row-start-4">
            <a href="{{ $demoUrl }}" class="ch-btn-primary">
                Demander une démo
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </a>
            <a href="{{ $trialUrl }}" class="ch-btn-secondary">
                Essayez gratuitement
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </a>
        </div>
        <div class="order-6 flex flex-wrap gap-x-6 gap-y-3 lg:col-start-1 lg:row-start-5">
            <span class="flex items-center gap-2 text-sm text-[#374151]">
                <span class="flex text-[#5546F0]"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"></path><path d="M14 3v5h5M10 13h6M10 17h4"></path></svg></span>
                ATS intégré
            </span>
            <span class="flex items-center gap-2 text-sm text-[#374151]">
                <span class="flex text-[#5546F0]"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="8" width="18" height="13" rx="2"></rect><path d="M3 12h18M12 8v13M12 8c-2-4-6-4-6-1s4 1 6 1zM12 8c2-4 6-4 6-1s-4 1-6 1z"></path></svg></span>
                45 jours offerts
            </span>
            <span class="flex items-center gap-2 text-sm text-[#374151]">
                <span class="flex text-[#5546F0]"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"></path><path d="m9 12 2 2 4-4"></path></svg></span>
                Sans engagement
            </span>
        </div>
    </div>
</section>

{{-- ATS --}}
<section id="ats" class="relative overflow-hidden" style="background:linear-gradient(180deg,#F5F3FF 0%,#FFFFFF 100%)">
    <div aria-hidden="true" style="position:absolute;left:900px;top:-200px;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(165,180,252,1) 0%,rgba(255,255,255,0) 68%);opacity:0.45;pointer-events:none"></div>
    <div class="home-shell relative grid grid-cols-1 items-center gap-12 py-14 sm:py-16 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:gap-16 lg:py-20">
    <div class="relative flex flex-col gap-[22px]">
        <span class="self-start rounded-full bg-[#EBBA6E] px-3.5 py-2 text-[13px] font-extrabold tracking-[0.06em] text-[#0D0B2B]">ATS INCLUS DANS TOUTES LES FORMULES</span>
        <h2 class="m-0 text-4xl font-extrabold leading-[1.05] tracking-[-0.035em] lg:text-[50px]">
            Tous vos recrutements, <span class="ch-grad-text">un seul outil.</span>
        </h2>
        <p class="m-0 text-lg leading-[1.55] text-[#4B5563]">De l’annonce à l’embauche, sans changer d’outil.</p>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ([
                ['Publiez vos annonces', '<path d="M7 3h7l5 5v13H7z"></path><path d="M14 3v5h5M10 13h6M10 17h4"></path>'],
                ['Pipeline visuel', '<rect x="3" y="4" width="5" height="16" rx="1.5"></rect><rect x="10" y="4" width="5" height="11" rx="1.5"></rect><rect x="17" y="4" width="4" height="7" rx="1.5"></rect>'],
                ['Recrutez en équipe', '<circle cx="8" cy="9" r="3"></circle><circle cx="16" cy="9" r="3"></circle><path d="M2.5 19c.8-3 3-4.5 5.5-4.5s4.7 1.5 5.5 4.5M13 15c.9-.4 1.9-.5 3-.5 2.5 0 4.7 1.5 5.5 4.5"></path>'],
                ['Messagerie intégrée', '<path d="M4 5h16v11H9l-5 4z"></path>'],
                ['Sourcing et short-lists', '<circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path>'],
                ['Tableau de bord', '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"></path>'],
            ] as [$label, $icon])
                <span class="ch-card flex items-center gap-3 rounded-2xl px-4 py-3.5 text-[15px] font-bold">
                    <span class="ch-feature-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icon !!}</svg>
                    </span>
                    {{ $label }}
                </span>
            @endforeach
        </div>
        <div class="mt-1 flex gap-3">
            <a href="{{ $trialUrl }}" class="ch-btn-primary">
                Tester l’ATS gratuitement
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </a>
        </div>
    </div>

    <div class="relative">
        <div class="relative flex flex-col gap-4 rounded-[28px] border border-[#E8E9F0] bg-white p-[22px] shadow-[0_2px_4px_rgba(17,24,39,0.08),0_30px_60px_-24px_rgba(49,46,129,0.45)]">
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2.5">
                    <span class="flex h-[34px] w-[34px] items-center justify-center rounded-[10px] text-white" style="background:linear-gradient(120deg,#2563EB 0%,#5546F0 45%,#8B5CF6 100%)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"></path></svg>
                    </span>
                    <span class="text-base font-extrabold">Mon espace recrutement</span>
                </span>
                <span class="text-xs text-[#6B7280]">Cette semaine</span>
            </div>
            <div class="flex gap-2.5">
                @foreach ([['3', 'annonces en ligne'], ['42', 'candidatures'], ['6', 'entretiens planifiés']] as [$n, $lbl])
                    <span class="flex flex-1 flex-col gap-0.5 rounded-[14px] bg-[#F5F6FF] px-4 py-3.5">
                        <span class="text-2xl font-extrabold text-[#5546F0]">{{ $n }}</span>
                        <span class="text-xs text-[#6B7280]">{{ $lbl }}</span>
                    </span>
                @endforeach
            </div>
            <div class="relative flex flex-col gap-3.5 rounded-[18px] border border-[#EEF0F4] bg-white p-5">
                <div class="flex items-center justify-between">
                    <span class="flex flex-col gap-0.5">
                        <span class="text-xs text-[#6B7280]">Annonce</span>
                        <span class="text-base font-extrabold">Data Engineer · Full remote</span>
                    </span>
                    <span class="rounded-full bg-[#DCFCE7] px-2.5 py-1.5 text-xs font-bold text-[#166534]">En ligne</span>
                </div>
                <div class="flex flex-col gap-2.5 sm:flex-row">
                    <div class="flex flex-1 flex-col gap-2.5 rounded-2xl bg-[#F5F6FF] p-3">
                        <span class="flex items-center justify-between text-[13px] font-bold text-[#5546F0]">Reçues<span class="rounded-full bg-white px-2 py-0.5 text-xs text-[#374151]">12</span></span>
                        @foreach ([['YB', '#5546F0', 'Youssef B.', 'Casablanca'], ['SA', '#8B5CF6', 'Salma A.', 'Rabat']] as [$i, $c, $n, $city])
                            <div class="flex flex-col gap-1.5 rounded-xl border border-[#E8E9F0] bg-white p-3 shadow-[0_1px_2px_rgba(17,24,39,0.05)]">
                                <span class="flex items-center gap-2">
                                    <span class="flex h-[26px] w-[26px] items-center justify-center rounded-full text-[11px] font-bold text-white" style="background:{{ $c }}">{{ $i }}</span>
                                    <span class="text-[13px] font-bold">{{ $n }}</span>
                                </span>
                                <span class="text-xs text-[#6B7280]">{{ $city }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-1 flex-col gap-2.5 rounded-2xl bg-[#F5F6FF] p-3">
                        <span class="flex items-center justify-between text-[13px] font-bold text-[#7C3AED]">Présélection<span class="rounded-full bg-white px-2 py-0.5 text-xs text-[#374151]">4</span></span>
                        <div class="flex flex-col gap-1.5 rounded-xl border border-[#E8E9F0] bg-white p-3 shadow-[0_1px_2px_rgba(17,24,39,0.05)]">
                            <span class="flex items-center gap-2">
                                <span class="flex h-[26px] w-[26px] items-center justify-center rounded-full bg-[#2563EB] text-[11px] font-bold text-white">HA</span>
                                <span class="text-[13px] font-bold">Hamza A.</span>
                            </span>
                            <span class="text-xs text-[#6B7280]">Marrakech</span>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col gap-2.5 rounded-2xl bg-[#F5F6FF] p-3">
                        <span class="flex items-center justify-between text-[13px] font-bold text-[#2563EB]">Entretien<span class="rounded-full bg-white px-2 py-0.5 text-xs text-[#374151]">2</span></span>
                        <div class="flex flex-col gap-1.5 rounded-xl border border-[#E8E9F0] bg-white p-3 shadow-[0_1px_2px_rgba(17,24,39,0.05)]">
                            <span class="flex items-center gap-2">
                                <span class="flex h-[26px] w-[26px] items-center justify-center rounded-full bg-[#06B6D4] text-[11px] font-bold text-white">NK</span>
                                <span class="text-[13px] font-bold">Nora K.</span>
                            </span>
                            <span class="text-xs text-[#6B7280]">Tanger</span>
                        </div>
                    </div>
                </div>
                <span class="text-right text-[11px] text-[#9CA3AF]">Illustration — étapes et données fictives</span>
            </div>
        </div>
        <div class="absolute -left-3 bottom-10 hidden items-center gap-2.5 rounded-2xl bg-white px-3.5 py-3 shadow-[0_20px_40px_-18px_rgba(30,27,75,0.5)] sm:flex lg:-left-7">
            <span class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-[#06B6D4] text-[10px] font-bold text-white">NK</span>
            <span class="flex flex-col gap-px">
                <span class="text-[13px] font-bold">Nora K. → Entretien</span>
                <span class="text-[11px] text-[#6B7280]">Avis du manager : favorable</span>
            </span>
        </div>
    </div>
    </div>
</section>

{{-- Sourcing --}}
<section id="sourcing" class="relative overflow-hidden" style="background:#0D0B2B">
    <div aria-hidden="true" style="position:absolute;left:-200px;top:-260px;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,1) 0%,rgba(255,255,255,0) 68%);opacity:0.55;pointer-events:none"></div>
    <div aria-hidden="true" style="position:absolute;left:1000px;top:200px;width:620px;height:620px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,1) 0%,rgba(255,255,255,0) 68%);opacity:0.35;pointer-events:none"></div>
    <div class="home-shell relative grid grid-cols-1 items-center gap-12 py-14 sm:py-16 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)] lg:gap-16 lg:py-20">
    <div class="relative flex flex-col gap-5">
        <span style="font-size:13px;font-weight:700;letter-spacing:0.12em;color:#EBBA6E">SOURCING · DANS VOTRE ATS</span>
        <h2 class="m-0 text-4xl font-extrabold leading-[1.08] tracking-[-0.03em] text-white lg:text-[46px]">
            Sourcez et shortlistez <span class="text-[#EBBA6E]">vous-même.</span>
        </h2>
        <div class="flex flex-col gap-3">
            @foreach ([
                [1, false, 'L’ATS vous propose des talents adaptés à votre poste'],
                [2, false, 'Affinez avec les filtres'],
                [3, false, 'Ajoutez les meilleurs à votre short-list'],
                [4, true, 'Contactez-les directement'],
            ] as [$step, $gold, $text])
                <span class="flex items-center gap-3 text-base text-[#E0E7FF]">
                    <span @class([
                        'flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[13px] font-extrabold',
                        'text-white' => ! $gold,
                        'bg-[#EBBA6E] text-[#0D0B2B]' => $gold,
                    ]) @if(! $gold) style="background:linear-gradient(120deg,#2563EB 0%,#5546F0 45%,#8B5CF6 100%)" @endif>{{ $step }}</span>
                    {{ $text }}
                </span>
            @endforeach
        </div>
        <div class="mt-1.5 flex gap-3">
            <a href="{{ $trialUrl }}" class="inline-flex items-center gap-2 rounded-[14px] bg-white px-6 py-[15px] text-[15px] font-bold text-[#0D0B2B]">
                Essayer le sourcing
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </a>
        </div>
    </div>

    <div class="relative">
        <div class="grid grid-cols-1 gap-[18px] rounded-[28px] bg-white p-5 shadow-[0_40px_80px_-30px_rgba(0,0,0,0.6)] sm:grid-cols-[170px_minmax(0,1fr)]">
            <div class="flex flex-col gap-4 border-[#EEF0F4] py-1 sm:border-r sm:pr-4">
                <span class="flex items-center gap-2 text-sm font-extrabold text-[#111827]">
                    <span class="flex text-[#5546F0]"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18l-7 8v6l-4 2v-8z"></path></svg></span>
                    Filtres
                </span>
                @foreach ([
                    ['COMPÉTENCES', [['Python', true], ['SQL', true], ['Spark', false]]],
                    ['EXPÉRIENCE', [['3-5 ans', false], ['5+ ans', true]]],
                    ['MODE', [['Remote', true], ['Visa', false], ['Local', false]]],
                    ['DISPONIBILITÉ', [['Immédiate', true]]],
                ] as [$group, $tags])
                    <span class="flex flex-col gap-2">
                        <span class="text-[11px] font-bold tracking-[0.06em] text-[#6B7280]">{{ $group }}</span>
                        <span class="flex flex-wrap gap-1.5">
                            @foreach ($tags as [$tag, $on])
                                <span @class([
                                    'rounded-full px-2.5 py-1.5 text-xs font-semibold',
                                    'bg-[#5546F0] text-white' => $on,
                                    'border border-[#E5E7EB] bg-white text-[#374151]' => ! $on,
                                ])>{{ $tag }}</span>
                            @endforeach
                        </span>
                    </span>
                @endforeach
            </div>
            <div class="flex flex-col gap-2.5">
                <div class="flex items-center justify-between gap-3">
                    <span class="flex flex-col gap-px">
                        <span class="text-xs text-[#6B7280]">Talents proposés pour</span>
                        <span class="text-[15px] font-extrabold text-[#111827]">Data Engineer · Full remote</span>
                    </span>
                    <span class="rounded-full bg-[#EBBA6E] px-2.5 py-1.5 text-xs font-extrabold text-[#0D0B2B]">Short-list : 2</span>
                </div>
                @foreach ([
                    ['AM', '#5546F0', 'Amine M.', 'Data Engineer · 7 ans · Remote', '96 %', true],
                    ['LB', '#7C3AED', 'Leïla B.', 'Data Engineer · 6 ans · Remote', '91 %', true],
                    ['OT', '#2563EB', 'Omar T.', 'Data Analyst · 5 ans · Remote', '84 %', false],
                ] as [$i, $c, $n, $meta, $score, $listed])
                    <div @class([
                        'flex items-center gap-3 rounded-[14px] px-3.5 py-3',
                        'border border-[#C7D2FE] bg-[#F5F6FF]' => $listed,
                        'border border-[#E8E9F0] bg-white' => ! $listed,
                    ])>
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[11px] font-bold text-white" style="background:{{ $c }}">{{ $i }}</span>
                        <span class="flex min-w-0 flex-1 flex-col gap-px">
                            <span class="text-sm font-bold">{{ $n }}</span>
                            <span class="text-xs text-[#6B7280]">{{ $meta }}</span>
                        </span>
                        <span class="text-sm font-extrabold text-[#5546F0]">{{ $score }}</span>
                        <span @class([
                            'rounded-[10px] px-2.5 py-1.5 text-xs font-bold',
                            'bg-[#DCFCE7] text-[#166534]' => $listed,
                            'bg-[#5546F0] text-white' => ! $listed,
                        ])>{{ $listed ? '✓ Short-list' : '+ Short-list' }}</span>
                    </div>
                @endforeach
                <span class="text-right text-[11px] text-[#9CA3AF]">Illustration — données fictives</span>
            </div>
        </div>
    </div>
    </div>
</section>

{{-- Plateforme --}}
<section class="home-shell flex flex-col gap-10 py-14 sm:py-20 lg:py-24">
    <div class="flex flex-col items-center gap-2.5 text-center">
        <span style="font-size:13px;font-weight:700;letter-spacing:0.12em;color:#5546F0">NOTRE PLATEFORME</span>
        <h2 class="m-0 text-3xl font-extrabold tracking-[-0.03em] lg:text-[44px]">Cinq façons de recruter votre prochain talent</h2>
    </div>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,3.2fr)_minmax(0,1fr)]">
        <div class="flex flex-col gap-3.5 rounded-[28px] border-2 border-[#C7D2FE] bg-[#EEF0FF] p-6">
            <span class="flex items-center gap-2.5">
                <span class="flex h-[30px] w-[30px] items-center justify-center rounded-[9px] text-white" style="background:linear-gradient(120deg,#2563EB 0%,#5546F0 45%,#8B5CF6 100%)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"></path></svg>
                </span>
                <span class="text-sm font-extrabold tracking-[0.08em] text-[#5546F0]">EN AUTONOMIE, DANS VOTRE ATS</span>
            </span>
            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['Sourcing', 'Trouvez et shortlistez vos talents.', '<path d="M3 5h18l-7 8v6l-4 2v-8z"></path>'],
                    ['Catalogue', 'Des profils vérifiés à parcourir.', '<circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path>'],
                    ['Candidature directe', 'Proposez un poste à un talent.', '<path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"></path>'],
                    ['Annonces', 'Publiez et suivez vos offres.', '<path d="M7 3h7l5 5v13H7z"></path><path d="M14 3v5h5M10 13h6M10 17h4"></path>'],
                ] as [$title, $text, $icon])
                    <div class="ch-card flex flex-col gap-3 rounded-[22px] border border-[#E8E9F0] p-[26px]">
                        <span class="flex h-12 w-12 items-center justify-center rounded-[14px] text-white" style="background:linear-gradient(120deg,#2563EB 0%,#5546F0 45%,#8B5CF6 100%)">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icon !!}</svg>
                        </span>
                        <span class="text-xl font-extrabold text-[#111827]">{{ $title }}</span>
                        <span class="text-[15px] leading-normal text-[#4B5563]">{{ $text }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="relative flex flex-col gap-3.5 overflow-hidden rounded-[28px] bg-[#0D0B2B] p-6">
            <div aria-hidden="true" style="position:absolute;left:60px;top:-220px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,1) 0%,rgba(255,255,255,0) 68%);opacity:0.5;pointer-events:none"></div>
            <span class="relative text-sm font-extrabold tracking-[0.08em] text-[#EBBA6E]">AVEC NOTRE ÉQUIPE</span>
            <div class="relative flex flex-col">
                <a href="{{ $offersUrl }}" class="flex flex-col gap-3 rounded-[22px] border border-white/15 bg-white/[0.07] p-[26px]">
                    <span class="flex h-12 w-12 items-center justify-center rounded-[14px] bg-[#EBBA6E] text-[#0D0B2B]">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"></path><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"></path></svg>
                    </span>
                    <span class="text-xl font-extrabold text-white">Intermédiation</span>
                    <span class="text-[15px] leading-normal text-[#C7D2FE]">Nous recrutons pour vous, jusqu’à la mise en relation.</span>
                    <span class="flex items-center gap-1.5 text-sm font-bold text-[#EBBA6E]">
                        En savoir plus
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Offres --}}
<section style="background:#F7F7FB">
    <div class="home-shell flex flex-col gap-10 py-14 sm:py-20 lg:py-24">
    <div class="flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-end">
        <div class="flex flex-col gap-2.5">
            <span style="font-size:13px;font-weight:700;letter-spacing:0.12em;color:#5546F0">NOS OFFRES</span>
            <h2 class="m-0 text-3xl font-extrabold tracking-[-0.03em] lg:text-[44px]">Une formule pour chaque façon de recruter</h2>
        </div>
        <a href="{{ $offersUrl }}" class="ch-btn-secondary shrink-0">
            Voir toutes nos offres
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
        </a>
    </div>
    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <div class="ch-card flex flex-col gap-4 rounded-[26px] border border-[#E8E9F0] p-8">
            <span class="text-2xl font-extrabold">Essentiel</span>
            <div class="flex flex-col gap-2.5">
                @foreach (['ATS intégré', 'Catalogue et candidature directe', '3 annonces actives'] as $item)
                    <span class="flex items-center gap-2.5 text-[15px]">
                        <span class="flex text-[#5546F0]"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"></path></svg></span>
                        {{ $item }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="flex flex-col gap-4 rounded-[26px] p-8 text-white shadow-[0_2px_4px_rgba(17,24,39,0.08),0_30px_60px_-24px_rgba(49,46,129,0.45)]" style="background:linear-gradient(120deg,#2563EB 0%,#5546F0 45%,#8B5CF6 100%)">
            <span class="flex items-center justify-between">
                <span class="text-2xl font-extrabold">Pro</span>
                <span class="rounded-full bg-[#EBBA6E] px-2.5 py-1 text-[11px] font-extrabold text-[#0D0B2B]">RECOMMANDÉ</span>
            </span>
            <div class="flex flex-col gap-2.5">
                @foreach (['Tout le plan Essentiel', 'Intermédiation incluse', '5 annonces actives'] as $item)
                    <span class="flex items-center gap-2.5 text-[15px]">
                        <span class="flex text-white"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"></path></svg></span>
                        {{ $item }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="ch-card flex flex-col gap-4 rounded-[26px] border border-[#E8E9F0] p-8">
            <span class="text-2xl font-extrabold">Sur mesure</span>
            <div class="flex flex-col gap-2.5">
                @foreach (['Volume d’annonces adapté', 'Plusieurs sièges', 'Intégrations RH'] as $item)
                    <span class="flex items-center gap-2.5 text-[15px]">
                        <span class="flex text-[#5546F0]"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"></path></svg></span>
                        {{ $item }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 items-center gap-8 rounded-3xl border-2 border-[#EBBA6E] px-6 py-7 sm:grid-cols-[minmax(0,1fr)_auto] sm:px-9" style="background:linear-gradient(180deg,#FFF8EA 0%,#FFFFFF 100%)">
        <span class="flex items-center gap-[18px]">
            <span class="flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-2xl bg-[#EBBA6E] text-[#0D0B2B]">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="8" width="18" height="13" rx="2"></rect><path d="M3 12h18M12 8v13M12 8c-2-4-6-4-6-1s4 1 6 1zM12 8c2-4 6-4 6-1s-4 1-6 1z"></path></svg>
            </span>
            <span class="flex flex-col gap-1">
                <span class="text-[13px] font-extrabold tracking-[0.1em] text-[#A16207]">ESSAI GRATUIT</span>
                <span class="text-xl font-extrabold sm:text-[22px]">Testez la plateforme 45 jours, offerts. Sans engagement.</span>
            </span>
        </span>
        <a href="{{ $trialUrl }}" class="inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-[14px] bg-[#0D0B2B] px-6 py-[15px] text-[15px] font-semibold text-white">
            Essayez gratuitement
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
        </a>
    </div>
    </div>
</section>

{{-- FAQ --}}
<section class="home-shell grid grid-cols-1 gap-12 py-14 sm:py-20 lg:grid-cols-[minmax(0,22rem)_minmax(0,1fr)] lg:gap-16 lg:py-24">
    <div class="flex flex-col gap-3.5">
        <span style="font-size:13px;font-weight:700;letter-spacing:0.12em;color:#5546F0">FAQ</span>
        <h2 class="m-0 text-3xl font-extrabold leading-tight tracking-[-0.03em] lg:text-[40px]">Questions fréquentes</h2>
        <p class="m-0 text-base text-[#4B5563]">Une autre question ? <a href="{{ $contactUrl }}" class="font-bold text-[#5546F0]">Contactez-nous</a></p>
    </div>
    <div class="flex flex-col gap-3" x-data="{ open: 0 }">
        @foreach ([
            [
                'Pourquoi vos tarifs ne sont-ils pas affichés ?',
                'Chaque entreprise recrute différemment. Votre tarif dépend de votre volume de recrutement, du nombre d’annonces et des services choisis : nous le construisons avec vous après une démo de la plateforme.',
            ],
            [
                'Quelle formule choisir ?',
                'Essentiel convient à une équipe qui recrute en autonomie avec l’ATS. Pro ajoute l’intermédiation pour les postes stratégiques. Sur mesure s’adresse aux organisations avec un volume important ou des besoins d’intégrations RH.',
            ],
            [
                'Que comprend l’essai gratuit ?',
                '45 jours d’accès complet à la plateforme : ATS, sourcing, catalogue, candidature directe et publication d’annonces. Sans carte bancaire et sans engagement.',
            ],
            [
                'Qu’est-ce que l’ATS intégré ?',
                'Un outil de suivi des recrutements inclus dans toutes les formules : publication d’annonces, pipeline visuel, collaboration d’équipe, messagerie et tableau de bord — de l’annonce à l’embauche.',
            ],
            [
                'Quelle différence entre le sourcing et l’intermédiation ?',
                'Le sourcing vous laisse shortlister vous-même dans l’ATS. L’intermédiation confie la recherche et la présélection à notre équipe, jusqu’à la mise en relation.',
            ],
        ] as $i => [$q, $a])
            <div class="rounded-[18px] border border-[#E8E9F0] bg-white px-[26px] py-[22px]">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-4 text-left text-lg font-bold"
                    @click="open = open === {{ $i }} ? null : {{ $i }}"
                    :aria-expanded="(open === {{ $i }}).toString()"
                >
                    <span>{{ $q }}</span>
                    <span class="shrink-0 text-[22px] text-[#5546F0]" x-text="open === {{ $i }} ? '−' : '+'"></span>
                </button>
                <p class="mt-2.5 m-0 text-base leading-relaxed text-[#4B5563]" x-show="open === {{ $i }}" x-cloak>{{ $a }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- CTA final --}}
<section class="home-shell pb-14 sm:pb-20 lg:pb-24">
    <div class="relative flex flex-col items-start justify-between gap-10 overflow-hidden rounded-[36px] px-8 py-12 lg:flex-row lg:items-center lg:px-16 lg:py-14" style="background:linear-gradient(120deg,#2563EB 0%,#5546F0 45%,#8B5CF6 100%)">
        <div aria-hidden="true" style="position:absolute;left:860px;top:-260px;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(235,186,110,1) 0%,rgba(255,255,255,0) 68%);opacity:0.4;pointer-events:none"></div>
        <div class="relative flex max-w-[660px] flex-col gap-3">
            <h2 class="m-0 text-3xl font-extrabold leading-tight tracking-[-0.03em] text-white lg:text-[40px]">
                Votre prochain talent est peut-être <span class="text-[#EBBA6E]">déjà sur la plateforme.</span>
            </h2>
            <p class="m-0 text-[17px] text-[#E0E7FF]">Échangeons sur vos besoins de recrutement.</p>
        </div>
        <div class="relative flex flex-shrink-0 flex-wrap gap-3">
            <a href="{{ $demoUrl }}" class="rounded-[14px] bg-white px-[26px] py-[17px] text-base font-bold text-[#0D0B2B]">Demander une démo</a>
            <a href="{{ $trialUrl }}" class="rounded-[14px] border-[1.5px] border-white/55 px-[26px] py-4 text-base font-semibold text-white">Essayez gratuitement</a>
        </div>
    </div>
</section>

</div>
@endsection
