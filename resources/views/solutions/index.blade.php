@extends('layouts.public', [
    'title' => 'Solutions',
    'metaDescription' => 'Les '.$solutions->count().' domaines ACCÈS EVENT pour la billetterie, le contrôle, les paiements et la sécurité événementielle.',
    'canonicalUrl' => route('solutions.index'),
    'bodyClass' => 'page-clean',
])

@section('content')
    {{-- En-tête centré --}}
    <section class="page-intro">
        <div class="page-intro-copy hero-copy">
            <p class="kicker">Solutions</p>
            <h1>Une suite intégrée pour piloter tout votre événement.</h1>
            <p class="sol-lead">Des modules métier connectés pour vendre, contrôler, encaisser, accréditer et superviser vos opérations terrain.</p>
            <div class="cta-row">
                <a href="{{ route('contact') }}" class="btn btn-primary">Demander une démonstration</a>
                <a href="#catalogue" class="arrow-link">Parcourir le catalogue <span aria-hidden="true">↓</span></a>
            </div>
        </div>
    </section>

    {{-- Mur animé des domaines : deux rangées qui défilent en sens inverse (pause au survol).
         Chaque rangée est dupliquée pour une boucle continue ; la copie est ignorée au clavier et par les lecteurs d'écran. --}}
    @php($half = (int) ceil($solutions->count() / 2))
    <section class="sol-wall" aria-label="Aperçu des domaines">
        @foreach ([$solutions->take($half), $solutions->slice($half)] as $row)
            <div @class(['sol-wall-row', 'is-reverse' => $loop->last])>
                @foreach ([false, true] as $isClone)
                    <div class="sol-wall-track" @if ($isClone) aria-hidden="true" @endif>
                        @foreach ($row as $solution)
                            <a href="{{ route('solutions.show', $solution) }}" class="sol-wall-card" @if ($isClone) tabindex="-1" @endif>
                                <x-solution-media :solution="$solution" />
                                <span>{{ $solution->title }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </section>

    {{-- Chiffres clés, en une ligne --}}
    <dl class="sol-stats">
        <div>
            <dt>domaines métier</dt>
            <dd>{{ $solutions->count() }}</dd>
        </div>
        <div>
            <dt>applications connectées</dt>
            <dd>{{ $applicationCount }}</dd>
        </div>
        <div>
            <dt>administration unique</dt>
            <dd>1</dd>
        </div>
    </dl>

    {{-- Catalogue : un sommaire éditorial des domaines --}}
    <section id="catalogue" class="section-clean sol-catalog">
        <header class="section-head" data-reveal>
            <div>
                <p class="kicker">Catalogue</p>
                <h2>Choisissez les briques utiles à votre dispositif.</h2>
            </div>
            <p>Chaque domaine fonctionne seul ou avec l'ensemble de l'écosystème, sur la même administration.</p>
        </header>

        {{-- Filtres par famille : les cartes non concernées s'effacent, sans rechargement. --}}
        <div class="sol-filters" role="group" aria-label="Filtrer les domaines par famille" data-filter-group>
            <button type="button" class="is-active" data-filter="all" aria-pressed="true">
                Tous <span>{{ $solutions->count() }}</span>
            </button>
            @foreach (config('accessevent.solution_groups') as $key => $group)
                @php($groupCount = $solutions->where('group', $key)->count())
                @if ($groupCount > 0)
                    <button type="button" data-filter="{{ $key }}" aria-pressed="false">
                        {{ $group['label'] }} <span>{{ $groupCount }}</span>
                    </button>
                @endif
            @endforeach
        </div>
        <p class="sr-only" aria-live="polite" data-filter-status></p>

        <div class="sol-grid" data-filter-list>
            @foreach ($solutions as $solution)
                <a href="{{ route('solutions.show', $solution) }}" @class(['sol-card', 'is-featured' => $loop->first]) data-group="{{ $solution->group ?? 'none' }}" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                    <div class="sol-card-media">
                        <x-solution-media :solution="$solution" :eager="$loop->first" />
                    </div>
                    <div class="sol-card-body">
                        <div class="sol-card-meta">
                            <span class="sol-card-index">{{ str_pad((string) $solution->position, 2, '0', STR_PAD_LEFT) }}</span>
                            @if ($solution->groupLabel)
                                <span class="sol-card-group">{{ $solution->groupLabel }}</span>
                            @endif
                        </div>
                        <h3>{{ $solution->title }}</h3>
                        @if ($solution->short_description)
                            <p>{{ \Illuminate\Support\Str::limit($solution->short_description, $loop->first ? 200 : 120) }}</p>
                        @endif
                        <span class="arrow-link">Découvrir <span aria-hidden="true">→</span></span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    @include('partials.cta-clean', [
        'eyebrow' => 'Sur mesure',
        'heading' => "Plusieurs domaines vous intéressent\u{00A0}?",
        'text' => 'Nous assemblons les briques adaptées à votre événement et à votre budget.',
        'label' => 'Composer ma solution',
    ])
@endsection
