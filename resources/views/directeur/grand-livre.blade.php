@extends('layouts.directeur')

@section('title', 'Grand Livre')
@section('header-title', '📚 GRAND LIVRE')
@section('header-subtitle', 'Soldes détaillés des comptes')

@section('directeur-content')
<div class="fade-up">

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
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-book me-1 text-primary"></i>Compte comptable</label>
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
                    <label class="form-label"><i class="fas fa-calendar me-1 text-primary"></i>Date début</label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-calendar me-1 text-primary"></i>Date fin</label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button onclick="filtrer()" class="btn-filter w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-end">
                    <button onclick="exporterPDF()" class="btn-pdf w-100">
                        <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes stats -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-debit">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Débit</div>
                <div class="stat-value" id="totalDebitCard">0 FC</div>
            </div>
        </div>
        <div class="stat-card stat-credit">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Crédit</div>
                <div class="stat-value" id="totalCreditCard">0 FC</div>
            </div>
        </div>
        <div class="stat-card stat-comptes">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-label">Nombre comptes</div>
                <div class="stat-value" id="nbComptes">0</div>
            </div>
        </div>
        <div class="stat-card stat-equilibre">
            <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-info">
                <div class="stat-label">Balance générale</div>
                <div class="stat-value" id="balanceGenerale">0 FC</div>
            </div>
        </div>
    </div>

    <!-- Affichage des comptes -->
    @forelse($ecrituresParCompte as $compteData)
    <div class="data-card mb-4">
        <div class="card-header">
            <div class="card-header-icon"><i class="fas fa-book-open"></i></div>
            <div>
                <h3 class="card-header-title">Compte {{ $compteData['compte'] }} - {{ $compteData['nom_compte'] }}</h3>
                <p class="card-header-subtitle">
                    Solde final : 
                    <span class="badge {{ $compteData['solde_final'] > 0 ? 'badge-debit' : ($compteData['solde_final'] < 0 ? 'badge-credit' : 'badge-equilibre') }}">
                        {{ number_format(abs($compteData['solde_final']), 0, ',', ' ') }} FC
                    </span>
                    ({{ $compteData['nature_final'] }})
                </p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table-grand-livre">
                <thead>
                    <tr>
                        <th class="text-center">DATE</th>
                        <th class="text-center">N° PIÈCE</th>
                        <th class="text-left">LIBELLÉ</th>
                        <th class="text-right">DÉBIT (FC)</th>
                        <th class="text-right">CRÉDIT (FC)</th>
                        <th class="text-right">SOLDE (FC)</th>
                        <th class="text-center">NATURE</th>
                    </tr>
                </thead>
                <tbody>
                    @php $soldeCumul = 0; @endphp
                    @foreach($compteData['lignes'] as $ligne)
                    @php 
                        $soldeCumul += $ligne['debit'] - $ligne['credit'];
                        $natureSolde = $soldeCumul > 0 ? 'Débiteur' : ($soldeCumul < 0 ? 'Créditeur' : 'Équilibré');
                    @endphp
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($ligne['date'])->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $ligne['numero_piece'] }}</td>
                        <td class="text-left">{{ $ligne['libelle'] }}</td>
                        <td class="text-right text-success fw-bold">
                            @if($ligne['debit'] > 0) {{ number_format($ligne['debit'], 0, ',', ' ') }} @else — @endif
                        </td>
                        <td class="text-right text-danger fw-bold">
                            @if($ligne['credit'] > 0) {{ number_format($ligne['credit'], 0, ',', ' ') }} @else — @endif
                        </td>
                        <td class="text-right fw-bold {{ $soldeCumul > 0 ? 'text-danger' : ($soldeCumul < 0 ? 'text-success' : 'text-info') }}">
                            {{ number_format(abs($soldeCumul), 0, ',', ' ') }}
                        </td>
                        <td class="text-center">
                            <span class="nature-badge {{ $soldeCumul > 0 ? 'nature-debit' : ($soldeCumul < 0 ? 'nature-credit' : 'nature-equilibre') }}">
                                {{ $natureSolde }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @php
                    $totalDebitCompte = collect($compteData['lignes'])->sum('debit');
                    $totalCreditCompte = collect($compteData['lignes'])->sum('credit');
                    $soldeFinal = $compteData['solde_final'];
                    $natureFinal = $soldeFinal > 0 ? 'Débiteur' : ($soldeFinal < 0 ? 'Créditeur' : 'Équilibré');
                @endphp
                <tfoot class="table-footer">
                    <tr class="totaux-row">
                        <td colspan="3" class="text-right fw-bold">TOTAUX DU COMPTE :</td>
                        <td class="text-right text-success fw-bold">{{ number_format($totalDebitCompte, 0, ',', ' ') }}</td>
                        <td class="text-right text-danger fw-bold">{{ number_format($totalCreditCompte, 0, ',', ' ') }}</td>
                        <td class="text-right fw-bold {{ $soldeFinal > 0 ? 'text-danger' : ($soldeFinal < 0 ? 'text-success' : 'text-info') }}">
                            {{ number_format(abs($soldeFinal), 0, ',', ' ') }}
                        </td>
                        <td class="text-center">
                            <span class="nature-badge {{ $soldeFinal > 0 ? 'nature-debit' : ($soldeFinal < 0 ? 'nature-credit' : 'nature-equilibre') }}">
                                {{ $natureFinal }}
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
            <i class="fas fa-database fa-3x text-muted mb-3"></i>
            <h5>Aucune écriture comptable trouvée</h5>
            <p class="text-muted">Aucune écriture validée n'a été enregistrée pour le moment.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection

@push('styles')
<style>
    .fade-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .filters-card { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 25px; }
    .filters-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 12px 20px; display: flex; align-items: center; gap: 12px; }
    .filters-icon { width: 35px; height: 35px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
    .filters-title { font-size: 14px; font-weight: 600; margin: 0; color: white; }
    .filters-subtitle { font-size: 11px; margin: 2px 0 0; color: rgba(255,255,255,0.8); }
    .filters-body { padding: 20px; }

    /* ===== STYLES DES BOUTONS ===== */
    .btn-filter {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }
    .btn-filter:active {
        transform: translateY(1px);
    }

    .btn-pdf {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-pdf:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    .btn-pdf:active {
        transform: translateY(1px);
    }

    /* Champs de formulaire */
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
    }
    .form-control-custom:focus {
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }

    /* Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
    .stat-card { background: white; border-radius: 16px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; gap: 15px; align-items: center; }
    .stat-debit .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-credit .stat-icon { background: #fee2e2; color: #ef4444; }
    .stat-comptes .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-equilibre .stat-icon { background: #fef3c7; color: #f59e0b; }
    .stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; }
    .stat-icon i { font-size: 22px; }
    .stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
    .stat-value { font-size: 20px; font-weight: 800; color: #1e293b; }

    /* Cards */
    .data-card { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 20px; }
    .card-header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 15px 20px; display: flex; align-items: center; gap: 12px; }
    .card-header-icon { width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; }
    .card-header-title { font-size: 15px; font-weight: 700; margin: 0; color: white; }
    .card-header-subtitle { font-size: 11px; margin: 3px 0 0; color: #94a3b8; }

    /* Tableau */
    .table-grand-livre { width: 100%; border-collapse: collapse; font-size: 12px; }
    .table-grand-livre thead th { background: #f8fafc; padding: 12px 10px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #e2e8f0; }
    .table-grand-livre tbody td { padding: 10px; border-bottom: 1px solid #f1f5f9; }
    .table-footer td { padding: 12px 10px; background: #f8fafc; font-weight: 700; }

    /* Alignements */
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .text-success { color: #059669; }
    .text-danger { color: #dc2626; }
    .text-info { color: #3b82f6; }
    .fw-bold { font-weight: 700; }

    /* Badges */
    .nature-badge { padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
    .nature-debit { background: #fee2e2; color: #dc2626; }
    .nature-credit { background: #d1fae5; color: #059669; }
    .nature-equilibre { background: #dbeafe; color: #3b82f6; }

    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-debit { background: #fee2e2; color: #dc2626; }
    .badge-credit { background: #d1fae5; color: #059669; }
    .badge-equilibre { background: #dbeafe; color: #3b82f6; }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .table-grand-livre thead { display: none; }
        .table-grand-livre tbody tr { display: block; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: white; }
        .table-grand-livre tbody td { display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        .table-grand-livre tbody td:last-child { border-bottom: none; }
        .table-grand-livre tbody td:before { content: attr(data-label); font-weight: 700; font-size: 10px; color: #64748b; width: 35%; text-transform: uppercase; }
        .table-footer td { display: flex; justify-content: space-between; align-items: center; }
    }
</style>
@endpush

@push('scripts')
<script>
    // Calcul des totaux pour les cartes statistiques
    let totalDebit = 0, totalCredit = 0, nbComptes = 0;
    document.querySelectorAll('.data-card').forEach(card => {
        let debitCompte = 0, creditCompte = 0;
        card.querySelectorAll('tbody tr').forEach(row => {
            let debit = parseFloat(row.cells[3]?.innerText.replace(/[^\d]/g, '') || 0);
            let credit = parseFloat(row.cells[4]?.innerText.replace(/[^\d]/g, '') || 0);
            debitCompte += debit;
            creditCompte += credit;
        });
        if(debitCompte > 0 || creditCompte > 0) {
            totalDebit += debitCompte;
            totalCredit += creditCompte;
            nbComptes++;
        }
    });
    document.getElementById('totalDebitCard').innerHTML = totalDebit.toLocaleString() + ' FC';
    document.getElementById('totalCreditCard').innerHTML = totalCredit.toLocaleString() + ' FC';
    document.getElementById('nbComptes').innerHTML = nbComptes;
    let balance = totalDebit - totalCredit;
    let balanceEl = document.getElementById('balanceGenerale');
    if(balance === 0) balanceEl.innerHTML = '✓ Équilibré';
    else balanceEl.innerHTML = Math.abs(balance).toLocaleString() + ' FC';

    // Fonction FILTRER
    function filtrer() {
        let compte = document.getElementById('compte').value;
        let dateDebut = document.getElementById('date_debut').value;
        let dateFin = document.getElementById('date_fin').value;
        let url = '{{ route("directeur.grand-livre") }}';
        let params = [];
        if(compte) params.push('compte=' + compte);
        if(dateDebut) params.push('date_debut=' + dateDebut);
        if(dateFin) params.push('date_fin=' + dateFin);
        if(params.length) url += '?' + params.join('&');
        window.location.href = url;
    }

    // Fonction EXPORT PDF
    function exporterPDF() {
        let compte = document.getElementById('compte').value;
        let dateDebut = document.getElementById('date_debut').value;
        let dateFin = document.getElementById('date_fin').value;
        
        let url = '/directeur/export/grand-livre/pdf';
        let params = [];
        if(compte) params.push('compte=' + compte);
        if(dateDebut) params.push('date_debut=' + dateDebut);
        if(dateFin) params.push('date_fin=' + dateFin);
        if(params.length) url += '?' + params.join('&');
        
        window.open(url, '_blank');
    }
</script>
@endpush