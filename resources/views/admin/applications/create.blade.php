@extends('layouts.admin', [
    'title' => 'Nouvelle application',
    'pageHeading' => 'Nouvelle application',
    'moduleLabel' => 'Applications',
])

@section('content')
    <section class="admin-card">
        @include('admin.applications.partials.form', [
            'action' => route('admin.applications.store'),
            'method' => 'POST',
            'submitLabel' => 'Créer l application',
        ])
    </section>
@endsection
