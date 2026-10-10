<form method="POST" action="{{ $action }}" class="admin-form" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="form-field">
            <label for="title">Titre</label>
            <input id="title" name="title" class="form-input" value="{{ old('title', $solution->title) }}" required>
            @error('title') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-field">
            <label for="slug">Identifiant URL</label>
            <input id="slug" name="slug" class="form-input" value="{{ old('slug', $solution->slug) }}" placeholder="généré automatiquement si vide">
            @error('slug') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="subtitle">Accroche</label>
            <input id="subtitle" name="subtitle" class="form-input" value="{{ old('subtitle', $solution->subtitle) }}">
            @error('subtitle') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-field">
            <label for="position">Ordre</label>
            <input id="position" name="position" type="number" min="0" class="form-input" value="{{ old('position', $solution->position) }}" required>
            @error('position') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-field">
        <label for="short_description">Description courte</label>
        <textarea id="short_description" name="short_description" class="form-input form-textarea" rows="3">{{ old('short_description', $solution->short_description) }}</textarea>
        @error('short_description') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="content">Contenu principal</label>
        <textarea id="content" name="content" class="form-input form-textarea" rows="8">{{ old('content', $solution->content) }}</textarea>
        @error('content') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="image_file">Image d'illustration du domaine</label>
        <input id="image_file" name="image_file" type="file" accept="image/*" class="form-input">
        @error('image_file') <p class="form-error">{{ $message }}</p> @enderror
        <p class="field-help">Formats image acceptés. Taille maximale : 5 Mo. L'image sera affichée sur la carte d'accueil et la page détail.</p>
        @if ($solution->image_path)
            <div class="media-current-preview">
                <img src="{{ $solution->image_path }}" alt="{{ $solution->title }}">
                <span>Image actuelle</span>
            </div>
            <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink mt-2">
                <input type="checkbox" name="remove_image" value="1" class="size-4 rounded border-ae-line text-ae-primary">
                Retirer l'image (le pictogramme du domaine s'affichera à la place)
            </label>
        @endif
    </div>

    <div class="form-field">
        <label for="benefits">Bénéfices</label>
        <textarea id="benefits" name="benefits" class="form-input form-textarea" rows="5" placeholder="Plus de ventes | Vous êtes présent sur tous les canaux.">{{ old('benefits', $benefitsText) }}</textarea>
        @error('benefits') <p class="form-error">{{ $message }}</p> @enderror
        <p class="field-help">Une ligne par bénéfice. Format conseillé : Titre | Description.</p>
    </div>

    <div class="form-field">
        <label for="tools">Outils associés</label>
        <textarea id="tools" name="tools" class="form-input form-textarea" rows="5" placeholder="ACCÈS CHECK | Application de contrôle.">{{ old('tools', $toolsText) }}</textarea>
        @error('tools') <p class="form-error">{{ $message }}</p> @enderror
        <p class="field-help">Une ligne par outil. Format conseillé : Nom | Description.</p>
    </div>

    <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" class="size-4 rounded border-ae-line text-ae-primary" @checked(old('is_published', $solution->is_published))>
        Publier cette solution
    </label>

    <div class="form-actions">
        <a href="{{ route('admin.solutions.index') }}" class="btn btn-secondary">Retour</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

@if ($solution->exists)
    <form method="POST" action="{{ route('admin.solutions.destroy', $solution) }}" class="delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Supprimer la solution</button>
    </form>
@endif
