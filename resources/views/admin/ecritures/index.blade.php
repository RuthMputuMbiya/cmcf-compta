@extends('layouts.admin')

@section('title', 'Gestion des écritures')
@section('header-title', '📋 Gestion des écritures')
@section('header-subtitle', 'Administration des écritures comptables')

@section('admin-content')
<div class="fade-up">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Cartes statistiques -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total écritures</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-label">Validées</div>
                <div class="stat-value">{{ $stats['validees'] }}</div>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-pencil-alt"></i></div>
            <div class="stat-info">
                <div class="stat-label">Brouillons</div>
                <div class="stat-value">{{ $stats['brouillons'] }}</div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-info">
                <div class="stat-label">Balance</div>
                <div class="stat-value">{{ number_format($stats['total_debit'] - $stats['total_credit'], 0, ',', ' ') }} FC</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-card mb-4">
        <div class="filters-header">
            <div class="filters-icon"><i class="fas fa-filter"></i></div>
            <div>
                <h3 class="filters-title">Filtres de recherche</h3>
                <p class="filters-subtitle">Affinez votre recherche</p>
            </div>
        </div>
        <div class="filters-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select id="statut" class="form-control-custom">
                        <option value="">Tous</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validée</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Compte</label>
                    <select id="compte" class="form-control-custom">
                        <option value="">Tous les comptes</option>
                        @foreach($comptes as $cpt)
                        <option value="{{ $cpt->numero }}" {{ request('compte') == $cpt->numero ? 'selected' : '' }}>
                            {{ $cpt->numero }} - {{ $cpt->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" id="search" class="form-control-custom" placeholder="N° pièce ou libellé..." value="{{ request('search') }}">
                </div>
                <div class="col-md-12 d-flex gap-2">
                    <button onclick="applyFilters()" class="btn-filter"><i class="fas fa-search me-2"></i>Filtrer</button>
                    <button onclick="resetFilters()" class="btn-reset"><i class="fas fa-undo-alt me-2"></i>Réinitialiser</button>
                    <button onclick="exporterPDF()" class="btn-pdf ms-auto"><i class="fas fa-file-pdf me-2"></i>Exporter PDF</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon"><i class="fas fa-list-alt"></i></div>
            <div>
                <h3 class="card-header-title">Liste des écritures</h3>
                <p class="card-header-subtitle">Toutes les écritures comptables</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Pièce</th>
                        <th>Date</th>
                        <th>Libellé</th>
                        <th>Compte Débit</th>
                        <th>Montant Débit</th>
                        <th>Compte Crédit</th>
                        <th>Montant Crédit</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ecritures as $e)
                    <tr>
                        <td><strong>{{ $e->numero_piece }}</strong></td>
                        <td>{{ $e->date->format('d/m/Y') }}</span>
                        <td>{{ Str::limit($e->libelle, 40) }}</span>
                        <td>{{ $e->compte_debit }}<br><small>{{ Str::limit($e->compteDebiteur->nom ?? '', 20) }}</small></span>
                        <td class="text-success">{{ number_format($e->montant_debit, 0, ',', ' ') }}</span>
                        <td>{{ $e->compte_credit }}<br><small>{{ Str::limit($e->compteCrediteur->nom ?? '', 20) }}</small></span>
                        <td class="text-danger">{{ number_format($e->montant_credit, 0, ',', ' ') }}</span>
                        <td>
                            @if($e->statut == 'valide')
                                <span class="badge-valid">Validée</span>
                            @else
                                <span class="badge-draft">Brouillon</span>
                            @endif
                        </span>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ url('/admin/ecritures/' . $e->id) }}" class="action-btn view" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="{{ url('/admin/ecritures/' . $e->id . '/delete') }}" class="action-btn delete" title="Supprimer"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">Aucune écriture trouvée</span>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $ecritures->links() }}
        </div>
    </div>
</div>

@push('styles')
<style>
    .fade-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
    .stat-card { background: white; border-radius: 16px; padding: 18px; display: flex; gap: 15px; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; }
    .stat-primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
    .stat-success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .stat-info .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
    .stat-icon i { font-size: 22px; }
    .stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; }
    .stat-value { font-size: 24px; font-weight: 800; color: #1e293b; }
    .filters-card { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 25px; }
    .filters-header { background: linear-gradient(135deg, #8b5cf6, #7c3aed); padding: 12px 20px; display: flex; align-items: center; gap: 12px; }
    .filters-icon { width: 35px; height: 35px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
    .filters-title { font-size: 14px; font-weight: 600; margin: 0; color: white; }
    .filters-subtitle { font-size: 11px; margin: 2px 0 0; color: rgba(255,255,255,0.8); }
    .filters-body { padding: 20px; }
    .form-label { font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block; }
    .form-control-custom { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; }
    .btn-filter { background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none; padding: 10px 20px; border-radius: 10px; color: white; font-weight: 600; cursor: pointer; }
    .btn-reset { background: #64748b; border: none; padding: 10px 20px; border-radius: 10px; color: white; font-weight: 600; cursor: pointer; }
    .btn-pdf { background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 10px 20px; border-radius: 10px; color: white; font-weight: 600; cursor: pointer; }
    .data-card { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
    .card-header-custom { background: linear-gradient(135deg, #3b82f6, #2563eb); padding: 12px 20px; display: flex; align-items: center; gap: 12px; }
    .card-header-icon { width: 35px; height: 35px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
    .card-header-title { font-size: 14px; font-weight: 600; margin: 0; color: white; }
    .card-header-subtitle { font-size: 11px; margin: 2px 0 0; color: rgba(255,255,255,0.8); }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th { background: #f8fafc; padding: 12px; font-size: 10px; font-weight: 600; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    .data-table tbody td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 12px; vertical-align: middle; }
    .badge-valid { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-draft { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
    .action-buttons { display: flex; gap: 8px; }
    .action-btn { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s; text-decoration: none; }
    .action-btn.view { background: #e0e7ff; color: #4f46e5; }
    .action-btn.view:hover { background: #4f46e5; color: white; }
    .action-btn.delete { background: #fee2e2; color: #dc2626; }
    .action-btn.delete:hover { background: #dc2626; color: white; }
    .text-success { color: #059669; }
    .text-danger { color: #dc2626; }
    .pagination-wrapper { padding: 15px 20px; border-top: 1px solid #e2e8f0; }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .data-table thead { display: none; }
        .data-table tbody tr { display: block; margin-bottom: 10px; border: 1px solid #e2e8f0; border-radius: 12px; }
        .data-table tbody td { display: flex; justify-content: space-between; align-items: center; padding: 10px; }
        .data-table tbody td:before { content: attr(data-label); font-weight: 600; font-size: 11px; color: #64748b; width: 40%; }
    }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        let url = window.location.pathname + '?';
        if(document.getElementById('date_debut')?.value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin')?.value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('statut')?.value) url += 'statut=' + document.getElementById('statut').value + '&';
        if(document.getElementById('compte')?.value) url += 'compte=' + document.getElementById('compte').value + '&';
        if(document.getElementById('search')?.value) url += 'search=' + document.getElementById('search').value;
        window.location.href = url;
    }
    function resetFilters() { window.location.href = window.location.pathname; }
    function exporterPDF() {
        let url = '{{ url("/admin/ecritures/export/pdf") }}?';
        if(document.getElementById('date_debut')?.value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin')?.value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('statut')?.value) url += 'statut=' + document.getElementById('statut').value;
        window.open(url, '_blank');
    }
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        let cells = row.querySelectorAll('td');
        if(cells.length >= 9) {
            cells[0].setAttribute('data-label', 'N° Pièce');
            cells[1].setAttribute('data-label', 'Date');
            cells[2].setAttribute('data-label', 'Libellé');
            cells[3].setAttribute('data-label', 'Compte Débit');
            cells[4].setAttribute('data-label', 'Montant Débit');
            cells[5].setAttribute('data-label', 'Compte Crédit');
            cells[6].setAttribute('data-label', 'Montant Crédit');
            cells[7].setAttribute('data-label', 'Statut');
            cells[8].setAttribute('data-label', 'Actions');
        }
    });
</script>
@endpush
@endsection