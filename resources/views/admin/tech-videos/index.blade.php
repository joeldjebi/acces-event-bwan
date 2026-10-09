@extends('layouts.admin', [
    'title' => 'Vidéos techniques',
    'pageHeading' => 'Gestion des vidéos tech',
    'moduleLabel' => 'Vidéos techniques',
])

@section('content')
    <section class="admin-card">
        <div class="admin-section-header">
            <div class="section-heading compact">
                <p class="eyebrow">Vidéos techniques</p>
                <h2>Contenus YouTube techniques.</h2>
                <p>Administrez les vidéos visibles dans le menu Vidéos techniques.</p>
            </div>
            <a href="{{ route('admin.tech-videos.create') }}" class="btn btn-primary">Nouvelle vidéo</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif

        <div class="table-wrap mt-6">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Vidéo</th>
                        <th>Mots-clés</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($videos as $video)
                        <tr>
                            <td>{{ $video->position }}</td>
                            <td>
                                <strong>{{ $video->label }}</strong>
                                <span>{{ $video->youtube_url }}</span>
                            </td>
                            <td>{{ $video->tagsInput() ?: '-' }}</td>
                            <td>
                                <span class="status-pill {{ $video->is_published ? 'is-live' : 'is-draft' }}">
                                    {{ $video->is_published ? 'Publiée' : 'Brouillon' }}
                                </span>
                            </td>
                            <td class="table-actions">
                                <a href="{{ route('tech-videos.index') }}" class="text-link" target="_blank" rel="noopener">Voir</a>
                                <a href="{{ route('admin.tech-videos.edit', $video) }}" class="btn btn-secondary">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Aucune vidéo tech pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $videos->links() }}
        </div>
    </section>
@endsection
