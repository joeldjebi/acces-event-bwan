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

            <div class="form-field">
                <label for="name">Nom interne</label>
                <input id="name" name="name" class="form-input" value="{{ old('name', $page->name) }}" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
                <p class="field-help">Visible uniquement dans l'administration.</p>
            </div>

            <p class="field-help">Seuls les champs affichés sur cette page du site sont proposés. Le libellé indique où chacun apparaît.</p>

            @foreach ($page->editableFields() as $field => $label)
                <div class="form-field">
                    <label for="{{ $field }}">{{ $label }}</label>
                    @if (in_array($field, ['summary', 'body'], true))
                        <textarea id="{{ $field }}" name="{{ $field }}" class="form-input form-textarea" rows="{{ $field === 'body' ? 6 : 3 }}">{{ old($field, $page->{$field}) }}</textarea>
                    @else
                        <input id="{{ $field }}" name="{{ $field }}" class="form-input" value="{{ old($field, $page->{$field}) }}">
                    @endif
                    @error($field) <p class="form-error">{{ $message }}</p> @enderror
                    @if ($field === 'body' && $page->key === 'about')
                        <p class="field-help">Un paragraphe par ligne.</p>
                    @endif
                </div>
            @endforeach

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

            <div class="form-actions">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </section>
@endsection
