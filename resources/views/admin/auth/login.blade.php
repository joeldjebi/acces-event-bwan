<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="{{ config('accessevent.colors.secondary') }}">

        <title>Connexion administrateur - {{ config('accessevent.brand.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-page antialiased">
        <main class="auth-card">
            <a href="{{ route('home') }}" class="brand-lockup" aria-label="{{ config('accessevent.brand.name') }}">
                <img src="{{ \App\Models\SiteSetting::logoUrl() }}" alt="" class="h-14 w-auto rounded">
                <span class="sr-only">{{ config('accessevent.brand.name') }}</span>
            </a>

            <div class="section-heading compact mt-8">
                <p class="eyebrow">Administration</p>
                <h1 class="text-3xl font-black text-ae-secondary">Connexion administrateur</h1>
                <p>Accédez à l'espace de gestion ACCÈS EVENT.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error mt-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                <div class="form-field">
                    <label for="email">Adresse e-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-input" autocomplete="email" required autofocus>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="password">Mot de passe</label>
                    <input id="password" name="password" type="password" class="form-input" autocomplete="current-password" required>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded border-ae-line text-ae-primary">
                    Se souvenir de moi
                </label>

                <button type="submit" class="btn btn-primary w-full">Se connecter</button>
            </form>
        </main>
    </body>
</html>
