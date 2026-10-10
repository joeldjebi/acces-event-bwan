@extends('layouts.admin', [
    'title' => 'Administrateurs',
    'pageHeading' => 'Administrateurs',
    'moduleLabel' => 'Paramètres',
])

@section('content')
    <section class="admin-card">
        <div class="admin-section-header">
            <div class="section-heading compact">
                <p class="eyebrow">Accès</p>
                <h2>Les personnes qui gèrent le site.</h2>
                <p>Le compte propriétaire ne peut pas être supprimé, et lui seul peut le modifier.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Nouvel administrateur</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif
        @error('user')
            <div class="alert alert-error mt-6">{{ $message }}</div>
        @enderror

        <div class="table-wrap mt-6">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Administrateur</th>
                        <th>Rôle</th>
                        <th>Créé le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }} @if ($user->is(auth()->user())) <small>(vous)</small> @endif</strong>
                                <span>{{ $user->email }}</span>
                            </td>
                            <td>
                                @if ($user->isOwner())
                                    <span class="status-pill kind-auth">Propriétaire</span>
                                @else
                                    <span class="status-pill is-draft">Administrateur</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                            <td class="table-actions">
                                @if (! $user->isOwner() || $user->is(auth()->user()))
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary">Modifier</a>
                                @endif
                                @unless ($user->isOwner() || $user->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer l\'administrateur {{ addslashes($user->name) }} ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-link">Supprimer</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </section>
@endsection
