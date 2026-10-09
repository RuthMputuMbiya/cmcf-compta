@extends('layouts.caissier')

@section('title', 'Gestion bancaire')
@section('header-title', '🏦 Gestion bancaire')
@section('header-subtitle', 'Historique des opérations bancaires')

@section('caissier-content')
<div class="fade-up">

    <!-- Filtres et actions -->
    <div class="actions-filters-card mb-4">
        <div class="actions-filters-header">
            <div class="actions-filters-icon"><i class="fas fa-filter"></i></div>
            <div>
                <h3 class="actions-filters-title">Filtres et actions</h3>
                <p class="actions-filters-subtitle">Rechercher et filtrer les opérations</p>
            </div>
        </div>
        <div class="actions-filters-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div class="d-flex gap-2">
                    <a href="{{ url('/caissier/banque/depot') }}" class="btn-success-custom">
                        <i class="fas fa-plus-circle me-2"></i>Dépôt
                    </a>
                    <a href="{{ url('/caissier/banque/retrait') }}" class="btn-danger-custom">
                        <i class="fas fa-minus-circle me-2"></i>Retrait
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <input type="date" id="date_debut" class="form-control-custom" placeholder="Date début" style="width: 150px;">
                    <input type="date" id="date_fin" class="form-control-custom" placeholder="Date fin" style="width: 150px;">
                    <select id="type_filter" class="form-control-custom" style="width: 130px;">
                        <option value="">Tous les types</option>
                        <option value="depot">Dépôts</option>
                        <option value="retrait">Retraits</option>
                    </select>
                    <button onclick="filtrer()" class="btn-filter">
                        <i class="fas fa-search"></i>
                    </button>
                    <button onclick="resetFilters()" class="btn-reset">
                        <i class="fas fa-undo-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total dépôts</div>
                <div class="stat-value">{{ number_format($depots ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total retraits</div>
                <div class="stat-value">{{ number_format($retraits ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-label">Solde actuel</div>
                <div class="stat-value {{ ($solde ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format(abs($solde ?? 0), 0, ',', ' ') }} FC
                </div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-list"></i></div>
            <div class="stat-info">
                <div class="stat-label">Opérations</div>
                <div class="stat-value">{{ $operations->total() }}</div>
            </div>
        </div>
    </div>

    <!-- Tableau des opérations -->
    <div class="data-card">
        <div class="card-header-custom blue">
            <div class="card-header-icon"><i class="fas fa-university"></i></div>
            <div>
                <h3 class="card-header-title">Historique des opérations bancaires</h3>
                <p class="card-header-subtitle">Liste détaillée des transactions</p>
            </div>
            <div class="ms-auto">
                <button onclick="exporterPDF()" class="btn-export">
                    <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Libellé</th>
                        <th class="text-end">Montant (FC)</th>
                        <th>Référence</th>
                        <th>Enregistré par</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operations as $op)
                    <tr data-type="{{ $op->type }}">
                        <td data-label="Date">{{ $op->date->format('d/m/Y') }}</span>
                        <td data-label="Type">
                            @if($op->type == 'depot')
                                <span class="badge-success"><i class="fas fa-arrow-down me-1"></i>Dépôt</span>
                            @else
                                <span class="badge-danger"><i class="fas fa-arrow-up me-1"></i>Retrait</span>
                            @endif
                        </span>
                        <td data-label="Libellé">{{ $op->libelle }}</span>
                        <td data-label="Montant" class="text-end {{ $op->type == 'depot' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ number_format(abs($op->montant), 0, ',', ' ') }} FC
                        </span>
                        <td data-label="Référence">{{ $op->reference ?? '-' }}</span>
                        <td data-label="Enregistré par">{{ $op->enregistrePar->name ?? '-' }}</span>
                        <td data-label="Actions" class="text-center">
                            <button onclick="supprimer({{ $op->id }}, '{{ $op->libelle }}')" class="btn-delete" title="Supprimer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </span>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <td colspan="7" class="py-5">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Aucune opération trouvée</h5>
                                <p class="text-muted">Aucune transaction bancaire n'a été enregistrée</p>
                            </div>
                        </span>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-container">
            {{ $operations->links() }}
        </div>
    </div>
</div>

@push('styles')
<style>
    .actions-filters-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .actions-filters-header {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .actions-filters-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .actions-filters-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    .actions-filters-subtitle {
        font-size: 11px;
        margin: 2px 0 0;
        color: rgba(255,255,255,0.8);
    }
    .actions-filters-body {
        padding: 20px;
    }
    .btn-success-custom, .btn-danger-custom {
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-success-custom {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    .btn-danger-custom {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.3);
    }
    .btn-filter, .btn-reset {
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-filter {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border: none;
        color: white;
    }
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
    }
    .btn-reset {
        background: #64748b;
        border: none;
        color: white;
    }
    .btn-reset:hover {
        background: #475569;
        transform: translateY(-2px);
    }
    .card-header-custom.blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .btn-export {
        background: rgba(255,255,255,0.2);
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        font-size: 12px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-export:hover {
        background: rgba(255,255,255,0.3);
    }
    .btn-delete {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        color: #dc2626;
        transform: scale(1.1);
    }
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
    .stat-info .stat-icon { background: #e0e7ff; color: #6366f1; }
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
    .pagination-container {
        padding: 12px 20px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
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
    .empty-state p {
        font-size: 13px;
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
    function filtrer() {
        let url = '{{ url("/caissier/banque") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('type_filter').value) url += 'type=' + document.getElementById('type_filter').value;
        window.location.href = url;
    }
    
    function resetFilters() {
        window.location.href = '{{ url("/caissier/banque") }}';
    }
    
    function supprimer(id, libelle) {
        if(confirm(`⚠ Supprimer l'opération "${libelle}" ? Cette action est irréversible.`)) {
            window.location.href = `/caissier/banque/${id}/delete`;
        }
    }
    
    function exporterPDF() {
        let url = '{{ url("/caissier/export/banque/pdf") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value;
        window.open(url, '_blank');
    }
</script>
@endpush
@endsection