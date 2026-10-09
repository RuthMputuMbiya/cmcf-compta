@extends('layouts.vendeur')

@section('title', 'Tableau de bord')
@section('header-title', '🏪 Tableau de bord - Ventes')
@section('header-subtitle', 'Vue d\'ensemble des performances commerciales')

@section('vendeur-content')
<div class="fade-up">

    <!-- Cartes statistiques -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Ventes du jour</div>
            <div class="stat-value">{{ number_format($ventes_jour ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Aujourd'hui</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
            <div class="stat-label">Ventes de la semaine</div>
            <div class="stat-value">{{ number_format($ventes_semaine ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-calendar-month"></i></div>
            <div class="stat-label">Ventes du mois</div>
            <div class="stat-value">{{ number_format($ventes_mois ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="stat-label">Factures</div>
            <div class="stat-value">{{ number_format($nb_factures_jour ?? 0, 0, ',', ' ') }}</div>
        </div>
    </div>

    <!-- Deuxième ligne -->
    <div class="stats-grid">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-label">Factures payées</div>
            <div class="stat-value">{{ number_format($factures_payees ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-label">Factures impayées</div>
            <div class="stat-value">{{ number_format($factures_impayees ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-percent"></i></div>
            <div class="stat-label">Taux d'encaissement</div>
            <div class="stat-value">{{ $taux_encaissement ?? 0 }}<span class="small">%</span></div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Clients</div>
            <div class="stat-value">{{ number_format($total_clients ?? 0, 0, ',', ' ') }}</div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="data-card">
                <div class="data-header">
                    <h3><i class="fas fa-chart-line me-2"></i>Évolution des ventes</h3>
                    <span class="badge bg-light">12 derniers mois</span>
                </div>
                <div class="p-4"><canvas id="evolutionChart" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition des ventes</h3></div>
                <div class="p-4"><canvas id="repartitionChart" height="250"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Top clients -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header">
                    <div><h3><i class="fas fa-trophy me-2"></i>Top 5 clients</h3></div>
                    <a href="{{ route('vendeur.clients') }}" class="btn-view-all">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>Client</th><th>Montant total</th><th>Factures</th></tr></thead>
                        <tbody>
                            @forelse($top_clients ?? [] as $client)
                            <tr>
                                <td>{{ $client->nom }}</span>
                                <td class="text-success">{{ number_format($client->total, 0, ',', ' ') }} FC</span>
                                <td>{{ $client->factures_count }}</span>
                            </tr>
                            @empty<td colspan="3" class="text-center py-3">Aucune donnée</span>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header">
                    <div><h3><i class="fas fa-file-invoice me-2"></i>Dernières factures</h3></div>
                    <a href="{{ route('vendeur.factures') }}" class="btn-view-all">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>N° Facture</th><th>Client</th><th>Montant</th><th>Statut</th></tr></thead>
                        <tbody>
                            @forelse($dernieres_factures ?? [] as $facture)
                            <tr>
                                <td><strong class="text-primary">{{ $facture->numero }}</strong></span>
                                <td>{{ $facture->client->nom }}</span>
                                <td class="text-success">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FC</span>
                                <td>@if($facture->statut == 'payee')<span class="badge-success">Payée</span>@else<span class="badge-danger">Impayée</span>@endif</span>
                            </tr>
                            @empty<td colspan="4" class="text-center py-3">Aucune facture</span>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Objectif mensuel -->
    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-bullseye me-2"></i>Objectif mensuel</h3></div>
        <div class="p-4">
            <div class="d-flex justify-content-between mb-2">
                <span>Progression : {{ $objectif_pourcentage ?? 0 }}%</span>
                <span>{{ number_format($objectif_realise ?? 0, 0, ',', ' ') }} FC / {{ number_format($objectif_total ?? 0, 0, ',', ' ') }} FC</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar bg-primary-custom" style="width: {{ $objectif_pourcentage ?? 0 }}%;"></div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions">
        <div class="quick-header"><i class="fas fa-bolt me-2"></i> Actions rapides</div>
        <div class="quick-grid">
            <a href="{{ route('vendeur.factures.create') }}" class="quick-btn"><i class="fas fa-plus-circle"></i> Nouvelle facture</a>
            <a href="{{ route('vendeur.encaissements') }}" class="quick-btn"><i class="fas fa-hand-holding-usd"></i> Encaissement</a>
            <a href="{{ route('vendeur.clients.create') }}" class="quick-btn"><i class="fas fa-user-plus"></i> Nouveau client</a>
            <a href="{{ route('vendeur.ventes') }}" class="quick-btn"><i class="fas fa-chart-line"></i> Rapport ventes</a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($evolution_mois ?? []) !!},
            datasets: [{ label: 'Ventes (FC)', data: {!! json_encode($evolution_ventes ?? []) !!}, borderColor: '#f59e0b', tension: 0.4, fill: true, backgroundColor: 'rgba(245,158,11,0.05)' }]
        },
        options: { responsive: true, maintainAspectRatio: true, scales: { y: { ticks: { callback: (v) => new Intl.NumberFormat('fr-FR').format(v) + ' FC' } } } }
    });
    new Chart(document.getElementById('repartitionChart'), {
        type: 'doughnut',
        data: { labels: ['Payées', 'Impayées'], datasets: [{ data: [{{ $factures_payees ?? 0 }}, {{ $factures_impayees ?? 0 }}], backgroundColor: ['#10b981', '#ef4444'], borderWidth: 0 }] },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
@endsection