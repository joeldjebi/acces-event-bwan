@extends('layouts.public', [
    'title' => $page->seo_title ?: $page->name,
    'metaDescription' => $page->seo_description ?: $page->summary,
    'canonicalUrl' => route('about'),
    'bodyClass' => 'page-clean',
])

@php($paragraphs = collect($page->bodyParagraphs()))

@section('content')
    {{-- En-tête centré --}}
    <section class="page-intro">
        <div class="page-intro-copy hero-copy">
            <p class="kicker">{{ $page->eyebrow }}</p>
            <h1>{{ $page->heading }}</h1>
            <p class="sol-lead">{{ $page->summary }}</p>
        </div>
    </section>

    {{-- Grande photo qui s'agrandit au défilement --}}
    <section class="showcase" data-scroll-scale>
        <figure class="showcase-frame">
            <img src="{{ asset('images/hero/acces-event-hero.png') }}" alt="Équipes ACCÈS EVENT au contrôle d'accès d'un événement">
        </figure>
    </section>

    {{-- Notre histoire : la conviction en grand, le reste du texte à côté --}}
    @if ($paragraphs->isNotEmpty())
        <section class="section-clean story">
            <div class="story-lead" data-reveal>
                <p class="kicker">Notre histoire</p>
                <p>{{ $paragraphs->first() }}</p>
            </div>
            @if ($paragraphs->count() > 1)
                <div class="story-body" data-reveal style="--reveal-delay: 120ms">
                    @foreach ($paragraphs->slice(1) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- Chiffres clés --}}
    <section class="big-figures" aria-label="ACCÈS EVENT en chiffres">
        <div data-reveal>
            <strong data-count-to="{{ $solutionCount }}">{{ $solutionCount }}</strong>
            <span>domaines métier couverts</span>
        </div>
        <div data-reveal style="--reveal-delay: 100ms">
            <strong data-count-to="{{ $applicationCount }}">{{ $applicationCount }}</strong>
            <span>applications connectées</span>
        </div>
        <div data-reveal style="--reveal-delay: 200ms">
            <strong>1</strong>
            <span>administration unique</span>
        </div>
    </section>

    {{-- Valeurs : bandeau bleu nuit --}}
    <section class="band-dark">
        <div class="band-dark-inner">
            <header class="section-head" data-reveal>
                <div>
                    <p class="kicker">Ce qui nous guide</p>
                    <h2>Une technologie de niveau mondial, pensée pour le terrain.</h2>
                </div>
            </header>

            <ul class="feature-cards">
                <li data-reveal>
                    <x-icon name="globe" />
                    <h3>Terrain africain</h3>
                    <p>Paiement mobile, distribution de proximité et gestion des flux locaux.</p>
                </li>
                <li data-reveal style="--reveal-delay: 90ms">
                    <x-icon name="layers" />
                    <h3>Technologie intégrée</h3>
                    <p>Une suite qui remplace les outils éparpillés.</p>
                </li>
                <li data-reveal style="--reveal-delay: 180ms">
                    <x-icon name="shield" />
                    <h3>Sécurité des revenus</h3>
                    <p>Contrôle centralisé, anti-fraude et suivi en temps réel.</p>
                </li>
            </ul>
        </div>
    </section>

    @include('partials.cta-clean', [
        'eyebrow' => 'Travaillons ensemble',
        'heading' => "Un projet d'événement en Afrique\u{00A0}?",
        'text' => 'Promoteurs, salons, festivals, institutions : parlons de vos enjeux terrain.',
        'label' => 'Prendre contact',
    ])
@endsection
