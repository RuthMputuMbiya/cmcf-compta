@extends('layouts.caissier')

@section('title', 'Tableau de bord - Caissière')
@section('header-title', '💰 Tableau de bord - Caisse')
@section('header-subtitle', 'Vue d\'ensemble des opérations financières')

@section('caissier-content')
<div class="fade-up">
    
    <!-- Bannière de bienvenue -->
    <div class="welcome-stats mb-4">
        <div class="welcome-stats-content">
            <div>
                <h3><i class="fas fa-chart-line me-2"></i>Bienvenue, {{ Auth::user()->name ?? 'Caissière' }}</h3>
                <p class="text-muted mb-0">Voici le récapitulatif des opérations du jour</p>
            </div>
            <div class="welcome-stats-badge">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->translatedFormat('l d F Y') }}
            </div>
        </div>
    </div>

    <!-- Cartes statistiques - Caisse -->
    <div class="stats-grid">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total entrées caisse</div>
                <div class="stat-value">{{ number_format($total_entrees_caisse ?? 0, 0, ',', ' ') }} FC</div>
                <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Encaissements</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 65%"></div></div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total sorties caisse</div>
                <div class="stat-value">{{ number_format($total_sorties_caisse ?? 0, 0, ',', ' ') }} FC</div>
                <div class="stat-trend down"><i class="fas fa-arrow-down me-1"></i> Décaissements</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 35%"></div></div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-label">Solde caisse</div>
                <div class="stat-value {{ ($solde_caisse ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format(abs($solde_caisse ?? 0), 0, ',', ' ') }} FC
                </div>
                <div class="stat-trend">{{ ($solde_caisse ?? 0) >= 0 ? 'Créditeur' : 'Débiteur' }}</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 100%"></div></div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-calendar"></i></div>
            <div class="stat-info">
                <div class="stat-label">Opérations aujourd'hui</div>
                <div class="stat-value">{{ $nb_transactions_jour ?? 0 }}</div>
                <div class="stat-trend"><i class="fas fa-clock me-1"></i> Transactions</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 45%"></div></div>
        </div>
    </div>

    <!-- Deuxième ligne - Banque -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-university"></i></div>
            <div class="stat-info">
                <div class="stat-label">Solde banque</div>
                <div class="stat-value">{{ number_format($solde_banque ?? 0, 0, ',', ' ') }} FC</div>
                <div class="stat-trend"><i class="fas fa-building me-1"></i> Compte bancaire</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 85%"></div></div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <div class="stat-label">Dépôts aujourd'hui</div>
                <div class="stat-value">{{ number_format($depots_jour ?? 0, 0, ',', ' ') }} FC</div>
                <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Versements</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 55%"></div></div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Retraits aujourd'hui</div>
                <div class="stat-value">{{ number_format($retraits_jour ?? 0, 0, ',', ' ') }} FC</div>
                <div class="stat-trend down"><i class="fas fa-arrow-down me-1"></i> Prélèvements</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 25%"></div></div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-label">Trésorerie totale</div>
                <div class="stat-value">{{ number_format(($solde_caisse ?? 0) + ($solde_banque ?? 0), 0, ',', ' ') }} FC</div>
                <div class="stat-trend"><i class="fas fa-coins me-1"></i> Disponibilités</div>
            </div>
            <div class="stat-progress-bar"><div class="stat-progress-fill" style="width: 100%"></div></div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="data-card">
                <div class="card-header-custom">
                    <div class="card-header-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <h3 class="card-header-title">Évolution de la caisse</h3>
                        <p class="card-header-subtitle">6 derniers mois</p>
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
                    <div class="card-header-icon"><i class="fas fa-chart-pie"></i></div>
                    <div>
                        <h3 class="card-header-title">Répartition des mouvements</h3>
                        <p class="card-header-subtitle">Entrées vs Sorties</p>
                    </div>
                </div>
                <div class="p-4">
                    <canvas id="repartitionChart" height="250"></canvas>
                    <div class="chart-legend mt-3">
                        <div class="legend-item"><span class="legend-color" style="background: #10b981;"></span> Entrées ({{ number_format(($total_entrees_caisse ?? 0) / max(1, ($total_entrees_caisse ?? 0) + ($total_sorties_caisse ?? 0)) * 100, 1) }}%)</div>
                        <div class="legend-item"><span class="legend-color" style="background: #ef4444;"></span> Sorties ({{ number_format(($total_sorties_caisse ?? 0) / max(1, ($total_entrees_caisse ?? 0) + ($total_sorties_caisse ?? 0)) * 100, 1) }}%)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    @if(($solde_caisse ?? 0) < 100000 && ($solde_caisse ?? 0) > 0)
    <div class="alert alert-warning mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Attention :</strong> Le solde de caisse est faible (moins de 100 000 FC). Veuillez prévoir un réapprovisionnement.
    </div>
    @endif

    @if(($solde_caisse ?? 0) < 0)
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Découvert !</strong> Le solde de caisse est négatif. Veuillez régulariser la situation.
    </div>
    @endif

    <!-- Dernières opérations caisse -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="card-header-custom green">
                    <div class="card-header-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <h3 class="card-header-title">Dernières opérations caisse</h3>
                        <p class="card-header-subtitle">5 dernières transactions</p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ url('/caissier/caisse') }}" class="btn-view-all">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Libellé</th>
                                <th class="text-end">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dernieres_operations_caisse ?? [] as $op)
                            <tr>
                                <td data-label="Date">{{ $op->date->format('d/m/Y') }}</span>
                                <td data-label="Type">
                                    @if($op->type == 'entree')
                                        <span class="badge-success"><i class="fas fa-arrow-down me-1"></i>Entrée</span>
                                    @else
                                        <span class="badge-danger"><i class="fas fa-arrow-up me-1"></i>Sortie</span>
                                    @endif
                                </span>
                                <td data-label="Libellé">{{ Str::limit($op->libelle, 30) }}</span>
                                <td data-label="Montant" class="text-end {{ $op->type == 'entree' ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($op->montant, 0, ',', ' ') }} FC
                                </span>
                            </tr>
                            @empty
                            <tr class="text-center">
                                <td colspan="4" class="py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                    Aucune opération
                                </span>
                            </span>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="card-header-custom blue">
                    <div class="card-header-icon"><i class="fas fa-university"></i></div>
                    <div>
                        <h3 class="card-header-title">Dernières opérations banque</h3>
                        <p class="card-header-subtitle">5 dernières transactions</p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ url('/caissier/banque') }}" class="btn-view-all">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Libellé</th>
                                <th class="text-end">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dernieres_operations_banque ?? [] as $op)
                            <tr>
                                <td data-label="Date">{{ $op->date->format('d/m/Y') }}</span>
                                <td data-label="Type">
                                    @if($op->type == 'depot')
                                        <span class="badge-success"><i class="fas fa-arrow-down me-1"></i>Dépôt</span>
                                    @else
                                        <span class="badge-danger"><i class="fas fa-arrow-up me-1"></i>Retrait</span>
                                    @endif
                                </span>
                                <td data-label="Libellé">{{ Str::limit($op->libelle, 30) }}</span>
                                <td data-label="Montant" class="text-end {{ $op->type == 'depot' ? 'text-success' : 'text-danger' }}">
                                    {{ number_format(abs($op->montant), 0, ',', ' ') }} FC
                                </span>
                            </tr>
                            @empty
                            <tr class="text-center">
                                <td colspan="4" class="py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                    Aucune opération
                                </span>
                            </span>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions">
        <div class="quick-header"><i class="fas fa-bolt me-2"></i> Actions rapides</div>
        <div class="quick-grid">
            <a href="{{ url('/caissier/caisse/entree') }}" class="quick-btn"><i class="fas fa-plus-circle"></i> Encaissement</a>
            <a href="{{ url('/caissier/caisse/sortie') }}" class="quick-btn"><i class="fas fa-minus-circle"></i> Décaissement</a>
            <a href="{{ url('/caissier/banque/depot') }}" class="quick-btn"><i class="fas fa-university"></i> Dépôt banque</a>
            <a href="{{ url('/caissier/banque/retrait') }}" class="quick-btn"><i class="fas fa-money-bill-wave"></i> Retrait banque</a>
            <a href="{{ url('/caissier/rapports/journalier') }}" class="quick-btn"><i class="fas fa-file-alt"></i> Rapport journalier</a>
            <a href="{{ url('/caissier/rapports/mensuel') }}" class="quick-btn"><i class="fas fa-calendar-alt"></i> Rapport mensuel</a>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Bannière de bienvenue */
    .welcome-stats {
        background: linear-gradient(135deg, #064e3b, #065f46);
        border-radius: 20px;
        padding: 20px 25px;
        margin-bottom: 25px;
    }
    .welcome-stats-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .welcome-stats-content h3 {
        color: white;
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .welcome-stats-content p {
        color: #a7f3d0;
        margin: 5px 0 0;
    }
    .welcome-stats-badge {
        background: rgba(255,255,255,0.2);
        padding: 8px 18px;
        border-radius: 40px;
        color: white;
        font-size: 13px;
    }
    
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 18px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        display: flex;
        gap: 15px;
        align-items: center;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 55px;
        height: 55px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-success .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-danger .stat-icon { background: #fee2e2; color: #ef4444; }
    .stat-primary .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-info .stat-icon { background: #e0e7ff; color: #6366f1; }
    .stat-purple .stat-icon { background: #ede9fe; color: #8b5cf6; }
    .stat-icon i { font-size: 24px; }
    .stat-info { flex: 1; }
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
    }
    .stat-trend {
        font-size: 11px;
        margin-top: 5px;
    }
    .stat-trend.up { color: #10b981; }
    .stat-trend.down { color: #ef4444; }
    .stat-progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #e2e8f0;
    }
    .stat-progress-fill {
        height: 100%;
    }
    .stat-success .stat-progress-fill { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-danger .stat-progress-fill { background: linear-gradient(90deg, #ef4444, #f87171); }
    .stat-primary .stat-progress-fill { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-info .stat-progress-fill { background: linear-gradient(90deg, #6366f1, #818cf8); }
    .stat-purple .stat-progress-fill { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    
    /* Card Header */
    .card-header-custom {
        background: linear-gradient(135deg, #10b981, #059669);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
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
    
    /* Data Card */
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .btn-view-all {
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
        color: white;
        text-decoration: none;
        font-size: 11px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-view-all:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }
    
    /* Tableau */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead th {
        background: #f8fafc;
        padding: 10px 15px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    .data-table tbody td {
        padding: 10px 15px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    /* Badges */
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Chart Legend */
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
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
    
    /* Quick Actions */
    .quick-actions {
        background: linear-gradient(135deg, #064e3b, #065f46);
        border-radius: 20px;
        padding: 20px 25px;
    }
    .quick-header {
        color: white;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .quick-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .quick-btn {
        background: rgba(255,255,255,0.15);
        padding: 8px 18px;
        border-radius: 30px;
        color: white;
        text-decoration: none;
        font-size: 12px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .quick-btn:hover {
        background: #10b981;
        transform: translateY(-2px);
    }
    
    /* Alerts */
    .alert-warning {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        color: #92400e;
        border-radius: 12px;
        padding: 15px;
    }
    .alert-danger {
        background: #fee2e2;
        border-left: 4px solid #ef4444;
        color: #991b1b;
        border-radius: 12px;
        padding: 15px;
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
        .welcome-stats-content {
            flex-direction: column;
            text-align: center;
        }
        .data-table thead {
            display: none;
        }
        .data-table tbody tr {
            display: block;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        .data-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        .data-table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 11px;
            color: #64748b;
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
                labels: {!! json_encode($evolution_mois ?? ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun']) !!},
                datasets: [
                    {
                        label: 'Entrées',
                        data: {!! json_encode($evolution_entrees ?? [0,0,0,0,0,0]) !!},
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
                        label: 'Sorties',
                        data: {!! json_encode($evolution_sorties ?? [0,0,0,0,0,0]) !!},
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
    
    // Graphique répartition
    const repCtx = document.getElementById('repartitionChart')?.getContext('2d');
    if (repCtx) {
        new Chart(repCtx, {
            type: 'doughnut',
            data: {
                labels: ['Entrées caisse', 'Sorties caisse'],
                datasets: [{
                    data: [{{ $total_entrees_caisse ?? 0 }}, {{ $total_sorties_caisse ?? 0 }}],
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
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${new Intl.NumberFormat('fr-FR').format(ctx.raw)} FC (${((ctx.raw / ({{ $total_entrees_caisse ?? 0 }} + {{ $total_sorties_caisse ?? 0 }})) * 100).toFixed(1)}%)` } }
                },
                cutout: '60%'
            }
        });
    }
</script>
@endpush
@endsection