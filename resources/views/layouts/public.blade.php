<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="{{ config('accessevent.colors.secondary') }}">
        <meta name="description" content="{{ $metaDescription ?? config('accessevent.brand.promise') }}">
        <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">

        <title>{{ isset($title) ? $title.' - '.config('accessevent.brand.name') : config('accessevent.brand.name') }}</title>
        <meta property="og:type" content="{{ $ogType ?? 'website' }}">
        <meta property="og:site_name" content="{{ config('accessevent.brand.name') }}">
        <meta property="og:title" content="{{ isset($title) ? $title.' - '.config('accessevent.brand.name') : config('accessevent.brand.name') }}">
        <meta property="og:description" content="{{ $metaDescription ?? config('accessevent.brand.promise') }}">
        <meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
        <meta property="og:image" content="{{ $ogImage ?? asset('images/hero/acces-event-hero.png') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ isset($title) ? $title.' - '.config('accessevent.brand.name') : config('accessevent.brand.name') }}">
        <meta name="twitter:description" content="{{ $metaDescription ?? config('accessevent.brand.promise') }}">
        <meta name="twitter:image" content="{{ $ogImage ?? asset('images/hero/acces-event-hero.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-ae-cream text-ae-ink antialiased {{ $bodyClass ?? '' }}">
        <div class="site-shell">
            <header class="site-header-wrap">
                <div class="site-header">
                    <a href="{{ route('home') }}" class="brand-lockup" aria-label="{{ config('accessevent.brand.name') }}">
                        <img src="{{ asset('images/logo/accesslogo.png') }}" alt="" class="brand-image">

                    </a>

                    <nav class="site-nav" id="site-nav" aria-label="Navigation principale">
                        <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Accueil</a>
                        <a href="{{ route('solutions.index') }}" @class(['is-active' => request()->routeIs('solutions.*')])>Solutions</a>
                        <a href="{{ route('ecosystem') }}" @class(['is-active' => request()->routeIs('ecosystem')])>Écosystème</a>
                        <a href="{{ route('tech-videos.index') }}" @class(['is-active' => request()->routeIs('tech-videos.*')])>Vidéos techniques</a>
                        <a href="{{ route('about') }}" @class(['is-active' => request()->routeIs('about')])>Qui sommes-nous</a>
                        <a href="{{ route('contact') }}" @class(['is-active' => request()->routeIs('contact')])>Contact</a>
                    </nav>

                    <a href="{{ route('contact') }}" class="btn btn-primary site-header-cta">Démonstration</a>

                    <button type="button" class="nav-toggle" aria-controls="site-nav" aria-expanded="false" aria-label="Ouvrir le menu">
                        <span></span>
                    </button>
                </div>
            </header>

            <main>
                @yield('content')
            </main>

            <footer class="site-footer">
                <div class="footer-main">
                    <div class="footer-brand">
                        <a href="{{ route('home') }}" class="brand-lockup footer-logo" aria-label="{{ config('accessevent.brand.name') }}">
                            <img src="{{ asset('images/logo/accesslogo.png') }}" alt="" class="brand-image">
                        </a>
                        <p>{{ $footerSettings['footer_tagline'] }}</p>
                    </div>

                    <div class="footer-column">
                        <p class="footer-title">Navigation</p>
                        <a href="{{ route('home') }}">Accueil</a>
                        <a href="{{ route('solutions.index') }}">Solutions</a>
                        <a href="{{ route('ecosystem') }}">Écosystème</a>
                        <a href="{{ route('tech-videos.index') }}">Vidéos techniques</a>
                        <a href="{{ route('about') }}">Qui sommes-nous</a>
                        <a href="{{ route('contact') }}">Contact</a>
                    </div>

                    <div class="footer-column">
                        <p class="footer-title">Contact</p>
                        @if ($footerSettings['footer_email'])
                            <a href="mailto:{{ $footerSettings['footer_email'] }}">{{ $footerSettings['footer_email'] }}</a>
                        @endif
                        @if ($footerSettings['footer_phone'])
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $footerSettings['footer_phone']) }}">{{ $footerSettings['footer_phone'] }}</a>
                        @endif
                        @if ($footerSettings['footer_whatsapp'])
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $footerSettings['footer_whatsapp']) }}" target="_blank" rel="noopener">WhatsApp : {{ $footerSettings['footer_whatsapp'] }}</a>
                        @endif
                    </div>

                    <div class="footer-column">
                        <p class="footer-title">Informations</p>
                        @if ($footerSettings['footer_address'])
                            <span>{{ $footerSettings['footer_address'] }}</span>
                        @endif
                        @if ($footerSettings['footer_hours'])
                            <span>{{ $footerSettings['footer_hours'] }}</span>
                        @endif
                        <div class="footer-socials">
                            @if ($footerSettings['footer_linkedin_url'] && $footerSettings['footer_linkedin_url'] !== '#')
                                <a href="{{ $footerSettings['footer_linkedin_url'] }}" target="_blank" rel="noopener">LinkedIn</a>
                            @endif
                            @if ($footerSettings['footer_facebook_url'] && $footerSettings['footer_facebook_url'] !== '#')
                                <a href="{{ $footerSettings['footer_facebook_url'] }}" target="_blank" rel="noopener">Facebook</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>{{ $footerSettings['footer_legal_text'] }}</p>
                    <p>&copy; {{ date('Y') }} {{ config('accessevent.brand.company') }}. Tous droits réservés.</p>
                </div>
            </footer>
        </div>
    </body>
</html>
