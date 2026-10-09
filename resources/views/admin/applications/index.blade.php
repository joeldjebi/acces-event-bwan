@extends('layouts.admin', [
    'title' => 'Applications',
    'pageHeading' => 'Gestion des applications',
    'moduleLabel' => 'Applications',
])

@section('content')
    <section class="admin-card">
        <div class="admin-section-header">
            <div class="section-heading compact">
                <p class="eyebrow">Écosystème</p>
                <h2>Applications et plateformes propriétaires.</h2>
                <p>Administrez les plateformes visibles sur la page écosystème et leurs liens avec les solutions.</p>
            </div>
            <a href="{{ route('admin.applications.create') }}" class="btn btn-primary">Nouvelle application</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif

        <div class="table-wrap mt-6">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Application</th>
                        <th>Statut</th>
                        <th>Solutions</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        <tr>
                            <td>{{ $application->position }}</td>
                            <td>
                                <strong>{{ $application->name }}</strong>
                                <span>{{ $application->slug }}</span>
                            </td>
                            <td>
                                <span class="status-pill {{ $application->is_published ? 'is-live' : 'is-draft' }}">
                                    {{ $application->is_published ? 'Publiée' : 'Brouillon' }}
                                </span>
                            </td>
                            <td>{{ $application->solutions_count }} solution(s)</td>
                            <td class="table-actions">
                                <a href="{{ route('ecosystem') }}" class="text-link" target="_blank" rel="noopener">Voir</a>
                                <a href="{{ route('admin.applications.edit', $application) }}" class="btn btn-secondary">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Aucune application pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    </section>
@endsection
