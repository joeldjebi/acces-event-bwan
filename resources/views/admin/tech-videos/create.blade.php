@extends('layouts.admin', [
    'title' => 'Nouvelle vidéo tech',
    'pageHeading' => 'Nouvelle vidéo tech',
    'moduleLabel' => 'Vidéos techniques',
])

@section('content')
    <section class="admin-card">
        @include('admin.tech-videos.partials.form', [
            'action' => route('admin.tech-videos.store'),
            'method' => 'POST',
            'submitLabel' => 'Créer la vidéo',
        ])
    </section>
@endsection
