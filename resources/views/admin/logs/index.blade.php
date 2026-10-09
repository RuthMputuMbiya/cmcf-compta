@extends('layouts.admin')

@section('title', 'Logs système')
@section('header-title', '📋 Logs système')
@section('header-subtitle', 'Historique des actions utilisateurs')

@section('admin-content')
<div class="fade-up">

    <!-- Filtres -->
    <div class="data-card mb-4">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-filter"></i>
            </div>
            <div>
                <h3 class="card-header-title">Filtres de recherche</h3>
                <p class="card-header-subtitle">Affinez votre recherche des logs</p>
            </div>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-user me-1"></i>Utilisateur</label>
                    <select id="user_id" class="form-control-custom">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i>Action</label>
                    <input type="text" id="action" class="form-control-custom" value="{{ request('action') }}" placeholder="Ex: Connexion, Création...">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Date début</label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Date fin</label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button onclick="filterLogs()" class="btn-primary-custom w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                    <button onclick="resetFilters()" class="btn-secondary-custom">
                        <i class="fas fa-undo-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-history"></i></div>
            <div class="stat-label">Total logs</div>
            <div class="stat-value">{{ $logs->total() }}</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-label">Aujourd'hui</div>
            <div class="stat-value">{{ $logs_aujourdhui ?? 0 }}</div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Utilisateurs actifs</div>
            <div class="stat-value">{{ $users->count() }}</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Actions distinctes</div>
            <div class="stat-value">{{ $actions->count() }}</div>
        </div>
    </div>

    <!-- Tableau des logs -->
    <div class="data-card">
        <div class="card-header-custom purple">
            <div class="card-header-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            <div>
                <h3 class="card-header-title">Historique des actions</h3>
                <p class="card-header-subtitle">Liste complète des actions enregistrées</p>
            </div>
            <div class="ms-auto">
                <button onclick="exportLogs()" class="btn-success-custom me-2">
                    <i class="fas fa-file-excel me-2"></i>Exporter
                </button>
                <button onclick="clearLogs()" class="btn-danger-custom">
                    <i class="fas fa-trash-alt me-2"></i>Nettoyer
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-user me-1"></i> Utilisateur</th>
                        <th><i class="fas fa-bolt me-1"></i> Action</th>
                        <th><i class="fas fa-database me-1"></i> Table</th>
                        <th><i class="fas fa-info-circle me-1"></i> Détails</th>
                        <th><i class="fas fa-network-wired me-1"></i> IP</th>
                        <th><i class="fas fa-calendar me-1"></i> Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td data-label="Utilisateur">
                            <strong>{{ $log->user?->name ?? 'Système' }}</strong>
                            <br><small class="text-muted">{{ $log->user?->email ?? '' }}</small>
                        </td>
                        <td data-label="Action">
                            <span class="badge-info">
                                <i class="fas fa-{{ $log->action == 'Connexion' ? 'sign-in-alt' : ($log->action == 'Déconnexion' ? 'sign-out-alt' : 'edit') }} me-1"></i>
                                {{ $log->action }}
                            </span>
                        </td>
                        <td data-label="Table">
                            <span class="badge-light">{{ $log->table ?? '-' }}</span>
                        </td>
                        <td data-label="Détails" class="text-muted small">
                            {{ Str::limit($log->details, 60) }}
                        </td>
                        <td data-label="IP">
                            <code>{{ $log->ip }}</code>
                        </td>
                        <td data-label="Date">
                            <strong>{{ $log->created_at->format('d/m/Y') }}</strong>
                            <br><small>{{ $log->created_at->format('H:i:s') }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h5>Aucun log trouvé</h5>
                                <p class="text-muted">Aucune activité enregistrée pour le moment</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de {{ $logs->firstItem() ?? 0 }} à {{ $logs->lastItem() ?? 0 }} sur {{ $logs->total() }} logs
                </div>
                <div>
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 15px 22px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-custom.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }
    
    .card-header-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    
    .card-header-title {
        font-size: 15px;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    
    .card-header-subtitle {
        font-size: 11px;
        margin: 3px 0 0;
        color: rgba(255,255,255,0.8);
    }
    
    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    
    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        transition: all 0.2s;
        background: white;
    }
    
    .form-control-custom:focus {
        border-color: #8b5cf6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
    }
    
    .btn-secondary-custom {
        background: #64748b;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .btn-secondary-custom:hover {
        background: #475569;
        transform: translateY(-2px);
    }
    
    .btn-success-custom {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    
    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    
    .btn-danger-custom {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    
    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.3);
    }
    
    .badge-info {
        background: #dbeafe;
        color: #2563eb;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-light {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-family: monospace;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    
    .stat-primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
    .stat-success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-info .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
    .stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    
    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 25px;
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
        vertical-align: middle;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    code {
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 11px;
        font-family: monospace;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px;
    }
    
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
    }
    
    .empty-state h5 {
        font-size: 16px;
        color: #64748b;
        margin: 15px 0 5px;
    }
    
    .text-muted {
        color: #94a3b8;
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
<script>
    function filterLogs() {
        let url = '{{ url("/admin/logs") }}?';
        if(document.getElementById('user_id').value) url += 'user_id=' + document.getElementById('user_id').value + '&';
        if(document.getElementById('action').value) url += 'action=' + document.getElementById('action').value + '&';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value;
        window.location.href = url;
    }
    
    function resetFilters() {
        window.location.href = '{{ url("/admin/logs") }}';
    }
    
    function exportLogs() {
        let url = '{{ url("/admin/logs/export") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value;
        window.open(url, '_blank');
    }
    
    function clearLogs() {
        if(confirm('⚠️ Attention : Cette action supprimera TOUS les logs. Êtes-vous sûr ?')) {
            window.location.href = '{{ url("/admin/logs/clear") }}';
        }
    }
</script>
@endpush
@endsection