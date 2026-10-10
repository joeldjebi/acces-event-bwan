<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="{{ config('accessevent.colors.secondary') }}">

        <title>{{ isset($title) ? $title.' - Administration' : 'Administration' }} - {{ config('accessevent.brand.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-ae-muted text-ae-ink antialiased">
        <div class="admin-shell">
            <aside class="admin-sidebar">
                <a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="Administration {{ config('accessevent.brand.name') }}">
                    <img src="{{ \App\Models\SiteSetting::logoUrl() }}" alt="">
                    <span>Administration</span>
                </a>

                <nav class="admin-nav" aria-label="Navigation administration">
                    <p class="admin-nav-label">Gestion</p>
                    <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')]) @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="{{ route('admin.solutions.index') }}" @class(['is-active' => request()->routeIs('admin.solutions.*')]) @if (request()->routeIs('admin.solutions.*')) aria-current="page" @endif>
                        <span>Solutions</span>
                    </a>
                    <a href="{{ route('admin.applications.index') }}" @class(['is-active' => request()->routeIs('admin.applications.*')]) @if (request()->routeIs('admin.applications.*')) aria-current="page" @endif>
                        <span>Applications</span>
                    </a>
                    <a href="{{ route('admin.tech-videos.index') }}" @class(['is-active' => request()->routeIs('admin.tech-videos.*')]) @if (request()->routeIs('admin.tech-videos.*')) aria-current="page" @endif>
                        <span>Vidéos techniques</span>
                    </a>
                    <a href="{{ route('admin.pages.index') }}" @class(['is-active' => request()->routeIs('admin.pages.*')]) @if (request()->routeIs('admin.pages.*')) aria-current="page" @endif>
                        <span>Pages</span>
                    </a>
                    <a href="{{ route('admin.media.index') }}" @class(['is-active' => request()->routeIs('admin.media.*')]) @if (request()->routeIs('admin.media.*')) aria-current="page" @endif>
                        <span>Médias</span>
                    </a>
                    <a href="{{ route('admin.demo-requests.index') }}" @class(['is-active' => request()->routeIs('admin.demo-requests.*')]) @if (request()->routeIs('admin.demo-requests.*')) aria-current="page" @endif>
                        <span>Demandes</span>
                    </a>
                    <p class="admin-nav-label">Paramètres</p>
                    <a href="{{ route('admin.settings.footer.edit') }}" @class(['is-active' => request()->routeIs('admin.settings.footer.*')]) @if (request()->routeIs('admin.settings.footer.*')) aria-current="page" @endif>
                        <span>Pied de page</span>
                    </a>
                    <a href="{{ route('admin.settings.brand.edit') }}" @class(['is-active' => request()->routeIs('admin.settings.brand.*')]) @if (request()->routeIs('admin.settings.brand.*')) aria-current="page" @endif>
                        <span>Logo</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" @class(['is-active' => request()->routeIs('admin.users.*')]) @if (request()->routeIs('admin.users.*')) aria-current="page" @endif>
                        <span>Administrateurs</span>
                    </a>
                    @if (auth()->user()->isOwner())
                        <a href="{{ route('admin.activity.index') }}" @class(['is-active' => request()->routeIs('admin.activity.*')]) @if (request()->routeIs('admin.activity.*')) aria-current="page" @endif>
                            <span>Journal d'activité</span>
                        </a>
                    @endif
                </nav>
            </aside>

            <div class="admin-main">
                <header class="admin-topbar">
                    <div>
                        <p class="eyebrow">{{ $moduleLabel ?? 'Administration' }}</p>
                        <h1>{{ $pageHeading ?? 'Socle technique' }}</h1>
                    </div>
                    <div class="admin-userbar">
                        <span>{{ auth()->user()->name }}</span>
                        <a href="{{ route('home') }}" class="btn btn-secondary" target="_blank" rel="noopener">Voir le site</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-ghost">Déconnexion</button>
                        </form>
                    </div>
                </header>

                <main class="admin-content">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
