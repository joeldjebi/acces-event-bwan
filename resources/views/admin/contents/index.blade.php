@extends('layouts.admin', [
    'title' => 'Textes et images',
    'pageHeading' => 'Textes et images du site',
    'moduleLabel' => 'Contenus',
])

@section('content')
    <section class="admin-card">
        <div class="section-heading compact">
            <p class="eyebrow">Site public</p>
            <h2>Tous les textes et images, page par page.</h2>
            <p>Choisissez une page. Un champ laissé vide reprend son texte d'origine. Les contenus propres à chaque domaine, application ou vidéo se modifient dans leur rubrique.</p>
        </div>

        <div class="content-sections mt-6">
            @foreach ($sections as $section)
                <a href="{{ route('admin.contents.edit', $section['key']) }}" class="content-section-card">
                    <strong>{{ $section['label'] }}</strong>
                    <span>{{ $section['description'] }}</span>
                    <small>
                        {{ $section['total'] }} {{ $section['total'] > 1 ? 'contenus' : 'contenu' }}
                        @if ($section['custom'] > 0)
                            · <b>{{ $section['custom'] }} {{ $section['custom'] > 1 ? 'personnalisés' : 'personnalisé' }}</b>
                        @endif
                    </small>
                </a>
            @endforeach
        </div>
    </section>
@endsection
