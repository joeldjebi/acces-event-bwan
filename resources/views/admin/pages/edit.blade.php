@extends('layouts.admin', [
    'title' => 'Modifier '.$page->name,
    'pageHeading' => 'Modifier '.$page->name,
    'moduleLabel' => 'Pages',
])

@section('content')
    <section class="admin-card">
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="admin-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-field">
                    <label for="name">Nom interne</label>
                    <input id="name" name="name" class="form-input" value="{{ old('name', $page->name) }}" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label for="eyebrow">Sur-titre</label>
                    <input id="eyebrow" name="eyebrow" class="form-input" value="{{ old('eyebrow', $page->eyebrow) }}">
                    @error('eyebrow') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="heading">Titre principal</label>
                <input id="heading" name="heading" class="form-input" value="{{ old('heading', $page->heading) }}">
                @error('heading') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-field">
                <label for="summary">Résumé / introduction</label>
                <textarea id="summary" name="summary" class="form-input form-textarea" rows="3">{{ old('summary', $page->summary) }}</textarea>
                @error('summary') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-field">
                <label for="body">Corps de page</label>
                <textarea id="body" name="body" class="form-input form-textarea" rows="8">{{ old('body', $page->body) }}</textarea>
                @error('body') <p class="form-error">{{ $message }}</p> @enderror
                <p class="field-help">Une ligne ou un paragraphe par bloc de texte public.</p>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="cta_label">Libellé de l'appel à l'action</label>
                    <input id="cta_label" name="cta_label" class="form-input" value="{{ old('cta_label', $page->cta_label) }}">
                    @error('cta_label') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label for="cta_url">URL de l'appel à l'action</label>
                    <input id="cta_url" name="cta_url" class="form-input" value="{{ old('cta_url', $page->cta_url) }}">
                    @error('cta_url') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="seo_title">Titre de référencement</label>
                    <input id="seo_title" name="seo_title" class="form-input" value="{{ old('seo_title', $page->seo_title) }}">
                    @error('seo_title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label for="seo_description">Description de référencement</label>
                    <textarea id="seo_description" name="seo_description" class="form-input form-textarea" rows="3">{{ old('seo_description', $page->seo_description) }}</textarea>
                    @error('seo_description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" class="size-4 rounded border-ae-line text-ae-primary" @checked(old('is_published', $page->is_published))>
                Publier cette page
            </label>

            <div class="form-actions">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </section>
@endsection
