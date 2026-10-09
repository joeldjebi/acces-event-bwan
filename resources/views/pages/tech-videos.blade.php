@extends('layouts.public', [
    'title' => 'Vidéos techniques',
    'metaDescription' => 'Vidéos techniques ACCÈS EVENT : démonstrations, intégrations et usages terrain.',
    'canonicalUrl' => route('tech-videos.index'),
    'bodyClass' => 'page-clean',
])

@section('content')
    {{-- En-tête centré --}}
    <section class="page-intro">
        <div class="page-intro-copy hero-copy">
            <p class="kicker">Vidéos techniques</p>
            <h1>Démonstrations techniques et usages terrain.</h1>
            <p class="sol-lead">Explorez les vidéos ACCÈS EVENT pour comprendre les flux, les intégrations et les opérations connectées.</p>
            <div class="cta-row">
                <a href="#videos" class="btn btn-primary">Voir les vidéos</a>
                <a href="{{ route('contact') }}" class="arrow-link">Demander une démo en direct <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    {{-- Vidéos : la première est mise en avant. Une vidéo YouTube s'ouvre dans le lecteur intégré ;
         un lien qui n'est pas une vidéo (ex. une chaîne) s'ouvre sur YouTube. --}}
    <section id="videos" class="section-clean video-section">
        @if ($videos->isNotEmpty())
            <div class="sol-grid">
                @foreach ($videos as $video)
                    <article @class(['sol-card', 'video-card', 'is-featured' => $loop->first]) data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                        <a href="{{ $video->youtube_url }}" class="sol-card-media video-cover" target="_blank" rel="noopener"
                            @if ($video->youtube_id) data-video-id="{{ $video->youtube_id }}" data-video-title="{{ $video->label }}" @endif
                            aria-label="Regarder « {{ $video->label }} »">
                            @if ($video->cover_url)
                                <img src="{{ $video->cover_url }}" alt="" class="media-cover" loading="lazy">
                            @else
                                <div class="media-cover solution-visual" aria-hidden="true"></div>
                            @endif
                            <span class="video-play" aria-hidden="true"></span>
                        </a>
                        <div class="sol-card-body">
                            <h3>{{ $video->label }}</h3>
                            @if ($video->description)
                                <p>{{ \Illuminate\Support\Str::limit($video->description, 160) }}</p>
                            @endif
                            @if (! empty($video->tags))
                                <div class="chip-row">
                                    @foreach ($video->tags as $tag)
                                        <span>{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener" class="arrow-link">{{ $video->youtube_id ? 'Voir sur YouTube' : 'Ouvrir sur YouTube' }} <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state" data-reveal>
                <x-icon name="play" />
                <h2>Les premières vidéos arrivent bientôt.</h2>
                <p>En attendant, nous pouvons vous faire une démonstration en direct.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">Demander une démonstration</a>
            </div>
        @endif
    </section>

    {{-- Ce que couvrent les vidéos --}}
    <section class="section-clean">
        <header class="section-head" data-reveal>
            <div>
                <p class="kicker">Au programme</p>
                <h2>Comprendre la plateforme, de l'écran au terrain.</h2>
            </div>
        </header>

        <ul class="feature-cards is-light">
            <li data-reveal>
                <x-icon name="play" />
                <h3>Démonstrations produit</h3>
                <p>Les modules ACCÈS EVENT présentés pas à pas, de la configuration au résultat.</p>
            </li>
            <li data-reveal style="--reveal-delay: 90ms">
                <x-icon name="plug" />
                <h3>Intégrations &amp; flux</h3>
                <p>Comment les applications échangent billets, droits, paiements et données.</p>
            </li>
            <li data-reveal style="--reveal-delay: 180ms">
                <x-icon name="users" />
                <h3>Usages terrain</h3>
                <p>Les équipes en situation réelle : contrôle, encaissement et supervision.</p>
            </li>
        </ul>
    </section>

    @include('partials.cta-clean', [
        'eyebrow' => 'En direct',
        'heading' => "Une question technique\u{00A0}? Voyons-la ensemble.",
        'text' => 'Intégrations, flux de données, matériel terrain : nos équipes vous répondent en démonstration.',
    ])

    {{-- Lecteur vidéo intégré --}}
    <dialog class="video-dialog" data-video-dialog aria-label="Lecteur vidéo">
        <div class="video-dialog-inner">
            <div class="video-dialog-bar">
                <strong data-video-title></strong>
                <button type="button" data-video-close aria-label="Fermer la vidéo">✕</button>
            </div>
            <div class="video-dialog-frame">
                <iframe src="about:blank" title="Vidéo ACCÈS EVENT" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
            </div>
        </div>
    </dialog>
@endsection
