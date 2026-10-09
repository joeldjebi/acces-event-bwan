@extends('layouts.admin', [
    'title' => 'Pied de page',
    'pageHeading' => 'Gérer le pied de page',
    'moduleLabel' => 'Paramètres',
])

@section('content')
    <section class="admin-card">
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.footer.update') }}" class="admin-form">
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="footer_tagline">Phrase d'accroche du pied de page</label>
                <textarea id="footer_tagline" name="footer_tagline" class="form-input form-textarea" rows="3">{{ old('footer_tagline', $settings['footer_tagline']) }}</textarea>
                @error('footer_tagline') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="footer_email">Email</label>
                    <input id="footer_email" name="footer_email" type="email" class="form-input" value="{{ old('footer_email', $settings['footer_email']) }}">
                    @error('footer_email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label for="footer_phone">Téléphone</label>
                    <input id="footer_phone" name="footer_phone" class="form-input" value="{{ old('footer_phone', $settings['footer_phone']) }}">
                    @error('footer_phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="footer_whatsapp">WhatsApp</label>
                    <input id="footer_whatsapp" name="footer_whatsapp" class="form-input" value="{{ old('footer_whatsapp', $settings['footer_whatsapp']) }}">
                    @error('footer_whatsapp') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label for="footer_hours">Horaires</label>
                    <input id="footer_hours" name="footer_hours" class="form-input" value="{{ old('footer_hours', $settings['footer_hours']) }}">
                    @error('footer_hours') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="footer_address">Adresse</label>
                <textarea id="footer_address" name="footer_address" class="form-input form-textarea" rows="3">{{ old('footer_address', $settings['footer_address']) }}</textarea>
                @error('footer_address') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="footer_linkedin_url">URL LinkedIn</label>
                    <input id="footer_linkedin_url" name="footer_linkedin_url" class="form-input" value="{{ old('footer_linkedin_url', $settings['footer_linkedin_url']) }}">
                    @error('footer_linkedin_url') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label for="footer_facebook_url">URL Facebook</label>
                    <input id="footer_facebook_url" name="footer_facebook_url" class="form-input" value="{{ old('footer_facebook_url', $settings['footer_facebook_url']) }}">
                    @error('footer_facebook_url') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="footer_legal_text">Mention légale courte</label>
                <textarea id="footer_legal_text" name="footer_legal_text" class="form-input form-textarea" rows="3">{{ old('footer_legal_text', $settings['footer_legal_text']) }}</textarea>
                @error('footer_legal_text') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('home') }}" class="btn btn-secondary">Voir le site</a>
                <button type="submit" class="btn btn-primary">Enregistrer le pied de page</button>
            </div>
        </form>
    </section>
@endsection
