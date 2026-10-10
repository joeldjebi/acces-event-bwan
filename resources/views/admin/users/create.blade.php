@extends('layouts.admin', [
    'title' => 'Nouvel administrateur',
    'pageHeading' => 'Nouvel administrateur',
    'moduleLabel' => 'Paramètres',
])

@section('content')
    <section class="admin-card">
        @include('admin.users.partials.form', [
            'action' => route('admin.users.store'),
            'method' => 'POST',
            'submitLabel' => "Créer l'administrateur",
        ])
    </section>
@endsection
