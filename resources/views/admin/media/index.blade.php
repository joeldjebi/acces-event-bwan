@extends('layouts.admin', [
    'title' => 'Médias',
    'pageHeading' => 'Bibliothèque multimédia',
    'moduleLabel' => 'Médias',
])

@section('content')
    <section class="admin-card">
        <div class="admin-section-header">
            <div class="section-heading compact">
                <p class="eyebrow">Images</p>
                <h2>Médias réutilisables sur le site.</h2>
                <p>Ajoutez les images de solutions, applications, pages et contenus visuels. Copiez l'URL pour la réutiliser dans les formulaires.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="media-upload-form mt-6">
            @csrf

            <div class="form-grid">
                <div class="form-field">
                    <label for="file">Image</label>
                    <input id="file" name="file" type="file" accept="image/*" class="form-input" required>
                    @error('file') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label for="name">Nom</label>
                    <input id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="Hero billetterie">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="alt_text">Texte alternatif</label>
                <input id="alt_text" name="alt_text" class="form-input" value="{{ old('alt_text') }}" placeholder="Description courte de l'image">
                @error('alt_text') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ajouter le média</button>
            </div>
        </form>
    </section>

    <section class="media-grid-section">
        @forelse ($mediaAssets as $media)
            <article class="media-card">
                <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: $media->name }}">
                <div>
                    <h2>{{ $media->name }}</h2>
                    <p>{{ $media->readableSize() }} - {{ $media->mime_type }}</p>
                    <input class="form-input" value="{{ $media->url }}" readonly>
                    <form method="POST" action="{{ route('admin.media.destroy', $media) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </article>
        @empty
            <article class="admin-card">
                <p>Aucun média pour le moment.</p>
            </article>
        @endforelse
    </section>

    <div class="mt-6">
        {{ $mediaAssets->links() }}
    </div>
@endsection
