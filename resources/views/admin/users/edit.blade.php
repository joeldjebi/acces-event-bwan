@extends('layouts.admin', [
    'title' => 'Modifier un administrateur',
    'pageHeading' => 'Modifier « '.$user->name.' »',
    'moduleLabel' => 'Paramètres',
])

@section('content')
    <section class="admin-card">
        @include('admin.users.partials.form', [
            'action' => route('admin.users.update', $user),
            'method' => 'PUT',
            'submitLabel' => 'Enregistrer',
        ])
    </section>
@endsection
