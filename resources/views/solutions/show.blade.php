@extends('layouts.public', [
    'title' => $solution->title,
    'metaDescription' => $solution->short_description,
    'canonicalUrl' => route('solutions.show', $solution),
    'ogType' => 'article',
    'ogImage' => $solution->image_path ?: asset('images/hero/acces-event-hero.png'),
    'bodyClass' => 'page-clean',
])

@php
    $number = str_pad((string) $solution->position, 2, '0', STR_PAD_LEFT);
    $paragraphs = collect(preg_split('/\r\n|\r|\n/', (string) $solution->content))
        ->map(fn ($paragraph) => trim($paragraph))
        ->filter();
@endphp

@section('content')
    {{-- En-tête du domaine, centré --}}
    <section class="sol-hero">
        <div class="sol-hero-copy hero-copy">
            <nav class="breadcrumb" aria-label="Fil d'Ariane">
                <a href="{{ route('solutions.index') }}">Solutions</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $solution->title }}</span>
            </nav>
            <p class="kicker">Domaine {{ $number }} / {{ str_pad((string) $solutionCount, 2, '0', STR_PAD_LEFT) }}@if ($solution->groupLabel) · {{ $solution->groupLabel }}@endif</p>
            <h1>{{ $solution->title }}</h1>
            @if ($solution->subtitle || $solution->short_description)
                <p class="sol-lead">{{ $solution->subtitle ?: $solution->short_description }}</p>
            @endif
            <div class="cta-row">
                <a href="{{ route('contact') }}" class="btn btn-primary">Demander une démonstration</a>
                <a href="{{ route('solutions.index') }}" class="arrow-link">Tous les domaines <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    {{-- Grande image du domaine (ajoutée dans l'admin) qui s'agrandit au défilement --}}
    <section class="showcase" data-scroll-scale>
        <figure class="showcase-frame sol-showcase">
            <x-solution-media :solution="$solution" eager />
        </figure>
    </section>

    {{-- Barre de navigation du domaine : reste visible en haut de l'écran pendant la lecture --}}
    <nav class="sol-localnav" aria-label="Sections du domaine" data-localnav>
        <div class="sol-localnav-inner">
            <strong>{{ $solution->title }}</strong>
            <div class="sol-localnav-links">
                <a href="#presentation">Présentation</a>
                @if ($solution->tools->isNotEmpty())
                    <a href="#outils">Outils</a>
                @endif
                @if ($solution->benefits->isNotEmpty())
                    <a href="#benefices">Bénéfices</a>
                @endif
                <a href="#domaines-associes">Domaines associés</a>
            </div>
            <a href="{{ route('contact') }}" class="btn btn-primary">Démonstration</a>
        </div>
    </nav>

    {{-- Présentation et encadré « en bref » --}}
    <section id="presentation" class="section-clean sol-overview">
        <article class="sol-prose" data-reveal>
            <p class="kicker">Présentation</p>
            {{-- Le texte court n'est repris ici que s'il n'a pas déjà servi d'accroche dans l'en-tête. --}}
            @if ($solution->subtitle && $solution->short_description)
                <p class="sol-prose-lead">{{ $solution->short_description }}</p>
            @endif
            @foreach ($paragraphs as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach

            <dl class="sol-facts">
                @foreach ([
                    'Outils inclus' => $solution->tools->count(),
                    'Applications connectées' => $solution->platformApplications->count(),
                    'Bénéfices clés' => $solution->benefits->count(),
                ] as $label => $value)
                    @if ($value > 0)
                        <div>
                            <dt>{{ $label }}</dt>
                            <dd>{{ str_pad((string) $value, 2, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </article>

        <aside class="sol-aside" data-reveal>
            @if ($solution->platformApplications->isNotEmpty())
                <p class="sol-aside-title">Applications associées</p>
                <ul class="sol-app-list">
                    @foreach ($solution->platformApplications as $application)
                        <li>
                            <a href="{{ route('ecosystem') }}">
                                @if ($application->logo_path)
                                    <img src="{{ $application->logo_path }}" alt="" class="sol-app-logo" loading="lazy">
                                @else
                                    <span class="sol-app-logo" aria-hidden="true">{{ $application->initials }}</span>
                                @endif
                                <strong>{{ $application->name }}</strong>
                                <span class="sol-app-arrow" aria-hidden="true">→</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
            <p class="sol-aside-text">Voyez {{ $solution->title }} en action sur un scénario proche de votre événement.</p>
            <a href="{{ route('contact') }}" class="btn btn-primary">Demander une démonstration</a>
        </aside>
    </section>

    {{-- Outils inclus --}}
    @if ($solution->tools->isNotEmpty())
        <section id="outils" class="section-clean">
            <header class="section-head" data-reveal>
                <div>
                    <p class="kicker">Outils inclus</p>
                    <h2>Les outils qui font tourner {{ $solution->title }}.</h2>
                </div>
            </header>

            <div class="sol-tools">
                @foreach ($solution->tools as $tool)
                    <article class="sol-tool" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                        <span class="sol-tool-badge" aria-hidden="true">{{ collect(preg_split('/\s+/u', $tool->name, -1, PREG_SPLIT_NO_EMPTY))->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}</span>
                        <h3>{{ $tool->name }}</h3>
                        @if ($tool->description)
                            <p>{{ $tool->description }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Bénéfices : bandeau bleu nuit --}}
    @if ($solution->benefits->isNotEmpty())
        <section id="benefices" class="band-dark">
            <div class="band-dark-inner">
                <header class="section-head" data-reveal>
                    <div>
                        <p class="kicker">Bénéfices</p>
                        <h2>Ce que {{ $solution->title }} apporte à votre opération.</h2>
                    </div>
                </header>

                <ol class="feature-cards">
                    @foreach ($solution->benefits as $benefit)
                        <li data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 90 }}ms">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M7.5 12.5l3 3 6-6.5" />
                            </svg>
                            <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $benefit->title }}</h3>
                            <p>{{ $benefit->description }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- Domaines associés : même famille d'abord --}}
    @if ($relatedSolutions->isNotEmpty())
        <section id="domaines-associes" class="section-clean">
            <header class="section-head" data-reveal>
                <div>
                    <p class="kicker">{{ $solution->groupLabel ? 'Dans le même univers' : 'À découvrir aussi' }}</p>
                    <h2>Des domaines qui se combinent avec {{ $solution->title }}.</h2>
                </div>
                <a href="{{ route('solutions.index') }}" class="arrow-link">Tous les domaines <span aria-hidden="true">→</span></a>
            </header>

            <div class="sol-grid">
                @foreach ($relatedSolutions as $related)
                    <a href="{{ route('solutions.show', $related) }}" class="sol-card" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <div class="sol-card-media">
                            <x-solution-media :solution="$related" />
                        </div>
                        <div class="sol-card-body">
                            <div class="sol-card-meta">
                                <span class="sol-card-index">{{ str_pad((string) $related->position, 2, '0', STR_PAD_LEFT) }}</span>
                                @if ($related->groupLabel)
                                    <span class="sol-card-group">{{ $related->groupLabel }}</span>
                                @endif
                            </div>
                            <h3>{{ $related->title }}</h3>
                            @if ($related->short_description)
                                <p>{{ \Illuminate\Support\Str::limit($related->short_description, 110) }}</p>
                            @endif
                            <span class="arrow-link">Découvrir <span aria-hidden="true">→</span></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Navigation entre domaines --}}
    @if ($previousSolution || $nextSolution)
        <nav class="section-clean" aria-label="Autres domaines">
            <div class="sol-pager">
                @if ($previousSolution)
                    <a href="{{ route('solutions.show', $previousSolution) }}" class="sol-pager-prev">
                        <div class="sol-pager-thumb">
                            <x-solution-media :solution="$previousSolution" />
                        </div>
                        <div class="sol-pager-text">
                            <span>← Domaine précédent</span>
                            <strong>{{ $previousSolution->title }}</strong>
                        </div>
                    </a>
                @endif
                @if ($nextSolution)
                    <a href="{{ route('solutions.show', $nextSolution) }}" class="sol-pager-next">
                        <div class="sol-pager-thumb">
                            <x-solution-media :solution="$nextSolution" />
                        </div>
                        <div class="sol-pager-text">
                            <span>Domaine suivant →</span>
                            <strong>{{ $nextSolution->title }}</strong>
                        </div>
                    </a>
                @endif
            </div>
        </nav>
    @endif

    @include('partials.cta-clean', [
        'heading' => 'Voir '.$solution->title."\u{00A0}sur votre événement\u{00A0}?",
        'text' => 'Nous préparons une démonstration sur un scénario proche du vôtre.',
    ])
@endsection
