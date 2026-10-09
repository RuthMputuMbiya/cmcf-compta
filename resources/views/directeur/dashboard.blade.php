@extends('layouts.directeur')

@section('title', 'Tableau de bord')
@section('header-title', '📊 Tableau de bord - Direction Générale')
@section('header-subtitle', 'Vue d\'ensemble des performances de l\'entreprise')

@section('directeur-content')
<div class="fade-up">

    <!-- Cartes KPI -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Chiffre d'affaires</div>
            <div class="stat-value">{{ number_format($chiffre_affaires ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> +{{ $croissance_ca ?? 12.5 }}%</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="stat-label">Bénéfice net</div>
            <div class="stat-value">{{ number_format($benefice_net ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> +{{ $croissance_benefice ?? 8.3 }}%</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-university"></i></div>
            <div class="stat-label">Trésorerie</div>
            <div class="stat-value">{{ number_format($tresorerie ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend"><i class="fas fa-chart-line me-1"></i> Disponibilités</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-percent"></i></div>
            <div class="stat-label">Rentabilité</div>
            <div class="stat-value">{{ $rentabilite ?? 0 }}<span class="small">%</span></div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> En hausse</div>
        </div>
    </div>

    <!-- Deuxième ligne de stats -->
    <div class="stats-grid">
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="stat-label">Total écritures</div>
            <div class="stat-value">{{ number_format($total_ecritures ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Total charges</div>
            <div class="stat-value">{{ number_format($total_charges ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Clients actifs</div>
            <div class="stat-value">{{ number_format($clients_actifs ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-truck"></i></div>
            <div class="stat-label">Fournisseurs</div>
            <div class="stat-value">{{ number_format($fournisseurs ?? 0, 0, ',', ' ') }}</div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="data-card">
                <div class="data-header">
                    <h3><i class="fas fa-chart-line me-2"></i>Évolution du chiffre d'affaires</h3>
                    <span class="badge bg-light">12 derniers mois</span>
                </div>
                <div class="p-4"><canvas id="caChart" height="300"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition charges/produits</h3></div>
                <div class="p-4"><canvas id="repartitionChart" height="250"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Ratios financiers -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="data-card text-center p-3">
                <div class="stat-icon stat-primary mx-auto mb-2"><i class="fas fa-chart-simple"></i></div>
                <h5>Marge brute</h5>
                <h3 class="text-primary">{{ $marge_brute ?? 0 }}%</h3>
                <small class="text-muted">+2.5% vs période précédente</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="data-card text-center p-3">
                <div class="stat-icon stat-success mx-auto mb-2"><i class="fas fa-coins"></i></div>
                <h5>BFR</h5>
                <h3 class="text-success">{{ number_format($bfr ?? 0, 0, ',', ' ') }} FC</h3>
                <small class="text-muted">Besoin en fonds de roulement</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="data-card text-center p-3">
                <div class="stat-icon stat-warning mx-auto mb-2"><i class="fas fa-clock"></i></div>
                <h5>Délai paiement client</h5>
                <h3 class="text-warning">{{ $delai_paiement ?? 45 }} jours</h3>
                <small class="text-muted">Objectif: 30 jours</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="data-card text-center p-3">
                <div class="stat-icon stat-purple mx-auto mb-2"><i class="fas fa-chart-line"></i></div>
                <h5>ROE</h5>
                <h3 class="text-purple">{{ $roe ?? 18.5 }}%</h3>
                <small class="text-muted">Rentabilité financière</small>
            </div>
        </div>
    </div>

    <!-- Dernières opérations -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header">
                    <h3><i class="fas fa-list-alt me-2"></i>Dernières écritures</h3>
                    <a href="{{ route('directeur.journal') }}" class="btn-view-all">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>Date</th><th>Libellé</th><th>Montant</th></tr></thead>
                        <tbody>
                            @forelse($dernieres_ecritures ?? [] as $e)
                            <tr><td>{{ $e->date->format('d/m/Y') }}</td><td>{{ Str::limit($e->libelle, 30) }}</td><td class="text-success">{{ number_format($e->montant_debit, 0, ',', ' ') }} FC</td></tr>
                            @empty<tr><td colspan="3" class="text-center py-3">Aucune écriture</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header">
                    <h3><i class="fas fa-chart-line me-2"></i>Top 5 clients</h3>
                    <a href="#" class="btn-view-all">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>Client</th><th>Chiffre d'affaires</th><th>%</th></tr></thead>
                        <tbody>
                            @forelse($top_clients ?? [] as $client)
                            <tr><td>{{ $client->nom }}</td><td class="text-success">{{ number_format($client->total, 0, ',', ' ') }} FC</td><td>{{ $client->pourcentage }}%</td></tr>
                            @empty<tr><td colspan="3" class="text-center py-3">Aucune donnée</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions mt-4">
        <div class="quick-header"><i class="fas fa-bolt me-2"></i> Actions rapides</div>
        <div class="quick-grid">
            <a href="{{ route('directeur.journal') }}" class="quick-btn"><i class="fas fa-book"></i> Journal</a>
            <a href="{{ route('directeur.bilan') }}" class="quick-btn"><i class="fas fa-chart-pie"></i> Bilan</a>
            <a href="{{ route('directeur.resultat') }}" class="quick-btn"><i class="fas fa-chart-line"></i> Résultat</a>
            <a href="{{ route('directeur.banque') }}" class="quick-btn"><i class="fas fa-university"></i> Banque</a>
            <a href="{{ route('directeur.caisse') }}" class="quick-btn"><i class="fas fa-money-bill-wave"></i> Caisse</a>
            <a href="{{ route('directeur.statistiques') }}" class="quick-btn"><i class="fas fa-chart-bar"></i> Statistiques</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    new Chart(document.getElementById('caChart'), {
        type: 'line',
        data: { labels: {!! json_encode($ca_mois ?? []) !!}, datasets: [{ label: 'CA (FC)', data: {!! json_encode($ca_data ?? []) !!}, borderColor: '#3b82f6', tension: 0.4, fill: true }] },
        options: { responsive: true, maintainAspectRatio: true }
    });
    new Chart(document.getElementById('repartitionChart'), {
        type: 'doughnut',
        data: { labels: ['Charges', 'Produits'], datasets: [{ data: {!! json_encode([$total_charges ?? 0, $chiffre_affaires ?? 0]) !!}, backgroundColor: ['#ef4444', '#10b981'], borderWidth: 0 }] },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
@endsection