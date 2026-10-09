@extends('layouts.admin', [
    'title' => 'Modifier '.$video->label,
    'pageHeading' => 'Modifier la vidéo tech',
    'moduleLabel' => 'Vidéos techniques',
])

@section('content')
    <section class="admin-card">
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        @include('admin.tech-videos.partials.form', [
            'action' => route('admin.tech-videos.update', $video),
            'method' => 'PUT',
            'submitLabel' => 'Enregistrer',
        ])
    </section>
@endsection
