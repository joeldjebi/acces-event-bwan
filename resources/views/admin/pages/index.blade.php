@extends('layouts.admin', [
    'title' => 'Pages',
    'pageHeading' => 'Gestion des pages',
    'moduleLabel' => 'Pages',
])

@section('content')
    <section class="admin-card">
        <div class="section-heading compact">
            <p class="eyebrow">Contenus</p>
            <h2>Pages principales du site public.</h2>
            <p>Modifiez les titres, textes, appels à l'action et métadonnées SEO des pages fixes.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif

        <div class="table-wrap mt-6">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Clé</th>
                        <th>Statut</th>
                        <th>SEO</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>
                                <strong>{{ $page->name }}</strong>
                                <span>{{ $page->heading }}</span>
                            </td>
                            <td>{{ $page->key }}</td>
                            <td>
                                <span class="status-pill {{ $page->is_published ? 'is-live' : 'is-draft' }}">
                                    {{ $page->is_published ? 'Publiée' : 'Brouillon' }}
                                </span>
                            </td>
                            <td>{{ $page->seo_title ?: 'À compléter' }}</td>
                            <td class="table-actions">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-secondary">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
