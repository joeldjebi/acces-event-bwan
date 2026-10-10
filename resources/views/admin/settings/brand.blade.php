@extends('layouts.admin', [
    'title' => 'Logo',
    'pageHeading' => 'Logo du site',
    'moduleLabel' => 'Paramètres',
])

@section('content')
    <section class="admin-card">
        <div class="section-heading compact">
            <p class="eyebrow">Identité</p>
            <h2>Le logo affiché partout sur le site.</h2>
            <p>En-tête et pied de page du site, page Écosystème, administration et page de connexion.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif

        <div class="brand-preview mt-6">
            <figure>
                <div class="brand-preview-light"><img src="{{ $logoUrl }}" alt="Logo actuel sur fond clair"></div>
                <figcaption>Sur fond clair</figcaption>
            </figure>
            <figure>
                <div class="brand-preview-dark"><img src="{{ $logoUrl }}" alt="Logo actuel sur fond foncé"></div>
                <figcaption>Sur fond foncé</figcaption>
            </figure>
        </div>
        <p class="field-help mt-4 mb-6">{{ $isCustom ? 'Logo personnalisé, envoyé depuis cette page.' : "Logo d'origine du site." }}</p>

        <form method="POST" action="{{ route('admin.settings.brand.update') }}" class="admin-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="logo_file">Nouveau logo</label>
                <input id="logo_file" name="logo_file" type="file" accept="image/png,image/jpeg,image/webp" class="form-input">
                @error('logo_file') <p class="form-error">{{ $message }}</p> @enderror
                <p class="field-help">PNG, JPG ou WebP, 2 Mo maximum, au moins 120 × 40 pixels. Un PNG à fond transparent donne le meilleur rendu.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Mettre en ligne ce logo</button>
            </div>
        </form>

        @if ($isCustom)
            <form method="POST" action="{{ route('admin.settings.brand.update') }}" class="delete-form" onsubmit="return confirm('Revenir au logo d\'origine ?');">
                @csrf
                @method('PUT')
                <input type="hidden" name="reset_logo" value="1">
                <button type="submit" class="btn btn-secondary">Rétablir le logo d'origine</button>
            </form>
        @endif
    </section>
@endsection
