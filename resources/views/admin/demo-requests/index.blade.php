@extends('layouts.admin', [
    'title' => 'Demandes de démonstration',
    'pageHeading' => 'Demandes de démonstration',
    'moduleLabel' => 'Demandes de démonstration',
])

@section('content')
    <section class="admin-card">
        <div class="admin-section-header">
            <div class="section-heading compact">
                <p class="eyebrow">Prospects</p>
                <h2>Demandes reçues depuis le site public.</h2>
                <p>Suivez les nouveaux contacts, marquez les demandes comme traitées et conservez des notes internes.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success mt-6">{{ session('status') }}</div>
        @endif

        <div class="table-wrap mt-6">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Contact</th>
                        <th>Événement</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($demoRequests as $demoRequest)
                        <tr>
                            <td>{{ $demoRequest->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $demoRequest->full_name }}</strong>
                                <span>{{ $demoRequest->email }}</span>
                            </td>
                            <td>
                                <strong>{{ $demoRequest->event_type ?: 'Non renseigné' }}</strong>
                                <span>{{ $demoRequest->selected_solution ?: 'À préciser' }}</span>
                            </td>
                            <td>
                                <span class="status-pill status-{{ $demoRequest->status }}">
                                    {{ $demoRequest->statusLabel() }}
                                </span>
                            </td>
                            <td class="table-actions">
                                <a href="{{ route('admin.demo-requests.show', $demoRequest) }}" class="btn btn-secondary">Ouvrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Aucune demande pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $demoRequests->links() }}
        </div>
    </section>
@endsection
