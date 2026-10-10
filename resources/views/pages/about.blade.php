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
            <img src="{{ site_image('about.image') }}" alt="{{ site_text('about.image_alt') }}">
        </figure>
    </section>

    {{-- Notre histoire : la conviction en grand, le reste du texte à côté --}}
    @if ($paragraphs->isNotEmpty())
        <section class="section-clean story">
            <div class="story-lead" data-reveal>
                <p class="kicker">{{ site_text('about.story_kicker') }}</p>
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
            <span>{{ site_text('about.figure_1') }}</span>
        </div>
        <div data-reveal style="--reveal-delay: 100ms">
            <strong data-count-to="{{ $applicationCount }}">{{ $applicationCount }}</strong>
            <span>{{ site_text('about.figure_2') }}</span>
        </div>
        <div data-reveal style="--reveal-delay: 200ms">
            <strong>1</strong>
            <span>{{ site_text('about.figure_3') }}</span>
        </div>
    </section>

    {{-- Valeurs : bandeau bleu nuit --}}
    <section class="band-dark">
        <div class="band-dark-inner">
            <header class="section-head" data-reveal>
                <div>
                    <p class="kicker">{{ site_text('about.values_kicker') }}</p>
                    <h2>{{ site_text('about.values_heading') }}</h2>
                </div>
            </header>

            <ul class="feature-cards">
                <li data-reveal>
                    <x-icon name="globe" />
                    <h3>{{ site_text('about.value_1_title') }}</h3>
                    <p>{{ site_text('about.value_1_text') }}</p>
                </li>
                <li data-reveal style="--reveal-delay: 90ms">
                    <x-icon name="layers" />
                    <h3>{{ site_text('about.value_2_title') }}</h3>
                    <p>{{ site_text('about.value_2_text') }}</p>
                </li>
                <li data-reveal style="--reveal-delay: 180ms">
                    <x-icon name="shield" />
                    <h3>{{ site_text('about.value_3_title') }}</h3>
                    <p>{{ site_text('about.value_3_text') }}</p>
                </li>
            </ul>
        </div>
    </section>

    @include('partials.cta-clean', [
        'eyebrow' => site_text('about.cta_eyebrow'),
        'heading' => site_text('about.cta_heading'),
        'text' => site_text('about.cta_text'),
        'label' => site_text('about.cta_button'),
    ])
@endsection
