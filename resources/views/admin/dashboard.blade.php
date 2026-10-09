@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header-title', '📊 Tableau de bord administrateur')
@section('header-subtitle', 'Vue d\'ensemble du système')

@section('admin-content')
<div class="fade-up">

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Total utilisateurs</div>
            <div class="stat-value">{{ number_format($total_users ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> +{{ $evolution_recente ?? 12 }}%</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-user-check"></i></div>
            <div class="stat-label">Utilisateurs actifs</div>
            <div class="stat-value">{{ number_format($users_actifs ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend up"><i class="fas fa-check-circle me-1"></i> Connectés</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
            <div class="stat-label">Utilisateurs inactifs</div>
            <div class="stat-value">{{ number_format($users_inactifs ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend down"><i class="fas fa-clock me-1"></i> Désactivés</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-history"></i></div>
            <div class="stat-label">Total logs</div>
            <div class="stat-value">{{ number_format($total_logs ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend"><i class="fas fa-calendar-day me-1"></i> Aujourd'hui: {{ $logs_aujourdhui ?? 0 }}</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-label">Total écritures</div>
            <div class="stat-value">{{ number_format($total_ecritures ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend"><i class="fas fa-chart-line me-1"></i> Comptables</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-table-list"></i></div>
            <div class="stat-label">Comptes plan</div>
            <div class="stat-value">{{ number_format($total_comptes ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend"><i class="fas fa-list me-1"></i> Classes 1-9</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-database"></i></div>
            <div class="stat-label">Taille BD</div>
            <div class="stat-value">{{ $taille_base ?? '0 MB' }}</div>
            <div class="stat-trend"><i class="fas fa-hdd me-1"></i> MySQL</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Activité aujourd'hui</div>
            <div class="stat-value">{{ $activite_aujourdhui ?? 0 }}</div>
            <div class="stat-trend"><i class="fas fa-clock me-1"></i> Opérations</div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="data-card">
                <div class="data-header">
                    <h3><i class="fas fa-chart-line me-2"></i>Évolution des utilisateurs</h3>
                    <span class="badge bg-light">12 derniers mois</span>
                </div>
                <div class="p-4"><canvas id="evolutionChart" height="300"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition par rôle</h3></div>
                <div class="p-4"><canvas id="roleChart" height="250"></canvas></div>
                <div class="source-legend" id="roleLegend"></div>
            </div>
        </div>
    </div>

    <!-- Dernières activités -->
    <div class="data-card">
        <div class="data-header">
            <div><h3><i class="fas fa-history me-2"></i>Dernières activités</h3></div>
            <a href="{{ url('/admin/logs') }}" class="btn-view-all">Voir tous <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>IP</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniers_logs ?? [] as $log)
                    <tr>
                        <td><strong>{{ $log->user?->name ?? 'Système' }}</strong></td>
                        <td><span class="badge-info">{{ $log->action }}</span></td>
                        <td>{{ $log->table ?? '-' }}</td>
                        <td>{{ $log->ip }}</td>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                            Aucune activité enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dernier bilan et résultat -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Dernier bilan</h3></div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box">
                                <label>Total Actif</label>
                                <h4 class="text-primary">{{ number_format($dernier_bilan->total_actif ?? 0, 0, ',', ' ') }} FC</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <label>Total Passif</label>
                                <h4 class="text-success">{{ number_format($dernier_bilan->total_passif ?? 0, 0, ',', ' ') }} FC</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Dernier résultat</h3></div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box">
                                <label>Total Produits</label>
                                <h4 class="text-success">{{ number_format($dernier_resultat->total_produits ?? 0, 0, ',', ' ') }} FC</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <label>Total Charges</label>
                                <h4 class="text-danger">{{ number_format($dernier_resultat->total_charges ?? 0, 0, ',', ' ') }} FC</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions">
        <div class="quick-header"><i class="fas fa-bolt me-2"></i> Actions rapides</div>
        <div class="quick-grid">
            <a href="{{ url('/admin/users/create') }}" class="quick-btn"><i class="fas fa-user-plus"></i> Ajouter utilisateur</a>
            <a href="{{ url('/admin/plan-comptable/create') }}" class="quick-btn"><i class="fas fa-plus-circle"></i> Ajouter compte</a>
            <!-- ========== NOUVEAU LIEN ÉCRITURES ========== -->
            <a href="{{ url('/admin/ecritures') }}" class="quick-btn"><i class="fas fa-book"></i> Voir écritures</a>
            <a href="{{ url('/admin/systeme/backups') }}" class="quick-btn"><i class="fas fa-database"></i> Backup</a>
            <a href="{{ url('/admin/systeme/clear-cache') }}" class="quick-btn"><i class="fas fa-trash-alt"></i> Vider cache</a>
            <a href="{{ url('/admin/logs') }}" class="quick-btn"><i class="fas fa-history"></i> Voir logs</a>
            <a href="{{ url('/admin/statistiques') }}" class="quick-btn"><i class="fas fa-chart-bar"></i> Statistiques</a>
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
                labels: {!! json_encode($evolution_mois ?? ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc']) !!},
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: {!! json_encode($evolution_users ?? []) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.05)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
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

    // Graphique répartition par rôle
    const roleCtx = document.getElementById('roleChart')?.getContext('2d');
    if (roleCtx) {
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($role_labels ?? ['Administrateur', 'Directeur', 'Comptable', 'Caissier', 'Vendeur']) !!},
                datasets: [{
                    data: {!! json_encode($role_data ?? [1, 0, 0, 0, 0]) !!},
                    backgroundColor: ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw} utilisateur(s)` } }
                },
                cutout: '60%'
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
    .info-box { background: #f8fafc; padding: 15px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0; }
    .info-box label { font-size: 11px; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 5px; }
    .info-box h4 { font-size: 18px; font-weight: 700; margin: 0; }
    .source-legend { display: flex; justify-content: center; gap: 15px; margin-top: 15px; flex-wrap: wrap; }
    .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .stat-trend { font-size: 11px; margin-top: 8px; display: flex; align-items: center; gap: 4px; }
    .stat-trend.up { color: #10b981; }
    .stat-trend.down { color: #ef4444; }
</style>
@endpush
@endsection