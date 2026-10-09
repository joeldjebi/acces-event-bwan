@extends('layouts.admin', [
    'title' => 'Tableau de bord',
    'pageHeading' => 'Tableau de bord ACCÈS EVENT',
    'moduleLabel' => 'Pilotage',
])

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-hero admin-card">
            <div>
                <p class="eyebrow">Pilotage</p>
                <h2>Vue d'ensemble de la plateforme.</h2>
                <p>Suivez les contenus publics, les demandes entrantes et les modules opérationnels depuis un seul écran.</p>
            </div>
            <a href="{{ route('admin.demo-requests.index') }}" class="btn btn-primary">Traiter les demandes</a>
        </section>

        <section class="dashboard-kpi-grid">
            <article>
                <span>Demandes</span>
                <strong>{{ $stats['demo_requests'] }}</strong>
                <small>{{ $stats['new_requests'] }} nouvelles</small>
            </article>
            <article>
                <span>Solutions</span>
                <strong>{{ $stats['solutions'] }}</strong>
                <small>{{ $stats['published_solutions'] }} publiées</small>
            </article>
            <article>
                <span>Applications</span>
                <strong>{{ $stats['applications'] }}</strong>
                <small>Écosystème public</small>
            </article>
            <article>
                <span>Médias</span>
                <strong>{{ $stats['media'] }}</strong>
                <small>Images disponibles</small>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="admin-card dashboard-chart-card">
                <div class="dashboard-card-header">
                    <div>
                        <p class="eyebrow">Demandes de démonstration</p>
                        <h2>Activité des 7 derniers jours</h2>
                    </div>
                    <span>{{ $stats['handled_requests'] }} traitées</span>
                </div>

                <div class="dashboard-bars" aria-label="Graphique des demandes de démonstration">
                    @foreach ($requestChart as $day)
                        <div class="dashboard-bar-item">
                            <div class="dashboard-bar-track">
                                <span style="height: {{ max(8, ($day['value'] / $maxChartValue) * 100) }}%"></span>
                            </div>
                            <strong>{{ $day['value'] }}</strong>
                            <small>{{ $day['label'] }}</small>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="admin-card dashboard-donut-card">
                <div class="dashboard-card-header">
                    <div>
                        <p class="eyebrow">Contenus</p>
                        <h2>Répartition du back office</h2>
                    </div>
                </div>

                <div class="dashboard-donut-wrap">
                    @php
                        $contentTotal = max(1, $stats['solutions'] + $stats['applications'] + $stats['pages'] + $stats['media']);
                    @endphp
                    <div class="dashboard-donut" style="--solutions: {{ ($stats['solutions'] / $contentTotal) * 100 }}%; --apps: {{ ($stats['applications'] / $contentTotal) * 100 }}%; --pages: {{ ($stats['pages'] / $contentTotal) * 100 }}%;">
                        <span>{{ $contentTotal }}</span>
                    </div>
                    <div class="dashboard-legend">
                        <span><i class="legend-solutions"></i> Solutions</span>
                        <span><i class="legend-apps"></i> Applications</span>
                        <span><i class="legend-pages"></i> Pages</span>
                        <span><i class="legend-media"></i> Médias</span>
                    </div>
                </div>
            </article>
        </section>

        <section class="admin-card dashboard-requests">
            <div class="dashboard-card-header">
                <div>
                    <p class="eyebrow">Suivi commercial</p>
                    <h2>Dernières demandes reçues</h2>
                </div>
                <a href="{{ route('admin.demo-requests.index') }}" class="btn btn-secondary">Voir tout</a>
            </div>

            <div class="dashboard-request-list">
                @forelse ($recentRequests as $request)
                    <a href="{{ route('admin.demo-requests.show', $request) }}">
                        <span>{{ $request->statusLabel() }}</span>
                        <strong>{{ $request->full_name }}</strong>
                        <small>{{ $request->company ?: $request->email }}</small>
                    </a>
                @empty
                    <div class="dashboard-empty">
                        <strong>Aucune demande pour le moment.</strong>
                        <span>Les prochaines demandes de démonstration apparaîtront ici.</span>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
