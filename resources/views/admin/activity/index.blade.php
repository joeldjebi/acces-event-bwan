@extends('layouts.admin', [
    'title' => "Journal d'activité",
    'pageHeading' => "Journal d'activité",
    'moduleLabel' => 'Paramètres',
])

@php
    $kinds = ['create' => 'Création', 'update' => 'Modification', 'delete' => 'Suppression', 'auth' => 'Connexion'];
@endphp

@section('content')
    <section class="admin-card">
        <div class="admin-section-header">
            <div class="section-heading compact">
                <p class="eyebrow">Réservé au propriétaire</p>
                <h2>Qui a fait quoi, et quand.</h2>
                <p>Chaque modification faite dans l'administration est enregistrée, ainsi que les connexions et les tentatives échouées.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.activity.index') }}" class="activity-filters mt-6">
            <div class="form-field">
                <label for="user">Administrateur</label>
                <select id="user" name="user" class="form-input">
                    <option value="">Tous</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(($filters['user'] ?? null) == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="kind">Type d'action</label>
                <select id="kind" name="kind" class="form-input">
                    <option value="">Toutes</option>
                    @foreach ($kinds as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['kind'] ?? null) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label for="q">Recherche</label>
                <input id="q" name="q" class="form-input" value="{{ $filters['q'] ?? '' }}" placeholder="Domaine, e-mail, adresse IP…">
            </div>
            <div class="activity-filters-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                @if (array_filter($filters))
                    <a href="{{ route('admin.activity.index') }}" class="text-link">Réinitialiser</a>
                @endif
            </div>
        </form>

        <div class="table-wrap mt-6">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Auteur</th>
                        <th>Action</th>
                        <th>Adresse IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <strong>{{ $log->created_at->timezone('Europe/Paris')->format('d/m/Y') }}</strong>
                                <span>{{ $log->created_at->timezone('Europe/Paris')->format('H:i:s') }}</span>
                            </td>
                            <td>
                                @if ($log->user_name)
                                    <strong>{{ $log->user_name }}</strong>
                                    <span>{{ $log->user_email }}</span>
                                @else
                                    <span>—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-pill kind-{{ $log->kind() }}">{{ $kinds[$log->kind()] }}</span>
                                <span class="activity-description">{{ $log->description }}</span>
                            </td>
                            <td><span title="{{ $log->user_agent }}">{{ $log->ip_address }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Aucune activité enregistrée{{ array_filter($filters) ? ' pour ces filtres' : '' }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </section>
@endsection
