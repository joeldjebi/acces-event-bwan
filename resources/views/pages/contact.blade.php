@extends('layouts.public', [
    'title' => $page->seo_title ?: $page->name,
    'metaDescription' => $page->seo_description ?: $page->summary,
    'canonicalUrl' => route('contact'),
    'bodyClass' => 'page-clean',
])

@section('content')
    {{-- En-tête centré --}}
    <section class="page-intro">
        <div class="page-intro-copy hero-copy">
            <p class="kicker">{{ $page->eyebrow }}</p>
            <h1>{{ $page->heading }}</h1>
            <p class="sol-lead">{{ $page->summary }}</p>
        </div>
    </section>

    <section class="section-clean contact-layout">
        <form method="POST" action="{{ route('demo-requests.store') }}" class="public-form contact-form" aria-label="Formulaire de démonstration" data-reveal>
            <div class="contact-form-head">
                <h2>Votre événement</h2>
                <p>Les champs marqués d'un * sont obligatoires.</p>
            </div>

            @csrf

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="honeypot-field" aria-hidden="true">
                <label for="website">Site web</label>
                <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="full_name">Nom complet <span class="required" aria-hidden="true">*</span></label>
                    <input id="full_name" name="full_name" class="form-input" value="{{ old('full_name') }}" placeholder="Votre nom" required>
                    @error('full_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="company">Entreprise</label>
                    <input id="company" name="company" class="form-input" value="{{ old('company') }}" placeholder="Organisation">
                    @error('company') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="email">Email <span class="required" aria-hidden="true">*</span></label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email') }}" placeholder="nom@entreprise.com" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="phone">Téléphone / WhatsApp</label>
                    <input id="phone" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+225 ...">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="event_type">Type d'événement</label>
                    <input id="event_type" name="event_type" class="form-input" value="{{ old('event_type') }}" placeholder="Concert, salon, festival, institution...">
                    @error('event_type') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="selected_solution">Solution souhaitée</label>
                    <select id="selected_solution" name="selected_solution" class="form-input">
                        <option value="">À préciser ensemble</option>
                        @foreach ($solutions as $solution)
                            <option value="{{ $solution->title }}" @selected(old('selected_solution') === $solution->title)>{{ $solution->title }}</option>
                        @endforeach
                    </select>
                    @error('selected_solution') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="message">Besoin <span class="required" aria-hidden="true">*</span></label>
                <textarea id="message" name="message" class="form-input form-textarea" rows="6" placeholder="Billetterie, contrôle, accréditation, paiement, sécurité..." required>{{ old('message') }}</textarea>
                @error('message') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn-primary contact-submit">{{ $page->cta_label ?: 'Envoyer la demande' }}</button>
        </form>

        <aside class="contact-aside" data-reveal style="--reveal-delay: 120ms">
            <div class="contact-methods">
                @if ($footerSettings['footer_whatsapp'])
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $footerSettings['footer_whatsapp']) }}" target="_blank" rel="noopener" class="contact-method">
                        <x-icon name="chat" />
                        <span>WhatsApp</span>
                        <strong>{{ $footerSettings['footer_whatsapp'] }}</strong>
                    </a>
                @endif
                @if ($footerSettings['footer_phone'])
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $footerSettings['footer_phone']) }}" class="contact-method">
                        <x-icon name="phone" />
                        <span>Téléphone</span>
                        <strong>{{ $footerSettings['footer_phone'] }}</strong>
                    </a>
                @endif
                @if ($footerSettings['footer_email'])
                    <a href="mailto:{{ $footerSettings['footer_email'] }}" class="contact-method">
                        <x-icon name="mail" />
                        <span>Email</span>
                        <strong>{{ $footerSettings['footer_email'] }}</strong>
                    </a>
                @endif
                @if ($footerSettings['footer_address'])
                    <div class="contact-method">
                        <x-icon name="pin" />
                        <span>Adresse</span>
                        <strong>{{ $footerSettings['footer_address'] }}</strong>
                    </div>
                @endif
                @if ($footerSettings['footer_hours'])
                    <div class="contact-method">
                        <x-icon name="clock" />
                        <span>Horaires</span>
                        <strong>{{ $footerSettings['footer_hours'] }}</strong>
                    </div>
                @endif
            </div>

            <div class="contact-steps">
                <p class="sol-aside-title">Comment ça se passe</p>
                <ol>
                    <li>
                        <span>01</span>
                        <div>
                            <strong>Vous décrivez votre événement</strong>
                            <p>Type, jauge, dates et modules qui vous intéressent.</p>
                        </div>
                    </li>
                    <li>
                        <span>02</span>
                        <div>
                            <strong>Un expert vous recontacte</strong>
                            <p>Pour cadrer vos besoins et votre dispositif terrain.</p>
                        </div>
                    </li>
                    <li>
                        <span>03</span>
                        <div>
                            <strong>Démonstration sur mesure</strong>
                            <p>Sur un scénario proche du vôtre.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </aside>
    </section>
@endsection
