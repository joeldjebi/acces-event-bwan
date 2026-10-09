<form method="POST" action="{{ $action }}" class="admin-form" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="form-field">
            <label for="label">Libellé</label>
            <input id="label" name="label" class="form-input" value="{{ old('label', $video->label) }}" required>
            @error('label') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-field">
            <label for="slug">Identifiant URL</label>
            <input id="slug" name="slug" class="form-input" value="{{ old('slug', $video->slug) }}" placeholder="généré automatiquement si vide">
            @error('slug') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="position">Ordre</label>
            <input id="position" name="position" type="number" min="0" class="form-input" value="{{ old('position', $video->position) }}" required>
            @error('position') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-field">
            <label for="youtube_url">Lien vidéo YouTube</label>
            <input id="youtube_url" name="youtube_url" type="url" class="form-input" value="{{ old('youtube_url', $video->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=..." required>
            @error('youtube_url') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-field">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-input form-textarea" rows="5">{{ old('description', $video->description) }}</textarea>
        @error('description') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="tags">Mots-clés</label>
            <input id="tags" name="tags" class="form-input" value="{{ old('tags', $video->tagsInput()) }}" placeholder="Démonstration, scan, contrôle">
            @error('tags') <p class="form-error">{{ $message }}</p> @enderror
            <p class="field-help">Optionnel. Séparez les mots-clés par des virgules.</p>
        </div>

        <div class="form-field">
            <label for="cover_image_file">Image de couverture</label>
            <input id="cover_image_file" name="cover_image_file" type="file" accept="image/*" class="form-input">
            @error('cover_image_file') <p class="form-error">{{ $message }}</p> @enderror
            <p class="field-help">Formats image acceptés. Taille maximale : 5 Mo.</p>
            @if ($video->cover_image_path)
                <div class="media-current-preview">
                    <img src="{{ $video->cover_image_path }}" alt="{{ $video->label }}">
                    <span>Image actuelle</span>
                </div>
            @endif
        </div>
    </div>

    <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" class="size-4 rounded border-ae-line text-ae-primary" @checked(old('is_published', $video->is_published))>
        Publier cette vidéo
    </label>

    <div class="form-actions">
        <a href="{{ route('admin.tech-videos.index') }}" class="btn btn-secondary">Retour</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

@if ($video->exists)
    <form method="POST" action="{{ route('admin.tech-videos.destroy', $video) }}" class="delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Supprimer la vidéo</button>
    </form>
@endif
