@extends('layouts.comptable')

@section('title', 'Plan comptable SYSCOHADA')
@section('header-title', '📊 Plan comptable SYSCOHADA')
@section('header-subtitle', 'Gestion complète du plan comptable OHADA')

@section('comptable-content')
<div class="fade-up">

    <!-- Messages de succès/erreur -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Actions -->
    <div class="actions-bar">
        <div class="actions-left">
            <a href="{{ url('/comptable/plan-comptable/create') }}" class="btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Nouveau compte
            </a>
        </div>
        <div class="actions-right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchCompte" placeholder="Rechercher un compte...">
            </div>
            <select id="classeFilter" class="filter-select">
                <option value="">Toutes classes</option>
                <option value="1">Classe 1 - Capitaux propres</option>
                <option value="2">Classe 2 - Actif immobilisé</option>
                <option value="3">Classe 3 - Actif circulant (Stocks)</option>
                <option value="4">Classe 4 - Tiers</option>
                <option value="5">Classe 5 - Trésorerie</option>
                <option value="6">Classe 6 - Charges</option>
                <option value="7">Classe 7 - Produits</option>
                <option value="8">Classe 8 - Comptes spéciaux</option>
                <option value="9">Classe 9 - Engagements hors bilan</option>
            </select>
            <select id="typeFilter" class="filter-select">
                <option value="">Tous types</option>
                <option value="Actif">Actif</option>
                <option value="Passif">Passif</option>
                <option value="Charge">Charge</option>
                <option value="Produit">Produit</option>
            </select>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-table-list"></i></div>
            <div class="stat-info">
                <span class="stat-label">Total comptes</span>
                <span class="stat-value">{{ $stats['total'] ?? 0 }}</span>
            </div>
        </div>
        @foreach($stats['par_classe'] ?? [] as $stat)
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-layer-group"></i></div>
            <div class="stat-info">
                <span class="stat-label">Classe {{ $stat->classe }}</span>
                <span class="stat-value">{{ $stat->total }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tableau -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Nom du compte</th>
                        <th>Classe</th>
                        <th>Type OHADA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="comptesTableBody">
                    @forelse($comptes as $compte)
                    <tr data-classe="{{ $compte->classe }}" data-type="{{ $compte->type }}" data-numero="{{ $compte->numero }}" data-nom="{{ strtolower($compte->nom) }}">
                        <td><strong class="text-primary">{{ $compte->numero }}</strong></td>
                        <td>{{ $compte->nom }}</span>
                        <td><span class="badge classe">Classe {{ $compte->classe }}</span></span>
                        <td>
                            @if($compte->type == 'Actif')
                                <span class="badge actif"><i class="fas fa-chart-line me-1"></i>Actif</span>
                            @elseif($compte->type == 'Passif')
                                <span class="badge passif"><i class="fas fa-chart-pie me-1"></i>Passif</span>
                            @elseif($compte->type == 'Charge')
                                <span class="badge charge"><i class="fas fa-arrow-down me-1"></i>Charge</span>
                            @else
                                <span class="badge produit"><i class="fas fa-arrow-up me-1"></i>Produit</span>
                            @endif
                        </span>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ url('/comptable/plan-comptable/' . $compte->id) }}" class="action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/comptable/plan-comptable/' . $compte->id . '/edit') }}" class="action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ url('/comptable/plan-comptable/' . $compte->id . '/delete') }}" class="action-btn delete" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-database fa-3x text-muted mb-3 d-block"></i>
                            Aucun compte trouvé
                        </span
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $comptes->links() }}
        </div>
    </div>
</div>

@push('styles')
<style>
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Actions Bar */
    .actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }
    .actions-left {
        display: flex;
        gap: 12px;
    }
    .actions-right {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99,102,241,0.4);
        color: white;
    }
    .search-box {
        position: relative;
    }
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }
    .search-box input {
        padding: 10px 12px 10px 38px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        width: 250px;
        transition: all 0.3s;
    }
    .search-box input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .filter-select {
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        background: white;
        cursor: pointer;
    }
    .filter-select:focus {
        outline: none;
        border-color: #6366f1;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .stat-icon.blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }
    .stat-icon.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }
    .stat-info {
        flex: 1;
    }
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        display: block;
    }
    
    /* Data Card */
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead th {
        background: #f8fafc;
        padding: 14px 16px;
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
    
    /* Badges */
    .badge {
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge.classe {
        background: #dbeafe;
        color: #2563eb;
    }
    .badge.actif {
        background: #d1fae5;
        color: #059669;
    }
    .badge.passif {
        background: #fef3c7;
        color: #d97706;
    }
    .badge.charge {
        background: #fee2e2;
        color: #dc2626;
    }
    .badge.produit {
        background: #e0e7ff;
        color: #4f46e5;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        text-decoration: none;
    }
    .action-btn.view {
        background: #e0e7ff;
        color: #4f46e5;
    }
    .action-btn.view:hover {
        background: #4f46e5;
        color: white;
    }
    .action-btn.edit {
        background: #fef3c7;
        color: #d97706;
    }
    .action-btn.edit:hover {
        background: #d97706;
        color: white;
    }
    .action-btn.delete {
        background: #fee2e2;
        color: #dc2626;
    }
    .action-btn.delete:hover {
        background: #dc2626;
        color: white;
    }
    
    .pagination-wrapper {
        padding: 15px 20px;
        border-top: 1px solid #e2e8f0;
    }
    .text-primary {
        color: #4f46e5 !important;
    }
    .text-center {
        text-align: center;
    }
    .py-5 {
        padding-top: 40px;
        padding-bottom: 40px;
    }
    .text-muted {
        color: #94a3b8;
    }
    
    @media (max-width: 768px) {
        .actions-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .actions-right {
            flex-direction: column;
        }
        .search-box input {
            width: 100%;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Filtre recherche
    document.getElementById('searchCompte')?.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        document.querySelectorAll('#comptesTableBody tr').forEach(row => {
            let numero = row.dataset.numero?.toLowerCase() || '';
            let nom = row.dataset.nom?.toLowerCase() || '';
            let match = numero.includes(term) || nom.includes(term);
            row.style.display = match ? '' : 'none';
        });
    });
    
    // Filtre classe
    document.getElementById('classeFilter')?.addEventListener('change', function() {
        let classe = this.value;
        document.querySelectorAll('#comptesTableBody tr').forEach(row => {
            let rowClasse = row.dataset.classe;
            row.style.display = !classe || rowClasse === classe ? '' : 'none';
        });
    });
    
    // Filtre type
    document.getElementById('typeFilter')?.addEventListener('change', function() {
        let type = this.value;
        document.querySelectorAll('#comptesTableBody tr').forEach(row => {
            let rowType = row.dataset.type;
            row.style.display = !type || rowType === type ? '' : 'none';
        });
    });
    
    // Auto-fermeture alertes
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush
@endsection