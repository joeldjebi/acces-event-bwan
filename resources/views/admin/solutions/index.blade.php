@extends('layouts.admin', [
    'title' => 'Solutions',
    'pageHeading' => 'Gestion des solutions',
    'moduleLabel' => 'Solutions',
])

@section('content')
    <section class="admin-card">
        <div class="admin-section-header">
            <div class="section-heading compact">
                <p class="eyebrow">Solutions</p>
                <h2>Les domaines d'activité ACCÈS EVENT.</h2>
                <p>Gérez les textes, bénéfices, outils associés, ordre d'affichage et publication.</p>
            </div>
            <a href="{{ route('admin.solutions.create') }}" class="btn btn-primary">Nouvelle solution</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif

        <div class="table-wrap mt-6">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Solution</th>
                        <th>Statut</th>
                        <th>Contenu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($solutions as $solution)
                        <tr>
                            <td>{{ $solution->position }}</td>
                            <td>
                                <strong>{{ $solution->title }}</strong>
                                <span>{{ $solution->slug }}</span>
                            </td>
                            <td>
                                <span class="status-pill {{ $solution->is_published ? 'is-live' : 'is-draft' }}">
                                    {{ $solution->is_published ? 'Publiée' : 'Brouillon' }}
                                </span>
                            </td>
                            <td>{{ $solution->benefits_count }} bénéfices - {{ $solution->tools_count }} outils</td>
                            <td class="table-actions">
                                <a href="{{ route('solutions.show', $solution) }}" class="text-link" target="_blank" rel="noopener">Voir</a>
                                <a href="{{ route('admin.solutions.edit', $solution) }}" class="btn btn-secondary">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Aucune solution pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $solutions->links() }}
        </div>
    </section>
@endsection
