@extends('layouts.public', [
    'title' => $page->seo_title ?: $page->name,
    'metaDescription' => $page->seo_description ?: $page->summary,
    'canonicalUrl' => route('home'),
    'bodyClass' => 'page-home',
])

@section('content')
    {{-- 1. Hero centré : promesse et appels à l'action --}}
    <section class="home-hero">
        <div class="home-aurora" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="home-hero-copy hero-copy">
            <p class="kicker">{{ $page->eyebrow }}</p>
            <h1>{{ $page->heading }}</h1>
            <p class="home-lead">{{ \Illuminate\Support\Str::limit($page->summary, 160) }}</p>
            <div class="cta-row">
                <a href="{{ $page->cta_url ?: route('contact') }}" class="btn btn-primary">{{ $page->cta_label ?: 'Demander une démonstration' }}</a>
                <a href="{{ route('solutions.index') }}" class="arrow-link">Découvrir nos solutions <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <a href="#scene" class="scroll-cue" aria-label="Découvrir la suite">
            <span aria-hidden="true"></span>
        </a>
    </section>

    {{-- 2. Grande photo interactive : s'agrandit au défilement, s'incline sous la souris,
         points cliquables sur les éléments de la scène et fil d'activité en direct. --}}
    <section id="scene" class="showcase home-showcase" data-scroll-scale>
        <figure class="showcase-frame" data-tilt>
            <div class="showcase-media">
                <img src="{{ asset('images/hero/acces-event-hero.png') }}" alt="Contrôle d'accès événementiel et billetterie digitale ACCÈS EVENT" width="1568" height="1003">
                <div @class(['hotspot', 'is-left' => false]) style="--x: 60.3%; --y: 61.8%">
                    <button type="button" class="hotspot-dot" aria-expanded="false" aria-controls="hotspot-billet">
                        <span class="sr-only">Billet mobile</span>
                    </button>
                    <div class="hotspot-card" id="hotspot-billet">
                        <strong>Billet mobile</strong>
                        <p>Le billet QR du smartphone est scanné et validé à l'entrée, en un geste.</p>
                        <a href="{{ route('solutions.show', 'billetterie-360') }}" class="arrow-link">En savoir plus <span aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div @class(['hotspot', 'is-left' => false]) style="--x: 14.7%; --y: 70.3%">
                    <button type="button" class="hotspot-dot" aria-expanded="false" aria-controls="hotspot-acces">
                        <span class="sr-only">Contrôle d'accès</span>
                    </button>
                    <div class="hotspot-card" id="hotspot-acces">
                        <strong>Contrôle d'accès</strong>
                        <p>Tourniquets connectés : chaque passage est autorisé ou refusé en temps réel.</p>
                        <a href="{{ route('solutions.show', 'controle-acces') }}" class="arrow-link">En savoir plus <span aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div @class(['hotspot', 'is-left' => false]) style="--x: 50.1%; --y: 48.7%">
                    <button type="button" class="hotspot-dot" aria-expanded="false" aria-controls="hotspot-equipes">
                        <span class="sr-only">Équipes terrain</span>
                    </button>
                    <div class="hotspot-card" id="hotspot-equipes">
                        <strong>Équipes terrain</strong>
                        <p>Superviseurs et contrôleurs équipés, affectations et consignes à jour sur tablette.</p>
                        <a href="{{ route('solutions.show', 'gestion-benevoles') }}" class="arrow-link">En savoir plus <span aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div @class(['hotspot', 'is-left' => true]) style="--x: 82.3%; --y: 25.4%">
                    <button type="button" class="hotspot-dot" aria-expanded="false" aria-controls="hotspot-supervision">
                        <span class="sr-only">Supervision en direct</span>
                    </button>
                    <div class="hotspot-card" id="hotspot-supervision">
                        <strong>Supervision en direct</strong>
                        <p>Entrées, ventes et recettes suivies zone par zone sur un tableau de bord unique.</p>
                        <a href="{{ route('solutions.index') }}" class="arrow-link">En savoir plus <span aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div @class(['hotspot', 'is-left' => true]) style="--x: 73.9%; --y: 78.8%">
                    <button type="button" class="hotspot-dot" aria-expanded="false" aria-controls="hotspot-accreditation">
                        <span class="sr-only">Accréditation</span>
                    </button>
                    <div class="hotspot-card" id="hotspot-accreditation">
                        <strong>Accréditation</strong>
                        <p>Badges nominatifs avec des droits d'accès par zone pour le staff, la presse et les VIP.</p>
                        <a href="{{ route('solutions.show', 'accreditation') }}" class="arrow-link">En savoir plus <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </div>
            <div class="showcase-glare" aria-hidden="true"></div>
            <figcaption class="home-floating">
                <span class="home-chip home-chip-1" aria-hidden="true">
                    <span class="home-live-dot"></span>
                    <span data-live-feed="Billet scanné · Porte A|Paiement mobile accepté|Badge VIP vérifié · Zone B|Entrée validée · Tribune Nord|Accès refusé · billet déjà utilisé">Billet scanné · Porte A</span>
                </span>
                <span class="home-chip home-chip-2">Paiement mobile accepté</span>
                <span class="home-chip home-chip-3">Zone VIP · accès autorisé</span>
            </figcaption>
        </figure>
        <p class="showcase-hint" aria-hidden="true"><span class="hint-hover">Survolez</span><span class="hint-touch">Touchez</span> les points pour explorer la scène</p>
    </section>

    {{-- 3. Manifeste : les mots s'illuminent au fil du défilement --}}
    <section class="home-statement">
        <p data-word-reveal>Billetterie, contrôle d'accès, paiements, accréditation et sécurité réunis dans une seule technologie. Pour sécuriser chaque recette, fluidifier chaque entrée et garder la main sur tout l'événement.</p>
    </section>

    {{-- 4. Chiffres clés animés --}}
    <section class="big-figures" aria-label="ACCÈS EVENT en chiffres">
        <div data-reveal>
            <strong data-count-to="{{ $solutionCount }}">{{ $solutionCount }}</strong>
            <span>domaines métier</span>
        </div>
        <div data-reveal style="--reveal-delay: 100ms">
            <strong data-count-to="{{ $applications->count() }}">{{ $applications->count() }}</strong>
            <span>applications connectées</span>
        </div>
        <div data-reveal style="--reveal-delay: 200ms">
            <strong>1</strong>
            <span>administration unique</span>
        </div>
    </section>

    {{-- 5. Domaines : carrousel d'images (photos ajoutées dans l'admin) --}}
    <section id="solutions" class="home-domains">
        <header class="home-domains-head section-head" data-reveal>
            <div>
                <p class="kicker">Domaines d'activité</p>
                <h2>Une suite modulaire, une seule administration.</h2>
            </div>
            <div class="home-carousel-controls">
                <a href="{{ route('solutions.index') }}" class="arrow-link">Voir les {{ $solutionCount }} domaines <span aria-hidden="true">→</span></a>
                <button type="button" class="carousel-btn" data-carousel-prev aria-label="Domaines précédents">←</button>
                <button type="button" class="carousel-btn" data-carousel-next aria-label="Domaines suivants">→</button>
            </div>
        </header>

        <div class="home-carousel" data-carousel tabindex="0" aria-label="Domaines d'activité">
            @forelse ($solutions as $solution)
                <a href="{{ route('solutions.show', $solution) }}" class="domain-card" data-reveal style="--reveal-delay: {{ min($loop->index, 4) * 70 }}ms">
                    <x-solution-media :solution="$solution" />
                    <div class="domain-card-body">
                        <span>{{ str_pad((string) $solution->position, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $solution->title }}</h3>
                        @if ($solution->short_description)
                            <p>{{ \Illuminate\Support\Str::limit($solution->short_description, 90) }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <p class="home-empty">Les domaines d'activité seront affichés ici depuis l'administration.</p>
            @endforelse
        </div>
    </section>

    {{-- Formats d'événements : onglets, domaines recommandés pour chaque format --}}
    @if ($eventTypes->isNotEmpty())
        <section class="section-clean event-types">
            <header class="section-head" data-reveal>
                <div>
                    <p class="kicker">Pour chaque format</p>
                    <h2>Quel que soit votre événement, les bons modules.</h2>
                </div>
                <p>Choisissez votre format : voici les domaines que nous activons le plus souvent.</p>
            </header>

            <div class="event-tabs" data-tabs>
                <div class="event-tablist" role="tablist" aria-label="Formats d'événements" data-reveal>
                    @foreach ($eventTypes as $key => $type)
                        <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" @unless ($loop->first) tabindex="-1" @endunless>
                            {{ $type['label'] }}
                        </button>
                    @endforeach
                </div>

                @foreach ($eventTypes as $key => $type)
                    <div class="event-panel" role="tabpanel" id="panel-{{ $key }}" aria-labelledby="tab-{{ $key }}" @unless ($loop->first) hidden @endunless>
                        <p class="event-panel-text">{{ $type['text'] }}</p>
                        <div class="event-panel-grid">
                            @foreach ($type['solutions'] as $solution)
                                <a href="{{ route('solutions.show', $solution) }}" class="event-module" style="--d: {{ $loop->index * 70 }}ms">
                                    <span class="event-module-icon solution-visual is-{{ $solution->group ?? 'default' }}">
                                        <x-solution-icon :slug="$solution->slug" />
                                    </span>
                                    <strong>{{ $solution->title }}</strong>
                                    @if ($solution->short_description)
                                        <span class="event-module-text">{{ \Illuminate\Support\Str::limit($solution->short_description, 80) }}</span>
                                    @endif
                                    <span class="event-module-arrow" aria-hidden="true">→</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 6. Méthode : une ligne de progression suit le défilement --}}
    <section class="section-clean home-method">
        <header class="section-head" data-reveal>
            <div>
                <p class="kicker">Exécution terrain</p>
                <h2>Une opération fluide, du paramétrage au reporting.</h2>
            </div>
        </header>

        <ol class="home-timeline" data-scroll-progress>
            <li data-reveal>
                <span>01</span>
                <div>
                    <h3>Préparer</h3>
                    <p>Billetterie, zones d'accès, accréditations et équipes paramétrées avant l'ouverture.</p>
                </div>
            </li>
            <li data-reveal>
                <span>02</span>
                <div>
                    <h3>Opérer</h3>
                    <p>Contrôle des entrées, paiements et sécurité suivis en temps réel le jour J.</p>
                </div>
            </li>
            <li data-reveal>
                <span>03</span>
                <div>
                    <h3>Analyser</h3>
                    <p>Ventes, fréquentation et recettes consolidées dans un seul tableau de bord.</p>
                </div>
            </li>
        </ol>
    </section>

    {{-- 7. Écosystème : bandeaux d'applications en défilement continu --}}
    <section id="ecosysteme" class="home-ecosystem">
        <div class="home-ecosystem-inner">
            <header data-reveal>
                <p class="kicker">Écosystème applicatif</p>
                <h2>Des applications connectées, une vision unique.</h2>
            </header>
            <a href="{{ route('ecosystem') }}" class="arrow-link" data-reveal>Explorer l'écosystème <span aria-hidden="true">→</span></a>
        </div>

        @php($half = (int) ceil($applications->count() / 2))
        @foreach ([$applications->take($half), $applications->slice($half)] as $row)
            <div @class(['marquee', 'is-reverse' => $loop->last])>
                {{-- La liste est dupliquée pour une boucle continue ; la copie est ignorée des lecteurs d'écran. --}}
                @foreach ([false, true] as $isClone)
                    <ul class="marquee-track" @if ($isClone) aria-hidden="true" @endif>
                        @foreach ($row as $application)
                            <li><strong>{{ $application->name }}</strong><span>{{ $application->role }}</span></li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        @endforeach
    </section>

    {{-- Appel à l'action final --}}
    <section class="home-final">
        <div class="home-final-card" data-reveal>
            <div class="home-aurora is-dark" aria-hidden="true">
                <span></span>
                <span></span>
            </div>
            <p class="kicker">Démonstration</p>
            <h2>Vous organisez un événement&nbsp;? Parlons-en.</h2>
            <p class="home-final-text">Présentez-nous votre dispositif : nous vous montrons les modules utiles sur un scénario proche du vôtre.</p>
            <div class="cta-row">
                <a href="{{ route('contact') }}" class="btn btn-primary">Demander une démonstration</a>
                @if ($footerSettings['footer_whatsapp'])
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $footerSettings['footer_whatsapp']) }}" target="_blank" rel="noopener" class="btn btn-glass">
                        <x-icon name="chat" />
                        Écrire sur WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection
