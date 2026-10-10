<form method="POST" action="{{ $action }}" class="admin-form" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="form-field">
            <label for="name">Nom</label>
            <input id="name" name="name" class="form-input" value="{{ old('name', $application->name) }}" required>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-field">
            <label for="slug">Identifiant URL</label>
            <input id="slug" name="slug" class="form-input" value="{{ old('slug', $application->slug) }}" placeholder="généré automatiquement si vide">
            @error('slug') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="position">Ordre</label>
            <input id="position" name="position" type="number" min="0" class="form-input" value="{{ old('position', $application->position) }}" required>
            @error('position') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-field">
            <label for="logo_file">Logo / image</label>
            <input id="logo_file" name="logo_file" type="file" accept="image/*" class="form-input">
            @error('logo_file') <p class="form-error">{{ $message }}</p> @enderror
            <p class="field-help">Formats image acceptés. Taille maximale : 5 Mo.</p>
            @if ($application->logo_path)
                <div class="media-current-preview">
                    <img src="{{ $application->logo_path }}" alt="{{ $application->name }}">
                    <span>Image actuelle</span>
                </div>
                <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink mt-2">
                    <input type="checkbox" name="remove_logo" value="1" class="size-4 rounded border-ae-line text-ae-primary">
                    Retirer le logo (les initiales de l'application s'afficheront à la place)
                </label>
            @endif
        </div>
    </div>

    <div class="form-field">
        <label for="role">Rôle dans l'écosystème</label>
        <textarea id="role" name="role" class="form-input form-textarea" rows="3">{{ old('role', $application->role) }}</textarea>
        @error('role') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="description">Description détaillée</label>
        <textarea id="description" name="description" class="form-input form-textarea" rows="6">{{ old('description', $application->description) }}</textarea>
        @error('description') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label>Solutions associées</label>
        <div class="checkbox-grid">
            @foreach ($solutions as $solution)
                <label>
                    <input type="checkbox" name="solutions[]" value="{{ $solution->id }}" @checked(in_array($solution->id, old('solutions', $selectedSolutions), true))>
                    <span>{{ $solution->title }}</span>
                </label>
            @endforeach
        </div>
        @error('solutions') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" class="size-4 rounded border-ae-line text-ae-primary" @checked(old('is_published', $application->is_published))>
        Publier cette application
    </label>

    <div class="form-actions">
        <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">Retour</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

@if ($application->exists)
    <form method="POST" action="{{ route('admin.applications.destroy', $application) }}" class="delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Supprimer l'application</button>
    </form>
@endif
