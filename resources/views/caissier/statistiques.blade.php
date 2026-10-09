@extends('layouts.caissier')

@section('title', 'Statistiques')
@section('header-title', '📊 Statistiques')
@section('header-subtitle', 'Analyse des opérations de caisse et banque')

@section('caissier-content')
<div class="fade-up">

    <!-- Cartes récapitulatives -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total entrées caisse</div>
                <div class="stat-value">{{ number_format($total_entrees ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total sorties caisse</div>
                <div class="stat-value">{{ number_format($total_sorties ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-university"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total dépôts banque</div>
                <div class="stat-value">{{ number_format($total_depots ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total retraits banque</div>
                <div class="stat-value">{{ number_format($total_retraits ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="data-card">
                <div class="card-header-custom">
                    <div class="card-header-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <h3 class="card-header-title">Évolution mensuelle</h3>
                        <p class="card-header-subtitle">12 derniers mois</p>
                    </div>
                </div>
                <div class="p-4">
                    <canvas id="evolutionChart" height="280"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="data-card">
                <div class="card-header-custom purple">
                    <div class="card-header-icon"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <h3 class="card-header-title">Top 10 opérations</h3>
                        <p class="card-header-subtitle">Les montants les plus élevés</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th class="text-end">Montant (FC)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_operations ?? [] as $op)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rank-badge">{{ $loop->iteration }}</span>
                                        {{ Str::limit($op->libelle, 35) }}
                                    </div>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($op->montant, 0, ',', ' ') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">Aucune donnée disponible</p>
                                    </div>
                                 </span>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques circulaires -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="card-header-custom green">
                    <div class="card-header-icon"><i class="fas fa-chart-pie"></i></div>
                    <div>
                        <h3 class="card-header-title">Répartition caisse</h3>
                        <p class="card-header-subtitle">Entrées vs Sorties</p>
                    </div>
                </div>
                <div class="p-4">
                    <canvas id="caisseChart" height="250"></canvas>
                    <div class="chart-legend mt-3">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #10b981;"></span>
                            Entrées ({{ number_format(($total_entrees ?? 0) / max(1, ($total_entrees ?? 0) + ($total_sorties ?? 0)) * 100, 1) }}%)
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #ef4444;"></span>
                            Sorties ({{ number_format(($total_sorties ?? 0) / max(1, ($total_entrees ?? 0) + ($total_sorties ?? 0)) * 100, 1) }}%)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="card-header-custom blue">
                    <div class="card-header-icon"><i class="fas fa-chart-pie"></i></div>
                    <div>
                        <h3 class="card-header-title">Répartition banque</h3>
                        <p class="card-header-subtitle">Dépôts vs Retraits</p>
                    </div>
                </div>
                <div class="p-4">
                    <canvas id="banqueChart" height="250"></canvas>
                    <div class="chart-legend mt-3">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #3b82f6;"></span>
                            Dépôts ({{ number_format(($total_depots ?? 0) / max(1, ($total_depots ?? 0) + ($total_retraits ?? 0)) * 100, 1) }}%)
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #f59e0b;"></span>
                            Retraits ({{ number_format(($total_retraits ?? 0) / max(1, ($total_depots ?? 0) + ($total_retraits ?? 0)) * 100, 1) }}%)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs supplémentaires -->
    <div class="indicators-card mt-4">
        <div class="indicators-header">
            <i class="fas fa-chart-simple"></i> Indicateurs clés
        </div>
        <div class="indicators-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="indicator-item">
                        <div class="indicator-icon"><i class="fas fa-chart-line text-primary"></i></div>
                        <div class="indicator-info">
                            <div class="indicator-label">Moyenne entrées</div>
                            <div class="indicator-value">{{ number_format(($total_entrees ?? 0) / max(1, count($evolution_entrees ?? [])), 0, ',', ' ') }} FC</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="indicator-item">
                        <div class="indicator-icon"><i class="fas fa-chart-line text-danger"></i></div>
                        <div class="indicator-info">
                            <div class="indicator-label">Moyenne sorties</div>
                            <div class="indicator-value">{{ number_format(($total_sorties ?? 0) / max(1, count($evolution_sorties ?? [])), 0, ',', ' ') }} FC</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="indicator-item">
                        <div class="indicator-icon"><i class="fas fa-calendar text-success"></i></div>
                        <div class="indicator-info">
                            <div class="indicator-label">Meilleur mois</div>
                            <div class="indicator-value">{{ $meilleur_mois ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="indicator-item">
                        <div class="indicator-icon"><i class="fas fa-chart-line text-warning"></i></div>
                        <div class="indicator-info">
                            <div class="indicator-label">Tendance</div>
                            <div class="indicator-value {{ ($tendance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($tendance ?? 0) >= 0 ? '+' : '' }}{{ $tendance ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s;
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-success .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-danger .stat-icon { background: #fee2e2; color: #ef4444; }
    .stat-primary .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-warning .stat-icon { background: #fef3c7; color: #f59e0b; }
    .stat-icon i { font-size: 20px; }
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
    }
    .stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
    }
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .card-header-custom {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .card-header-custom.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }
    .card-header-custom.green {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .card-header-custom.blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .card-header-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .card-header-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    .card-header-subtitle {
        font-size: 11px;
        margin: 2px 0 0;
        color: rgba(255,255,255,0.8);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead th {
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    .data-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: #e2e8f0;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
    }
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
        flex-wrap: wrap;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #64748b;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }
    .indicators-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .indicators-header {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        padding: 12px 20px;
        color: white;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .indicators-body {
        padding: 20px;
    }
    .indicator-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 12px;
    }
    .indicator-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .indicator-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
    }
    .indicator-value {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .empty-state {
        text-align: center;
        padding: 30px;
    }
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .indicators-body .row {
            flex-direction: column;
        }
        .indicator-item {
            margin-bottom: 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique évolution
    const evoCtx = document.getElementById('evolutionChart')?.getContext('2d');
    if (evoCtx) {
        new Chart(evoCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($evolution_mois ?? []) !!},
                datasets: [
                    {
                        label: 'Entrées caisse',
                        data: {!! json_encode($evolution_entrees ?? []) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    },
                    {
                        label: 'Sorties caisse',
                        data: {!! json_encode($evolution_sorties ?? []) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${new Intl.NumberFormat('fr-FR').format(ctx.raw)} FC` } }
                },
                scales: { y: { beginAtZero: true, ticks: { callback: (v) => new Intl.NumberFormat('fr-FR').format(v) + ' FC' } } }
            }
        });
    }

    // Graphique caisse
    const caisseCtx = document.getElementById('caisseChart')?.getContext('2d');
    if (caisseCtx) {
        new Chart(caisseCtx, {
            type: 'doughnut',
            data: {
                labels: ['Entrées', 'Sorties'],
                datasets: [{
                    data: [{{ $total_entrees ?? 0 }}, {{ $total_sorties ?? 0 }}],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${new Intl.NumberFormat('fr-FR').format(ctx.raw)} FC` } }
                },
                cutout: '60%'
            }
        });
    }

    // Graphique banque
    const banqueCtx = document.getElementById('banqueChart')?.getContext('2d');
    if (banqueCtx) {
        new Chart(banqueCtx, {
            type: 'doughnut',
            data: {
                labels: ['Dépôts', 'Retraits'],
                datasets: [{
                    data: [{{ $total_depots ?? 0 }}, {{ $total_retraits ?? 0 }}],
                    backgroundColor: ['#3b82f6', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${new Intl.NumberFormat('fr-FR').format(ctx.raw)} FC` } }
                },
                cutout: '60%'
            }
        });
    }
</script>
@endpush
@endsection