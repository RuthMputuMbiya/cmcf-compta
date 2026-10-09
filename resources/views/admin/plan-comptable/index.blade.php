@extends('layouts.admin')

@section('title', 'Plan comptable SYSCOHADA')
@section('header-title', '📊 Plan comptable SYSCOHADA')
@section('header-subtitle', 'Gestion complète du plan comptable OHADA')

@section('admin-content')
<div class="fade-up">

    <!-- Messages de succès/erreur -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d1fae5; color: #059669; border: none; border-radius: 12px; padding: 15px 20px; margin-bottom: 20px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #fee2e2; color: #dc2626; border: none; border-radius: 12px; padding: 15px 20px; margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex gap-2">
            <a href="{{ url('/admin/plan-comptable/create') }}" class="btn-primary-custom"><i class="fas fa-plus-circle me-2"></i>Nouveau compte</a>
            <a href="{{ url('/admin/plan-comptable/import') }}" class="btn-info-custom"><i class="fas fa-file-excel me-2"></i>Importer Excel</a>
            <a href="{{ url('/admin/plan-comptable/export') }}" class="btn-success-custom"><i class="fas fa-download me-2"></i>Exporter Excel</a>
            <a href="{{ url('/admin/plan-comptable/template') }}" class="btn-warning-custom"><i class="fas fa-download me-2"></i>Télécharger template</a>
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="searchCompte" class="form-control-custom" style="width: 250px;" placeholder="🔍 Rechercher...">
            <select id="classeFilter" class="form-control-custom" style="width: 130px;">
                <option value="">Toutes classes</option>
                <option value="1">Classe 1 - Capitaux propres</option>
                <option value="2">Classe 2 - Actif immobilisé</option>
                <option value="3">Classe 3 - Actif circulant</option>
                <option value="4">Classe 4 - Passif circulant</option>
                <option value="5">Classe 5 - Trésorerie</option>
                <option value="6">Classe 6 - Charges</option>
                <option value="7">Classe 7 - Produits</option>
                <option value="8">Classe 8 - Comptes spéciaux</option>
                <option value="9">Classe 9 - Engagements</option>
            </select>
            <select id="typeFilter" class="form-control-custom" style="width: 120px;">
                <option value="">Tous types</option>
                <option value="Actif">Actif</option>
                <option value="Passif">Passif</option>
                <option value="Charge">Charge</option>
                <option value="Produit">Produit</option>
            </select>
        </div>
    </div>

    <!-- Statistiques par classe -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary"><div class="stat-icon"><i class="fas fa-table-list"></i></div><div class="stat-label">Total comptes</div><div class="stat-value">{{ $stats['total'] ?? 0 }}</div></div>
        @foreach($stats['par_classe'] ?? [] as $stat)
        <div class="stat-card stat-purple"><div class="stat-icon"><i class="fas fa-layer-group"></i></div><div class="stat-label">Classe {{ $stat->classe }}</div><div class="stat-value">{{ $stat->total }}</div></div>
        @endforeach
    </div>

    <!-- Tableau plan comptable -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table" id="comptesTable">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Nom du compte</th>
                        <th>Classe</th>
                        <th>Type OHADA</th>
                        <th>Solde initial</th>
                        <th>Solde actuel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comptes as $compte)
                    <tr data-classe="{{ $compte->classe }}" data-type="{{ $compte->type }}" data-numero="{{ $compte->numero }}" data-nom="{{ strtolower($compte->nom) }}">
                        <td><strong class="text-primary">{{ $compte->numero }}</strong></td>
                        <td>{{ $compte->nom }}</span>
                        <td><span class="badge-info">Classe {{ $compte->classe }}</span></span>
                        <td>
                            @if($compte->type == 'Actif')
                                <span class="badge-success"><i class="fas fa-chart-line me-1"></i>Actif</span>
                            @elseif($compte->type == 'Passif')
                                <span class="badge-warning"><i class="fas fa-chart-pie me-1"></i>Passif</span>
                            @elseif($compte->type == 'Charge')
                                <span class="badge-danger"><i class="fas fa-arrow-down me-1"></i>Charge</span>
                            @else
                                <span class="badge-info"><i class="fas fa-arrow-up me-1"></i>Produit</span>
                            @endif
                        </span>
                        <td>{{ number_format($compte->solde_initial, 0, ',', ' ') }} FC</span>
                        <td>{{ number_format($compte->solde_actuel, 0, ',', ' ') }} FC</span>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ url('/admin/plan-comptable/' . $compte->id . '/edit') }}" class="text-warning" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="{{ url('/admin/plan-comptable/' . $compte->id) }}" class="text-info" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="{{ url('/admin/plan-comptable/' . $compte->id . '/delete') }}" class="text-danger" title="Supprimer" style="background: transparent; border: none; display: inline-block; text-decoration: none;">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-database fa-3x text-muted mb-3 d-block"></i>
                            Aucun compte trouvé
                        </span>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $comptes->links() }}
        </div>
    </div>

    <!-- Légende OHADA -->
    <div class="data-card mt-4">
        <div class="data-header">
            <h3><i class="fas fa-graduation-cap me-2"></i>Structure du plan comptable OHADA (SYSCOHADA)</h3>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3"><div class="info-box"><label>Classe 1</label><h6>Capitaux propres</h6><small>101 Capital, 106 Réserves, 12 Report à nouveau</small></div></div>
                <div class="col-md-3"><div class="info-box"><label>Classe 2</label><h6>Actif immobilisé</h6><small>21 Immobilisations corporelles, 23 Immobilisations financières</small></div></div>
                <div class="col-md-3"><div class="info-box"><label>Classe 3</label><h6>Actif circulant</h6><small>31 Stocks, 34 Créances, 38 Avances</small></div></div>
                <div class="col-md-3"><div class="info-box"><label>Classe 4</label><h6>Passif circulant</h6><small>40 Fournisseurs, 42 Personnel, 44 Etat</small></div></div>
                <div class="col-md-3"><div class="info-box"><label>Classe 5</label><h6>Trésorerie</h6><small>51 Banque, 52 Caisse, 53 Chèques postaux</small></div></div>
                <div class="col-md-3"><div class="info-box"><label>Classe 6</label><h6>Charges</h6><small>60 Achats, 62 Services extérieurs, 63 Impôts</small></div></div>
                <div class="col-md-3"><div class="info-box"><label>Classe 7</label><h6>Produits</h6><small>70 Ventes, 72 Subventions, 75 Produits financiers</small></div></div>
                <div class="col-md-3"><div class="info-box"><label>Classe 8-9</label><h6>Comptes spéciaux</h6><small>81 Amortissements, 91 Engagements hors bilan</small></div></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-primary-custom { background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(139,92,246,0.3); color: white; }
    .btn-info-custom { background: linear-gradient(135deg, #06b6d4, #0891b2); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-info-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(6,182,212,0.3); color: white; }
    .btn-success-custom { background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-success-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.3); color: white; }
    .btn-warning-custom { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-warning-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); color: white; }
    .badge-success { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-warning { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-danger { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .info-box { background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; height: 100%; }
    .info-box label { font-size: 10px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; display: block; margin-bottom: 5px; }
    .info-box h6 { font-size: 13px; font-weight: 700; margin: 0 0 5px 0; }
    .info-box small { font-size: 10px; color: #94a3b8; }
    .stat-card { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .stat-icon { width: 50px; height: 50px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
    .stat-primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
    .stat-purple .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
    .stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
    .stat-value { font-size: 26px; font-weight: 800; color: #1e293b; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
    .data-card { background: white; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 25px; }
    .data-header { padding: 18px 22px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .data-header h3 { font-size: 16px; font-weight: 700; margin: 0; color: #1e293b; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th { background: #f8fafc; padding: 12px 16px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    .data-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .form-control-custom { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s; }
    .form-control-custom:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.1); }
    .fade-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }
    .alert-dismissible .btn-close { padding: 1.25rem; }
    .text-warning { color: #f59e0b !important; }
    .text-info { color: #06b6d4 !important; }
    .text-danger { color: #ef4444 !important; }
    .text-primary { color: #8b5cf6 !important; }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('searchCompte')?.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        document.querySelectorAll('#comptesTable tbody tr').forEach(r => {
            let match = r.dataset.numero?.includes(term) || r.dataset.nom?.includes(term);
            r.style.display = match ? '' : 'none';
        });
    });
    document.getElementById('classeFilter')?.addEventListener('change', function() {
        let classe = this.value;
        document.querySelectorAll('#comptesTable tbody tr').forEach(r => {
            r.style.display = !classe || r.dataset.classe === classe ? '' : 'none';
        });
    });
    document.getElementById('typeFilter')?.addEventListener('change', function() {
        let type = this.value;
        document.querySelectorAll('#comptesTable tbody tr').forEach(r => {
            r.style.display = !type || r.dataset.type === type ? '' : 'none';
        });
    });
    
    // Auto-fermeture des alertes après 5 secondes
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush
@endsection