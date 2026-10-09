@extends('layouts.admin', [
    'title' => 'Modifier '.$application->name,
    'pageHeading' => 'Modifier '.$application->name,
    'moduleLabel' => 'Applications',
])

@section('content')
    <section class="admin-card">
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        @include('admin.applications.partials.form', [
            'action' => route('admin.applications.update', $application),
            'method' => 'PUT',
            'submitLabel' => 'Enregistrer',
        ])
    </section>
@endsection
