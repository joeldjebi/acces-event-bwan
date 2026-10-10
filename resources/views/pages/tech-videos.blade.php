@extends('layouts.public', [
    'title' => site_text('videos.seo_title'),
    'metaDescription' => site_text('videos.seo_description'),
    'canonicalUrl' => route('tech-videos.index'),
    'bodyClass' => 'page-clean',
])

@section('content')
    {{-- En-tête centré --}}
    <section class="page-intro">
        <div class="page-intro-copy hero-copy">
            <p class="kicker">{{ site_text('videos.kicker') }}</p>
            <h1>{{ site_text('videos.heading') }}</h1>
            <p class="sol-lead">{{ site_text('videos.lead') }}</p>
            <div class="cta-row">
                <a href="#videos" class="btn btn-primary">{{ site_text('videos.button') }}</a>
                <a href="{{ route('contact') }}" class="arrow-link">{{ site_text('videos.link') }} <span aria-hidden="true">→</span></a>
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
                            <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener" class="arrow-link">{{ site_text('videos.watch_link') }} <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state" data-reveal>
                <x-icon name="play" />
                <h2>{{ site_text('videos.empty_heading') }}</h2>
                <p>{{ site_text('videos.empty_text') }}</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">{{ site_text('common.demo_button') }}</a>
            </div>
        @endif
    </section>

    {{-- Ce que couvrent les vidéos --}}
    <section class="section-clean">
        <header class="section-head" data-reveal>
            <div>
                <p class="kicker">{{ site_text('videos.program_kicker') }}</p>
                <h2>{{ site_text('videos.program_heading') }}</h2>
            </div>
        </header>

        <ul class="feature-cards is-light">
            <li data-reveal>
                <x-icon name="play" />
                <h3>{{ site_text('videos.program_1_title') }}</h3>
                <p>{{ site_text('videos.program_1_text') }}</p>
            </li>
            <li data-reveal style="--reveal-delay: 90ms">
                <x-icon name="plug" />
                <h3>{{ site_text('videos.program_2_title') }}</h3>
                <p>{{ site_text('videos.program_2_text') }}</p>
            </li>
            <li data-reveal style="--reveal-delay: 180ms">
                <x-icon name="users" />
                <h3>{{ site_text('videos.program_3_title') }}</h3>
                <p>{{ site_text('videos.program_3_text') }}</p>
            </li>
        </ul>
    </section>

    @include('partials.cta-clean', [
        'eyebrow' => site_text('videos.cta_eyebrow'),
        'heading' => site_text('videos.cta_heading'),
        'text' => site_text('videos.cta_text'),
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
