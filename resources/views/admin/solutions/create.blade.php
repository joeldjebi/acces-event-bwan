@extends('layouts.admin', [
    'title' => 'Nouvelle solution',
    'pageHeading' => 'Nouvelle solution',
    'moduleLabel' => 'Solutions',
])

@section('content')
    <section class="admin-card">
        @include('admin.solutions.partials.form', [
            'action' => route('admin.solutions.store'),
            'method' => 'POST',
            'submitLabel' => 'Créer la solution',
        ])
    </section>
@endsection
