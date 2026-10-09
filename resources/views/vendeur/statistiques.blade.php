@extends('layouts.vendeur')

@section('title', 'Statistiques commerciales')
@section('header-title', '📊 Statistiques commerciales')
@section('header-subtitle', 'Analyse des performances de vente')

@section('vendeur-content')
<div class="fade-up">

    <!-- Sélecteur de période -->
    <div class="data-card mb-4">
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Période</label>
                    <select id="periode" class="form-control-custom">
                        <option value="mois">Ce mois</option>
                        <option value="trimestre">Ce trimestre</option>
                        <option value="semestre">Ce semestre</option>
                        <option value="annee">Cette année</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Année</label>
                    <select id="annee" class="form-control-custom">
                        @for($i = now()->year; $i >= now()->year-2; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button onclick="changerPeriode()" class="btn-primary-custom w-100"><i class="fas fa-sync-alt me-2"></i>Actualiser</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes KPI -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Chiffre d'affaires</div>
            <div class="stat-value">{{ number_format($ca_periode ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend {{ ($evolution_ca ?? 0) >= 0 ? 'up' : 'down' }}">{{ ($evolution_ca ?? 0) >= 0 ? '+' : '' }}{{ $evolution_ca ?? 0 }}% vs période précédente</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="stat-label">Nombre de factures</div>
            <div class="stat-value">{{ number_format($nb_factures_periode ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-chart-simple"></i></div>
            <div class="stat-label">Panier moyen</div>
            <div class="stat-value">{{ number_format($panier_moyen ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-percent"></i></div>
            <div class="stat-label">Taux transformation</div>
            <div class="stat-value">{{ $taux_transformation ?? 0 }}<span class="small">%</span></div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Évolution des ventes</h3></div>
                <div class="p-4"><canvas id="evolutionChart" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition par mode de paiement</h3></div>
                <div class="p-4"><canvas id="paiementChart" height="250"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Top produits / services -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-trophy me-2"></i>Top 5 clients</h3></div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>Client</th><th>Montant (FC)</th><th>% du CA</th></tr></thead>
                        <tbody>
                            @foreach($top_clients ?? [] as $client)
                            <tr>
                                <td>{{ $client->nom }}</span>
                                <td class="text-success">{{ number_format($client->total, 0, ',', ' ') }}</span>
                                <td>{{ $client->pourcentage }}%</span>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-bar me-2"></i>Performance par jour</h3></div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><td><th>Jour</th><th>Ventes (FC)</th><th>Factures</th></tr></thead>
                        <tbody>
                            @foreach($performance_journaliere ?? [] as $jour)
                            <tr>
                                <td>{{ $jour['jour'] }}</span>
                                <td class="text-success">{{ number_format($jour['ventes'], 0, ',', ' ') }}</span>
                                <td>{{ $jour['nb_factures'] }}</span>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Objectif et prévisions -->
    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-bullseye me-2"></i>Objectif mensuel</h3></div>
        <div class="p-4">
            <div class="d-flex justify-content-between mb-2">
                <span>Progression : {{ $objectif_pourcentage ?? 0 }}%</span>
                <span>{{ number_format($objectif_realise ?? 0, 0, ',', ' ') }} FC / {{ number_format($objectif_total ?? 0, 0, ',', ' ') }} FC</span>
            </div>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar bg-primary-custom" style="width: {{ $objectif_pourcentage ?? 0 }}%;"></div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="info-box">
                        <label>Prévision fin de mois</label>
                        <h4 class="text-primary">{{ number_format($prevision_fin_mois ?? 0, 0, ',', ' ') }} FC</h4>
                        <small>+{{ $prevision_croissance ?? 0 }}% vs mois dernier</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <label>Meilleur jour du mois</label>
                        <h4 class="text-success">{{ $meilleur_jour ?? '-' }}</h4>
                        <small>{{ number_format($meilleur_jour_montant ?? 0, 0, ',', ' ') }} FC</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function changerPeriode() {
        let periode = document.getElementById('periode').value;
        let annee = document.getElementById('annee').value;
        window.location.href = '{{ route("vendeur.statistiques") }}?periode=' + periode + '&annee=' + annee;
    }

    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($evolution_labels ?? []) !!},
            datasets: [{
                label: 'Ventes (FC)',
                data: {!! json_encode($evolution_data ?? []) !!},
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.05)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: true, scales: { y: { ticks: { callback: (v) => new Intl.NumberFormat('fr-FR').format(v) + ' FC' } } } }
    });

    new Chart(document.getElementById('paiementChart'), {
        type: 'doughnut',
        data: {
            labels: ['Espèces', 'Virement bancaire', 'Chèque'],
            datasets: [{ data: {!! json_encode($paiement_data ?? [0,0,0]) !!}, backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'], borderWidth: 0 }]
        },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush

@push('styles')
<style>
    .progress-bar { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0; }
    .info-box label { font-size: 11px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 5px; }
    .info-box h4 { font-size: 20px; font-weight: 700; margin: 0; }
</style>
@endpush
@endsection