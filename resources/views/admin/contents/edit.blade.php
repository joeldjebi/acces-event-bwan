@extends('layouts.admin', [
    'title' => 'Textes : '.$section['label'],
    'pageHeading' => $section['label'],
    'moduleLabel' => 'Textes et images',
])

@section('content')
    <section class="admin-card">
        <div class="section-heading compact">
            <p class="eyebrow">Textes et images</p>
            <p>{{ $section['description'] }}</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error mt-6">{{ $errors->first() }}</div>
        @endif

        <p class="field-help mt-6 mb-6">Le texte grisé dans chaque champ est le texte d'origine : laissez le champ vide pour le conserver, ou videz-le pour y revenir.</p>

        <form method="POST" action="{{ route('admin.contents.update', $sectionKey) }}" class="admin-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @foreach ($section['fields'] as $name => $field)
                @php
                    $key = $sectionKey.'.'.$name;
                    $type = $field['type'] ?? 'text';
                    $custom = \App\Support\SiteContent::override($key);
                @endphp

                <div @class(['form-field', 'content-field', 'is-custom' => $custom !== null])>
                    <label for="{{ $name }}">
                        {{ $field['label'] }}
                        @if ($custom !== null) <span class="status-pill kind-update">Personnalisé</span> @endif
                    </label>

                    @if ($type === 'image')
                        <div class="content-image-row">
                            <img src="{{ site_image($key) }}" alt="" class="content-image-preview">
                            <div>
                                <input id="{{ $name }}" name="{{ $name }}" type="file" accept="image/png,image/jpeg,image/webp" class="form-input">
                                <p class="field-help">PNG, JPG ou WebP, 5 Mo maximum.</p>
                                @if ($custom !== null)
                                    <label class="inline-flex items-center gap-2 text-sm font-bold text-ae-ink mt-2">
                                        <input type="checkbox" name="remove_{{ $name }}" value="1" class="size-4 rounded border-ae-line text-ae-primary">
                                        Revenir à l'image d'origine
                                    </label>
                                @endif
                            </div>
                        </div>
                    @elseif ($type === 'text')
                        <input id="{{ $name }}" name="{{ $name }}" class="form-input" value="{{ old($name, $custom) }}" placeholder="{{ $field['default'] }}">
                    @else
                        <textarea id="{{ $name }}" name="{{ $name }}" class="form-input form-textarea" rows="{{ $type === 'lines' ? 5 : 3 }}" placeholder="{{ $field['default'] }}">{{ old($name, $custom) }}</textarea>
                    @endif

                    @error($name) <p class="form-error">{{ $message }}</p> @enderror
                    @isset($field['help']) <p class="field-help">{{ $field['help'] }}</p> @endisset
                </div>
            @endforeach

            <div class="form-actions">
                <a href="{{ route('admin.contents.index') }}" class="btn btn-secondary">Toutes les pages</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </section>
@endsection
