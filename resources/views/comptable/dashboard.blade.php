@extends('layouts.comptable')

@section('title', 'Dashboard')
@section('header-title', '📊 Tableau de bord comptable')
@section('header-subtitle', 'Vue d\'ensemble des opérations financières')

@section('comptable-content')
<div class="fade-up">

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-label">Total écritures</div>
            <div class="stat-value">{{ number_format($total_ecritures ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Clients</div>
            <div class="stat-value">{{ number_format($total_clients ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-truck"></i></div>
            <div class="stat-label">Fournisseurs</div>
            <div class="stat-value">{{ number_format($total_fournisseurs ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Résultat net</div>
            <div class="stat-value">{{ number_format($resultat_net ?? 0, 0, ',', ' ') }} FC</div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Évolution mensuelle</h3></div>
                <div class="p-4"><canvas id="evolutionChart" height="300"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition</h3></div>
                <div class="p-4"><canvas id="repartitionChart" height="250"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Dernières écritures -->
    <div class="data-card">
        <div class="data-header">
            <div><h3><i class="fas fa-list-alt me-2"></i>Dernières écritures</h3></div>
            <a href="{{ url('/comptable/ecritures') }}" class="btn-view-all">Voir toutes <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Pièce</th>
                        <th>Date</th>
                        <th>Libellé</th>
                        <th>Débit</th>
                        <th>Crédit</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dernieres_ecritures ?? [] as $e)
                    <tr>
                        <td><strong class="text-primary">{{ $e->numero_piece }}</strong></td>
                        <td>{{ $e->date->format('d/m/Y') }}</span>
                        <td>{{ Str::limit($e->libelle, 40) }}</span>
                        <td class="text-success">{{ number_format($e->montant_debit, 0, ',', ' ') }} FC</span>
                        <td class="text-danger">{{ number_format($e->montant_credit, 0, ',', ' ') }} FC</span>
                        <td>
                            @if($e->statut == 'valide')
                                <span class="badge-success">Validé</span>
                            @else
                                <span class="badge-warning">Brouillon</span>
                            @endif
                        </span>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <td colspan="6" class="py-4">Aucune écriture</span>
                    </span>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions">
        <div class="quick-header"><i class="fas fa-bolt me-2"></i> Actions rapides</div>
        <div class="quick-grid">
            <a href="{{ url('/comptable/ecritures/create') }}" class="quick-btn"><i class="fas fa-plus-circle"></i> Nouvelle écriture</a>
            <a href="{{ url('/comptable/journal') }}" class="quick-btn"><i class="fas fa-book"></i> Journal</a>
            <a href="{{ url('/comptable/balance') }}" class="quick-btn"><i class="fas fa-balance-scale"></i> Balance</a>
            <a href="{{ url('/comptable/bilan') }}" class="quick-btn"><i class="fas fa-chart-pie"></i> Bilan</a>
            <a href="{{ url('/comptable/resultat') }}" class="quick-btn"><i class="fas fa-chart-line"></i> Résultat</a>
            <a href="{{ url('/comptable/clients') }}" class="quick-btn"><i class="fas fa-users"></i> Clients</a>
            <a href="{{ url('/comptable/fournisseurs') }}" class="quick-btn"><i class="fas fa-truck"></i> Fournisseurs</a>
            <!-- NOUVEAU LIEN PLAN COMPTABLE -->
            <a href="{{ url('/comptable/plan-comptable') }}" class="quick-btn"><i class="fas fa-book-open"></i> Plan comptable</a>
        </div>
    </div>
</div>

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
                datasets: [{
                    label: 'Écritures',
                    data: {!! json_encode($evolution_counts ?? [12, 19, 15, 17, 14, 23]) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.05)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}` } }
                },
                scales: { y: { beginAtZero: true, grid: { color: '#e2e8f0' } } }
            }
        });
    }

    // Graphique répartition
    const repCtx = document.getElementById('repartitionChart')?.getContext('2d');
    if (repCtx) {
        new Chart(repCtx, {
            type: 'doughnut',
            data: {
                labels: ['Clients', 'Fournisseurs', 'Banque', 'Caisse'],
                datasets: [{
                    data: [40, 25, 20, 15],
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } },
                cutout: '60%'
            }
        });
    }
</script>
@endpush

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
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .stat-primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
    .stat-success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .stat-info .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
    
    .stat-icon i { font-size: 22px; }
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .stat-value {
        font-size: 26px;
        font-weight: 800;
        color: #1e293b;
    }
    
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 25px;
    }
    
    .data-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .data-header h3 {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }
    
    .btn-view-all {
        background: #f1f5f9;
        padding: 6px 15px;
        border-radius: 30px;
        color: #3b82f6;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-view-all:hover {
        background: #3b82f6;
        color: white;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table thead th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .data-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .text-primary {
        color: #3b82f6 !important;
    }
    
    .text-success {
        color: #10b981 !important;
    }
    
    .text-danger {
        color: #ef4444 !important;
    }
    
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-warning {
        background: #fef3c7;
        color: #d97706;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .quick-actions {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 20px;
        padding: 25px;
    }
    
    .quick-header {
        color: white;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .quick-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .quick-btn {
        background: rgba(255,255,255,0.1);
        padding: 10px 22px;
        border-radius: 40px;
        color: white;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .quick-btn:hover {
        background: #3b82f6;
        transform: translateY(-2px);
        color: white;
    }
    
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .col-md-8 {
        flex: 2;
        min-width: 250px;
    }
    
    .col-md-4 {
        flex: 1;
        min-width: 250px;
    }
    
    .p-4 {
        padding: 20px;
    }
    
    .py-4 {
        padding-top: 20px;
        padding-bottom: 20px;
    }
    
    .text-center {
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .data-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .row {
            flex-direction: column;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .quick-grid {
            flex-direction: column;
        }
        .quick-btn {
            justify-content: center;
        }
    }
</style>
@endpush
@endsection