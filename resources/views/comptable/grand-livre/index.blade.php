@extends('layouts.comptable')

@section('title', 'Grand Livre')
@section('header-title', '📚 GRAND LIVRE')
@section('header-subtitle', 'Soldes détaillés des comptes')

@section('comptable-content')
<div class="fade-up">
    
    <!-- Filtres de recherche -->
    <div class="filters-card mb-4">
        <div class="filters-header">
            <div class="filters-icon">
                <i class="fas fa-filter"></i>
            </div>
            <div>
                <h3 class="filters-title">Filtres de recherche</h3>
                <p class="filters-subtitle">Affinez votre recherche</p>
            </div>
        </div>
        <div class="filters-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-book me-1 text-primary"></i>Compte comptable
                    </label>
                    <select id="compte" class="form-control-custom">
                        <option value="">Tous les comptes</option>
                        @foreach($comptes ?? [] as $cpt)
                        <option value="{{ $cpt->numero }}" {{ request('compte') == $cpt->numero ? 'selected' : '' }}>
                            {{ $cpt->numero }} - {{ $cpt->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1 text-primary"></i>Date début
                    </label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1 text-primary"></i>Date fin
                    </label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button onclick="filterCompte()" class="btn-filter w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-end">
                    <button onclick="exporterPDF()" class="btn-success-custom">
                        <i class="fas fa-file-pdf me-2"></i>Exporter PDF
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
                <div class="stat-label">Total Débit</div>
                <div class="stat-value">{{ number_format($totalDebit, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Crédit</div>
                <div class="stat-value">{{ number_format($totalCredit, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-label">Nombre comptes</div>
                <div class="stat-value">{{ count($ecrituresParCompte) }}</div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-info">
                <div class="stat-label">Balance générale</div>
                <div class="stat-value {{ $totalDebit - $totalCredit == 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format(abs($totalDebit - $totalCredit), 0, ',', ' ') }} FC
                </div>
            </div>
        </div>
    </div>

    <!-- Affichage des comptes avec écritures -->
    @forelse($ecrituresParCompte as $compteData)
    <div class="data-card mb-4">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div>
                <h3 class="card-header-title">Compte {{ $compteData['compte'] }} - {{ $compteData['nom_compte'] }}</h3>
                <p class="card-header-subtitle">
                    Solde final : 
                    <span class="{{ $compteData['solde_final'] > 0 ? 'text-danger' : ($compteData['solde_final'] < 0 ? 'text-success' : 'text-info') }}">
                        {{ number_format(abs($compteData['solde_final']), 0, ',', ' ') }} FC
                    </span>
                    ({{ $compteData['nature_final'] }})
                </p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-date">Date</th>
                        <th class="col-piece">N° Pièce</th>
                        <th class="col-libelle">Libellé</th>
                        <th class="col-montant text-right">Débit (FC)</th>
                        <th class="col-montant text-right">Crédit (FC)</th>
                        <th class="col-solde text-right">Solde (FC)</th>
                        <th class="col-nature text-center">Nature</th>
                    </tr>
                </thead>
                <tbody>
                    @php $soldeCumul = 0; @endphp
                    @foreach($compteData['lignes'] as $ligne)
                    @php 
                        $soldeCumul += $ligne['debit'] - $ligne['credit'];
                        $natureSolde = $soldeCumul > 0 ? 'Débiteur' : ($soldeCumul < 0 ? 'Créditeur' : 'Équilibré');
                        $soldeAbsolu = abs($soldeCumul);
                    @endphp
                    <tr>
                        <td data-label="Date">
                            <span class="date-badge">{{ \Carbon\Carbon::parse($ligne['date'])->format('d/m/Y') }}</span>
                        </td>
                        <td data-label="N° Pièce">
                            <span class="piece-badge">{{ $ligne['numero_piece'] }}</span>
                        </td>
                        <td data-label="Libellé">
                            <span class="libelle-text">{{ $ligne['libelle'] }}</span>
                        </td>
                        <td data-label="Débit" class="text-right">
                            @if($ligne['debit'] > 0)
                                <span class="debit-montant">{{ number_format($ligne['debit'], 0, ',', ' ') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td data-label="Crédit" class="text-right">
                            @if($ligne['credit'] > 0)
                                <span class="credit-montant">{{ number_format($ligne['credit'], 0, ',', ' ') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td data-label="Solde" class="text-right">
                            <span class="solde-montant {{ $soldeCumul > 0 ? 'text-danger' : ($soldeCumul < 0 ? 'text-success' : 'text-info') }}">
                                {{ number_format($soldeAbsolu, 0, ',', ' ') }}
                            </span>
                        </td>
                        <td data-label="Nature" class="text-center">
                            <span class="nature-badge {{ $soldeCumul > 0 ? 'nature-debit' : ($soldeCumul < 0 ? 'nature-credit' : 'nature-equilibre') }}">
                                {{ $natureSolde }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-footer">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAUX DU COMPTE :</td>
                        <td class="text-right text-success fw-bold">
                            {{ number_format(collect($compteData['lignes'])->sum('debit'), 0, ',', ' ') }}
                        </td>
                        <td class="text-right text-danger fw-bold">
                            {{ number_format(collect($compteData['lignes'])->sum('credit'), 0, ',', ' ') }}
                        </td>
                        <td class="text-right fw-bold {{ $compteData['solde_final'] > 0 ? 'text-danger' : ($compteData['solde_final'] < 0 ? 'text-success' : 'text-info') }}">
                            {{ number_format(abs($compteData['solde_final']), 0, ',', ' ') }}
                        </td>
                        <td class="text-center">
                            <span class="nature-badge {{ $compteData['solde_final'] > 0 ? 'nature-debit' : ($compteData['solde_final'] < 0 ? 'nature-credit' : 'nature-equilibre') }}">
                                {{ $compteData['nature_final'] }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @empty
    <div class="data-card">
        <div class="text-center py-5">
            <i class="fas fa-database fa-3x text-muted mb-3 d-block"></i>
            <h5>Aucune écriture comptable trouvée</h5>
            <p class="text-muted">Aucune écriture validée n'a été enregistrée pour le moment.</p>
            <a href="{{ url('/comptable/ecritures/create') }}" class="btn-primary-custom mt-3">
                <i class="fas fa-plus-circle me-2"></i>Créer une écriture
            </a>
        </div>
    </div>
    @endforelse
</div>

@push('styles')
<style>
    /* Animations */
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Filtres */
    .filters-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .filters-header {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .filters-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .filters-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    .filters-subtitle {
        font-size: 11px;
        margin: 2px 0 0;
        color: rgba(255,255,255,0.8);
    }
    .filters-body {
        padding: 20px;
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
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }
    .btn-filter {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79,70,229,0.3);
    }
    .btn-success-custom {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        padding: 10px 20px;
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
    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }

    /* Statistiques */
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
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
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
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-success .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-danger .stat-icon { background: #fee2e2; color: #ef4444; }
    .stat-primary .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-info .stat-icon { background: #e0e7ff; color: #6366f1; }
    .stat-icon i { font-size: 22px; }
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
    }

    /* Cartes des comptes */
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .card-header-custom {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .card-header-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
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
        color: #94a3b8;
    }

    /* Tableau */
    .table-responsive {
        overflow-x: auto;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .data-table thead th {
        background: #f8fafc;
        padding: 14px 12px;
        font-size: 11px;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    .col-date { width: 85px; }
    .col-piece { width: 100px; }
    .col-libelle { width: auto; }
    .col-montant { width: 130px; }
    .col-solde { width: 120px; }
    .col-nature { width: 100px; }

    .data-table tbody td {
        padding: 12px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .data-table tbody tr:hover {
        background: #fefce8;
    }
    .table-footer {
        background: #f1f5f9;
        border-top: 2px solid #e2e8f0;
    }
    .table-footer td {
        padding: 12px;
        font-size: 13px;
        font-weight: 700;
    }

    /* Badges et styles */
    .date-badge {
        font-family: 'Courier New', monospace;
        font-size: 11px;
        font-weight: 600;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .piece-badge {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #4f46e5;
        background: #eef2ff;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
        font-size: 11px;
    }
    .libelle-text {
        color: #1e293b;
        font-size: 12px;
        font-weight: 500;
    }
    .debit-montant {
        color: #059669;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }
    .credit-montant {
        color: #dc2626;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }
    .solde-montant {
        font-weight: 700;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }
    .nature-badge {
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .nature-debit {
        background: #fee2e2;
        color: #dc2626;
    }
    .nature-credit {
        background: #d1fae5;
        color: #059669;
    }
    .nature-equilibre {
        background: #dbeafe;
        color: #3b82f6;
    }

    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    .text-success {
        color: #059669;
    }
    .text-danger {
        color: #dc2626;
    }
    .text-info {
        color: #3b82f6;
    }
    .text-muted {
        color: #94a3b8;
    }
    .fw-bold {
        font-weight: 700;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .data-table thead {
            display: none;
        }
        .data-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
        }
        .data-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .data-table tbody td:last-child {
            border-bottom: none;
        }
        .data-table tbody td:before {
            content: attr(data-label);
            font-weight: 700;
            font-size: 10px;
            color: #64748b;
            width: 35%;
            text-transform: uppercase;
        }
        .table-footer td {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
    }

    /* Impression */
    @media print {
        .filters-card, .stats-grid, .btn-filter, .btn-success-custom {
            display: none;
        }
        .data-card {
            box-shadow: none;
            border: 1px solid #ccc;
            page-break-inside: avoid;
        }
        .card-header-custom {
            background: #e2e8f0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .card-header-title, .card-header-subtitle {
            color: #000;
        }
        .data-table thead th {
            background: #e2e8f0;
            color: #000;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function filterCompte() {
        let compte = document.getElementById('compte').value;
        let dateDebut = document.getElementById('date_debut').value;
        let dateFin = document.getElementById('date_fin').value;
        let url = '{{ url("/comptable/grand-livre") }}';
        let params = [];
        if(compte) params.push('compte=' + compte);
        if(dateDebut) params.push('date_debut=' + dateDebut);
        if(dateFin) params.push('date_fin=' + dateFin);
        if(params.length) url += '?' + params.join('&');
        window.location.href = url;
    }
    
    function exporterPDF() {
        let compte = document.getElementById('compte').value;
        let dateDebut = document.getElementById('date_debut').value;
        let dateFin = document.getElementById('date_fin').value;
        let url = '{{ url("/comptable/export/grand-livre/pdf") }}';
        let params = [];
        if(compte) params.push('compte=' + compte);
        if(dateDebut) params.push('date_debut=' + dateDebut);
        if(dateFin) params.push('date_fin=' + dateFin);
        if(params.length) url += '?' + params.join('&');
        window.open(url, '_blank');
    }
    
    // Ajouter les labels pour responsive
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        let cells = row.querySelectorAll('td');
        if (cells.length >= 7) {
            cells[0].setAttribute('data-label', 'Date');
            cells[1].setAttribute('data-label', 'N° Pièce');
            cells[2].setAttribute('data-label', 'Libellé');
            cells[3].setAttribute('data-label', 'Débit');
            cells[4].setAttribute('data-label', 'Crédit');
            cells[5].setAttribute('data-label', 'Solde');
            cells[6].setAttribute('data-label', 'Nature');
        }
    });
</script>
@endpush
@endsection