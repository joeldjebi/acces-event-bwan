@extends('layouts.admin', [
    'title' => 'Modifier '.$solution->title,
    'pageHeading' => 'Modifier '.$solution->title,
    'moduleLabel' => 'Solutions',
])

@section('content')
    <section class="admin-card">
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        @include('admin.solutions.partials.form', [
            'action' => route('admin.solutions.update', $solution),
            'method' => 'PUT',
            'submitLabel' => 'Enregistrer',
        ])
    </section>
@endsection
