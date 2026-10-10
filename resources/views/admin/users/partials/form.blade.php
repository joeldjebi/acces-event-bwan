<form method="POST" action="{{ $action }}" class="admin-form">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <div class="form-field">
            <label for="name">Nom</label>
            <input id="name" name="name" class="form-input" value="{{ old('name', $user->name) }}" autocomplete="name" required>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-field">
            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $user->email) }}" autocomplete="email" required>
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
            <p class="field-help">Sert d'identifiant de connexion.</p>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="password">{{ $user->exists ? 'Nouveau mot de passe' : 'Mot de passe' }}</label>
            <input id="password" name="password" type="password" class="form-input" autocomplete="new-password" @unless ($user->exists) required @endunless>
            @error('password') <p class="form-error">{{ $message }}</p> @enderror
            <p class="field-help">12 caractères minimum, avec des lettres et des chiffres.@if ($user->exists) Laissez vide pour le conserver.@endif</p>
        </div>

        <div class="form-field">
            <label for="password_confirmation">Confirmation du mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password" @unless ($user->exists) required @endunless>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Retour</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>
