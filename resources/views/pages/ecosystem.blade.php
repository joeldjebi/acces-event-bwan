@extends('layouts.public', [
    'title' => $page->seo_title ?: $page->name,
    'metaDescription' => $page->seo_description ?: $page->summary,
    'canonicalUrl' => route('ecosystem'),
    'bodyClass' => 'page-clean',
])

@section('content')
    {{-- En-tête centré --}}
    <section class="page-intro">
        <div class="page-intro-copy hero-copy">
            <p class="kicker">{{ $page->eyebrow }}</p>
            <h1>{{ $page->heading }}</h1>
            <p class="sol-lead">{{ \Illuminate\Support\Str::limit($page->summary, 170) }}</p>
            <div class="cta-row">
                <a href="{{ route('contact') }}" class="btn btn-primary">Demander une architecture</a>
                <a href="#applications" class="arrow-link">Explorer les applications <span aria-hidden="true">↓</span></a>
            </div>
        </div>
    </section>

    {{-- Hub animé : les applications gravitent autour du système central, reliées par des flux de données.
         Chaque application renvoie vers sa fiche plus bas. --}}
    <section class="hub-section" aria-label="Applications connectées au système central">
        <div class="hub" style="--count: {{ max(1, $applications->count()) }}">
            <div class="hub-ring">
                @foreach ($applications as $application)
                    <span class="hub-spoke" style="--i: {{ $loop->index }}" aria-hidden="true"></span>
                @endforeach

                @foreach ($applications as $application)
                    <a href="#app-{{ $application->slug }}" class="hub-node" style="--i: {{ $loop->index }}">
                        <span class="hub-node-inner">
                            @if ($application->logo_path)
                                <img src="{{ $application->logo_path }}" alt="" class="hub-node-badge" loading="lazy">
                            @else
                                <span class="hub-node-badge" aria-hidden="true">{{ $application->initials }}</span>
                            @endif
                            <span class="hub-node-label">{{ $application->name }}</span>
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="hub-core" aria-hidden="true">
                <img src="{{ asset('images/logo/accesslogo.png') }}" alt="">
                <span>Système central</span>
            </div>
        </div>
    </section>

    {{-- Applications, filtrables par famille de domaines --}}
    <section id="applications" class="section-clean">
        <header class="section-head" data-reveal>
            <div>
                <p class="kicker">Applications</p>
                <h2>Une plateforme composée pour chaque équipe terrain.</h2>
            </div>
            <p>{{ $applications->count() }} applications qui partagent les mêmes données, les mêmes droits et la même administration.</p>
        </header>

        <div class="sol-filters" role="group" aria-label="Filtrer les applications par famille" data-filter-group>
            <button type="button" class="is-active" data-filter="all" aria-pressed="true">
                Toutes <span>{{ $applications->count() }}</span>
            </button>
            @foreach (config('accessevent.solution_groups') as $key => $group)
                @php($groupCount = $applications->filter(fn ($application) => in_array($key, $application->groups(), true))->count())
                @if ($groupCount > 0)
                    <button type="button" data-filter="{{ $key }}" aria-pressed="false">
                        {{ $group['label'] }} <span>{{ $groupCount }}</span>
                    </button>
                @endif
            @endforeach
        </div>
        <p class="sr-only" aria-live="polite" data-filter-status></p>

        <div class="sol-grid" data-filter-list>
            @foreach ($applications as $application)
                <article id="app-{{ $application->slug }}" class="sol-card app-card" data-group="{{ implode(' ', $application->groups()) ?: 'none' }}" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                    <div class="sol-card-media">
                        @if ($application->logo_path)
                            <img src="{{ $application->logo_path }}" alt="{{ $application->name }}" class="media-cover" loading="lazy">
                        @else
                            <div class="media-cover solution-visual is-{{ $application->groups()[0] ?? 'default' }}" aria-hidden="true">
                                <strong class="app-initials">{{ $application->initials }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="sol-card-body">
                        <h3>{{ $application->name }}</h3>
                        <p>{{ $application->role }}</p>
                        @if ($application->solutions->isNotEmpty())
                            <div class="chip-row">
                                @foreach ($application->solutions as $solution)
                                    <a href="{{ route('solutions.show', $solution) }}">{{ $solution->title }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Interopérabilité : bandeau bleu nuit --}}
    <section class="band-dark">
        <div class="band-dark-inner">
            <header class="section-head" data-reveal>
                <div>
                    <p class="kicker">Interopérabilité</p>
                    <h2>{{ $page->cta_label ?: 'Une seule donnée, partout, en temps réel.' }}</h2>
                </div>
                @if ($page->body)
                    <p>{{ $page->body }}</p>
                @endif
            </header>

            <ul class="feature-cards">
                <li data-reveal>
                    <x-icon name="sync" />
                    <h3>Données synchronisées</h3>
                    <p>Titres, droits et passages partagés entre toutes les applications.</p>
                </li>
                <li data-reveal style="--reveal-delay: 90ms">
                    <x-icon name="bolt" />
                    <h3>Opérations temps réel</h3>
                    <p>Ventes, entrées et recettes remontent au fil de l'événement.</p>
                </li>
                <li data-reveal style="--reveal-delay: 180ms">
                    <x-icon name="layers" />
                    <h3>Administration unique</h3>
                    <p>Un seul back office pour paramétrer et piloter l'ensemble.</p>
                </li>
            </ul>
        </div>
    </section>

    @include('partials.cta-clean', [
        'eyebrow' => 'Sur mesure',
        'heading' => "Votre besoin n'est pas listé\u{00A0}?",
        'text' => 'Nos applications se combinent et s\'adaptent à votre dispositif terrain.',
        'label' => 'Parlons-en',
    ])
@endsection
