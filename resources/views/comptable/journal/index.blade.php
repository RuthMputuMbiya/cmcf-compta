@extends('layouts.comptable')

@section('title', 'Journal comptable')
@section('header-title', '📋 JOURNAL COMPTABLE')
@section('header-subtitle', 'Historique chronologique des opérations')

@section('comptable-content')
<div class="fade-up">
    
    <!-- En-tête -->
    <div class="welcome-banner mb-4">
        <div class="welcome-content">
            <div class="welcome-text">
                <h2><i class="fas fa-book me-2"></i>JOURNAL COMPTABLE</h2>
                <p>Consultez l'historique de toutes les opérations comptables</p>
            </div>
            <div class="welcome-date">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ now()->translatedFormat('l d F Y') }}</span>
            </div>
        </div>
    </div>

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
                        <i class="fas fa-calendar-alt me-1"></i>Date début
                    </label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt me-1"></i>Date fin
                    </label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-hashtag me-1"></i>N° Pièce
                    </label>
                    <input type="text" id="reference" class="form-control-custom" placeholder="Ex: PC-20250425-0001" value="{{ request('reference') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-book me-1"></i>Compte
                    </label>
                    <select id="compte" class="form-control-custom">
                        <option value="">Tous les comptes</option>
                        @foreach($comptes ?? [] as $compte)
                        <option value="{{ $compte->numero }}" {{ request('compte') == $compte->numero ? 'selected' : '' }}>
                            {{ $compte->numero }} - {{ $compte->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <button onclick="applyFilters()" class="btn-filter">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                    <button onclick="resetFilters()" class="btn-reset">
                        <i class="fas fa-undo-alt me-2"></i>Réinitialiser
                    </button>
                    <button onclick="exporterPDF()" class="btn-pdf">
                        <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                    </button>
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-debit">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Débit</div>
                <div class="stat-value" id="totalDebit">0 FC</div>
            </div>
        </div>
        <div class="stat-card stat-credit">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total Crédit</div>
                <div class="stat-value" id="totalCredit">0 FC</div>
            </div>
        </div>
        <div class="stat-card stat-lignes">
            <div class="stat-icon"><i class="fas fa-list"></i></div>
            <div class="stat-info">
                <div class="stat-label">Nombre lignes</div>
                <div class="stat-value" id="totalLignes">0</div>
            </div>
        </div>
        <div class="stat-card stat-equilibre">
            <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-info">
                <div class="stat-label">Équilibre</div>
                <div class="stat-value" id="equilibre">0 FC</div>
            </div>
        </div>
    </div>

    <!-- Tableau Journal style comptable -->
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            <div>
                <h3 class="card-header-title">JOURNAL COMPTABLE</h3>
                <p class="card-header-subtitle">Liste détaillée des opérations</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="journal-table">
                <thead>
                    <tr class="table-header-main">
                        <th class="col-numero">N°</th>
                        <th class="col-date">DATE</th>
                        <th class="col-piece">N° PIÈCE</th>
                        <th class="col-compte">COMPTE DÉBIT</th>
                        <th class="col-compte">COMPTE CRÉDIT</th>
                        <th class="col-libelle">LIBELLÉ</th>
                        <th class="col-montant">DÉBIT (FC)</th>
                        <th class="col-montant">CRÉDIT (FC)</th>
                    </tr>
                </thead>
                <tbody id="journalBody">
                    @php
                        $totalDebitGeneral = 0;
                        $totalCreditGeneral = 0;
                        $lignesCount = 0;
                        $currentNumero = 1;
                        $groupedEntries = [];
                        
                        foreach($journal as $ligne) {
                            $dateStr = $ligne->date ? \Carbon\Carbon::parse($ligne->date)->format('d/m/Y') : '-';
                            $pieceRef = $ligne->reference ?? '-';
                            $key = $dateStr . '_' . $pieceRef;
                            
                            if(!isset($groupedEntries[$key])) {
                                $groupedEntries[$key] = [
                                    'date' => $dateStr,
                                    'piece' => $pieceRef,
                                    'lignes' => []
                                ];
                            }
                            
                            $debit = $ligne->debit ?? 0;
                            $credit = $ligne->credit ?? 0;
                            
                            // Récupérer le nom du compte
                            $nomCompte = '';
                            if(isset($ligne->compte) && !empty($ligne->compte)) {
                                if(isset($comptes)) {
                                    $found = $comptes->firstWhere('numero', $ligne->compte);
                                    if($found) {
                                        $nomCompte = $found->nom;
                                    }
                                }
                            }
                            
                            $groupedEntries[$key]['lignes'][] = [
                                'compte' => $ligne->compte,
                                'nom_compte' => $nomCompte,
                                'debit' => $debit,
                                'credit' => $credit,
                                'libelle' => $ligne->libelle
                            ];
                            
                            $totalDebitGeneral += $debit;
                            $totalCreditGeneral += $credit;
                            $lignesCount++;
                        }
                    @endphp
                    
                    @forelse($groupedEntries as $index => $entry)
                        @php
                            $debitLines = [];
                            $creditLines = [];
                            $libellePrincipal = '';
                            $debitIntitules = [];
                            $creditIntitules = [];
                            
                            foreach($entry['lignes'] as $idx => $ligne) {
                                if($ligne['debit'] > 0) {
                                    $debitLines[] = [
                                        'compte' => $ligne['compte'],
                                        'montant' => $ligne['debit']
                                    ];
                                    if(!empty($ligne['nom_compte']) && !in_array($ligne['nom_compte'], $debitIntitules)) {
                                        $debitIntitules[] = $ligne['nom_compte'];
                                    }
                                }
                                if($ligne['credit'] > 0) {
                                    $creditLines[] = [
                                        'compte' => $ligne['compte'],
                                        'montant' => $ligne['credit']
                                    ];
                                    if(!empty($ligne['nom_compte']) && !in_array($ligne['nom_compte'], $creditIntitules)) {
                                        $creditIntitules[] = $ligne['nom_compte'];
                                    }
                                }
                                if($idx == 0 && !empty($ligne['libelle']) && $ligne['libelle'] != '-') {
                                    $libellePrincipal = $ligne['libelle'];
                                }
                            }
                            
                            $totalLignesEcriture = max(count($debitLines), count($creditLines));
                            if($totalLignesEcriture == 0) $totalLignesEcriture = 1;
                            $numeroActuel = $currentNumero++;
                        @endphp
                        
                        @for($i = 0; $i < $totalLignesEcriture; $i++)
                        <tr class="journal-row">
                            @if($i == 0)
                            <td class="numero-cell" rowspan="{{ $totalLignesEcriture }}">
                                <span class="numero-badge">{{ $numeroActuel }}</span>
                            </td>
                            <td class="date-cell" rowspan="{{ $totalLignesEcriture }}">
                                <span class="date-badge">{{ $entry['date'] }}</span>
                            </td>
                            <td class="piece-cell" rowspan="{{ $totalLignesEcriture }}">
                                <span class="piece-number">{{ $entry['piece'] }}</span>
                            </td>
                            @endif
                            
                            <!-- CPTC DÉBIT -->
                            <td class="compte-cell">
                                @if(isset($debitLines[$i]))
                                    <div class="compte-wrapper">
                                        <span class="compte-code-debit">{{ $debitLines[$i]['compte'] }}</span>
                                    </div>
                                @else
                                    <span class="compte-empty">—</span>
                                @endif
                            </td>
                            
                            <!-- CPTC CRÉDIT -->
                            <td class="compte-cell">
                                @if(isset($creditLines[$i]))
                                    <div class="compte-wrapper">
                                        <span class="compte-code-credit">{{ $creditLines[$i]['compte'] }}</span>
                                    </div>
                                @else
                                    <span class="compte-empty">—</span>
                                @endif
                            </td>
                            
                            <!-- LIBELLÉ -->
                            @if($i == 0)
                            <td class="libelle-cell" rowspan="{{ $totalLignesEcriture }}">
                                <div class="libelle-content">
                                    <!-- Intitulés des comptes en haut -->
                                    <div class="libelle-intitules">
                                        @if(count($debitIntitules) > 0)
                                            <div class="intitule-debit">
                                                @foreach($debitIntitules as $intitule)
                                                    <span class="badge-debit">{{ $intitule }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(count($creditIntitules) > 0)
                                            <div class="intitule-credit">
                                                @foreach($creditIntitules as $intitule)
                                                    <span class="badge-credit">{{ $intitule }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Libellé principal en bas à gauche -->
                                    @if(!empty($libellePrincipal))
                                    <div class="libelle-principal">
                                        <span class="libelle-texte">{{ $libellePrincipal }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            @endif
                            
                            <!-- DÉBIT -->
                            <td class="montant-cell debit-cell">
                                @if(isset($debitLines[$i]))
                                    <span class="debit-montant">{{ number_format($debitLines[$i]['montant'], 0, ',', ' ') }}</span>
                                @else
                                    <span class="montant-vide">—</span>
                                @endif
                            </td>
                            
                            <!-- CRÉDIT -->
                            <td class="montant-cell credit-cell">
                                @if(isset($creditLines[$i]))
                                    <span class="credit-montant">{{ number_format($creditLines[$i]['montant'], 0, ',', ' ') }}</span>
                                @else
                                    <span class="montant-vide">—</span>
                                @endif
                            </td>
                        </tr>
                        @endfor
                    @empty
                    <tr class="empty-row">
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-book-open"></i>
                                <h4>Aucune écriture trouvée</h4>
                                <p>Aucune ligne de journal ne correspond à vos critères</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="6" class="total-label">TOTAUX GÉNÉRAUX</span>
                        <td class="total-debit">{{ number_format($totalDebitGeneral, 0, ',', ' ') }}</span>
                        <td class="total-credit">{{ number_format($totalCreditGeneral, 0, ',', ' ') }}</span>
                    </tr>
                    <tr class="equilibre-row">
                        <td colspan="6" class="total-label">ÉQUILIBRE</span>
                        <td colspan="2" class="equilibre-value">
                            @if($totalDebitGeneral == $totalCreditGeneral)
                                <span class="equilibre-ok">✓ ÉQUILIBRÉ</span>
                            @else
                                <span class="equilibre-ko">✗ NON ÉQUILIBRÉ (Diff: {{ number_format(abs($totalDebitGeneral - $totalCreditGeneral), 0, ',', ' ') }} FC)</span>
                            @endif
                         </span
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="pagination-container">
            {{ $journal->links() }}
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
    
    .welcome-banner {
        background: linear-gradient(135deg, #0f2b3d, #1a4a6f);
        border-radius: 16px;
        padding: 20px 25px;
    }
    .welcome-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .welcome-text h2 {
        color: white;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        letter-spacing: 1px;
    }
    .welcome-text p {
        color: #a0c4e8;
        font-size: 13px;
        margin: 5px 0 0;
    }
    .welcome-date {
        background: rgba(255,255,255,0.1);
        padding: 8px 18px;
        border-radius: 30px;
        color: white;
        font-size: 13px;
    }
    
    .filters-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .filters-header {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .filters-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.15);
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
        color: rgba(255,255,255,0.7);
    }
    .filters-body {
        padding: 20px;
    }
    .form-label {
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }
    .btn-filter {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
    }
    .btn-reset {
        background: #64748b;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-reset:hover {
        background: #475569;
        transform: translateY(-2px);
    }
    .btn-pdf {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-pdf:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220,38,38,0.3);
    }
    .btn-print {
        background: linear-gradient(135deg, #475569, #334155);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-print:hover {
        transform: translateY(-2px);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        transition: all 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-debit .stat-icon { background: #d1fae5; color: #059669; }
    .stat-credit .stat-icon { background: #fee2e2; color: #dc2626; }
    .stat-lignes .stat-icon { background: #e0e7ff; color: #4f46e5; }
    .stat-equilibre .stat-icon { background: #fef3c7; color: #d97706; }
    .stat-icon i { font-size: 22px; }
    .stat-info {
        flex: 1;
    }
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
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
        font-size: 18px;
        color: white;
    }
    .card-header-title {
        font-size: 15px;
        font-weight: 700;
        margin: 0;
        color: white;
        letter-spacing: 1px;
    }
    .card-header-subtitle {
        font-size: 11px;
        margin: 3px 0 0;
        color: #94a3b8;
    }
    
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-top: 20px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .journal-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Segoe UI', sans-serif;
        font-size: 12px;
    }
    
    .table-header-main th {
        background: #1e293b;
        color: white;
        padding: 12px 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 10px;
        text-align: center;
        border: 1px solid #334155;
    }
    
    .col-numero { width: 50px; text-align: center; }
    .col-date { width: 85px; text-align: center; }
    .col-piece { width: 120px; text-align: center; }
    .col-compte { width: 100px; text-align: center; }
    .col-libelle { width: 320px; text-align: left; }
    .col-montant { width: 110px; text-align: right; }
    
    .journal-table tbody td {
        padding: 10px 8px;
        border: 1px solid #e2e8f0;
        vertical-align: top;
        background: white;
    }
    .journal-table tbody tr:hover td {
        background: #fefce8;
    }
    
    .numero-cell { text-align: center; background: #f8fafc; vertical-align: middle; }
    .numero-badge {
        font-weight: 700;
        color: #1e293b;
        background: #e2e8f0;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        display: inline-block;
    }
    .date-cell { text-align: center; background: #f8fafc; vertical-align: middle; }
    .date-badge {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #1e293b;
        background: #e2e8f0;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        display: inline-block;
    }
    .piece-cell { text-align: center; vertical-align: middle; }
    .piece-number {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #4f46e5;
        background: #eef2ff;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        display: inline-block;
    }
    
    .compte-cell {
        text-align: center;
        vertical-align: middle;
    }
    .compte-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .compte-code-debit {
        font-weight: 800;
        color: #059669;
        background: #d1fae5;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 13px;
        display: inline-block;
    }
    .compte-code-credit {
        font-weight: 800;
        color: #dc2626;
        background: #fee2e2;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 13px;
        display: inline-block;
    }
    .compte-empty {
        color: #cbd5e1;
        font-style: italic;
    }
    
    /* LIBELLÉ - Intitulés en haut, Libellé en bas à gauche */
    .libelle-cell {
        vertical-align: top;
    }
    .libelle-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .libelle-intitules {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 5px;
    }
    .intitule-debit {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .intitule-credit {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .badge-debit {
        font-size: 10px;
        color: #059669;
        background: #d1fae5;
        padding: 3px 10px;
        border-radius: 15px;
        display: inline-block;
        font-weight: 600;
    }
    .badge-credit {
        font-size: 10px;
        color: #dc2626;
        background: #fee2e2;
        padding: 3px 10px;
        border-radius: 15px;
        display: inline-block;
        font-weight: 600;
    }
    .libelle-principal {
        text-align: left;
        margin-top: 5px;
        border-top: 1px dashed #e2e8f0;
        padding-top: 8px;
    }
    .libelle-texte {
        font-size: 11px;
        color: #475569;
        font-style: italic;
        font-weight: 500;
    }
    
    .montant-cell {
        text-align: right;
        font-family: 'Courier New', monospace;
        vertical-align: middle;
    }
    .debit-montant {
        color: #059669;
        font-weight: 700;
        font-size: 12px;
    }
    .credit-montant {
        color: #dc2626;
        font-weight: 700;
        font-size: 12px;
    }
    .montant-vide {
        color: #cbd5e1;
    }
    
    .total-row {
        background: #f8fafc;
    }
    .total-row td {
        padding: 10px 12px;
        font-weight: 800;
        font-size: 13px;
        border: 1px solid #e2e8f0;
    }
    .total-label {
        text-align: right;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
    }
    .total-debit {
        text-align: right;
        color: #059669;
        font-weight: 800;
        background: #d1fae5;
    }
    .total-credit {
        text-align: right;
        color: #dc2626;
        font-weight: 800;
        background: #fee2e2;
    }
    .equilibre-row {
        background: #fef3c7;
    }
    .equilibre-row td {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
    }
    .equilibre-value {
        text-align: center;
        font-weight: 800;
    }
    .equilibre-ok {
        color: #059669;
    }
    .equilibre-ko {
        color: #dc2626;
    }
    
    .empty-row td {
        padding: 50px !important;
        text-align: center;
    }
    .empty-state {
        text-align: center;
        padding: 30px;
    }
    .empty-state i {
        font-size: 50px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }
    .empty-state h4 {
        font-size: 16px;
        color: #64748b;
        margin: 0 0 5px;
    }
    .empty-state p {
        font-size: 12px;
        color: #94a3b8;
        margin: 0;
    }
    
    .pagination-container {
        padding: 15px 20px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    
    @media (max-width: 992px) {
        .journal-table thead {
            display: none;
        }
        .journal-table tbody tr {
            display: block;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            overflow: hidden;
        }
        .journal-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            border: none;
            border-bottom: 1px solid #f1f5f9;
        }
        .journal-table tbody td:last-child {
            border-bottom: none;
        }
        .journal-table tbody td:before {
            content: attr(data-label);
            font-weight: 700;
            font-size: 10px;
            color: #64748b;
            width: 35%;
            text-transform: uppercase;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .welcome-content {
            flex-direction: column;
            text-align: center;
        }
        .card-header-custom {
            flex-direction: column;
            text-align: center;
        }
        .btn-filter, .btn-reset, .btn-pdf, .btn-print {
            width: 100%;
            justify-content: center;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media print {
        .filters-card, .stats-grid, .btn-pdf, .btn-filter, .btn-reset, .btn-print, .pagination-container {
            display: none;
        }
        .journal-table thead th {
            background: #e2e8f0;
            color: black;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .debit-montant, .credit-montant {
            color: black !important;
        }
        .data-card {
            box-shadow: none;
            border: 1px solid #ccc;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        let url = window.location.pathname + '?';
        const date_debut = document.getElementById('date_debut')?.value;
        const date_fin = document.getElementById('date_fin')?.value;
        const reference = document.getElementById('reference')?.value;
        const compte = document.getElementById('compte')?.value;
        
        if(date_debut) url += 'date_debut=' + date_debut + '&';
        if(date_fin) url += 'date_fin=' + date_fin + '&';
        if(reference) url += 'reference=' + reference + '&';
        if(compte) url += 'compte=' + compte;
        
        window.location.href = url.slice(0, -1);
    }
    
    function resetFilters() {
        window.location.href = window.location.pathname;
    }
    
    function exporterPDF() {
        let url = '{{ url("/comptable/export/journal/pdf") }}?';
        const date_debut = document.getElementById('date_debut')?.value;
        const date_fin = document.getElementById('date_fin')?.value;
        const reference = document.getElementById('reference')?.value;
        const compte = document.getElementById('compte')?.value;
        
        if(date_debut) url += 'date_debut=' + date_debut + '&';
        if(date_fin) url += 'date_fin=' + date_fin + '&';
        if(reference) url += 'reference=' + reference + '&';
        if(compte) url += 'compte=' + compte;
        
        window.open(url, '_blank');
    }
    
    function mettreAJourStats() {
        let totalDebit = {{ $totalDebitGeneral ?? 0 }};
        let totalCredit = {{ $totalCreditGeneral ?? 0 }};
        let lignesCount = {{ $lignesCount ?? 0 }};
        
        document.getElementById('totalDebit').innerHTML = totalDebit.toLocaleString() + ' FC';
        document.getElementById('totalCredit').innerHTML = totalCredit.toLocaleString() + ' FC';
        document.getElementById('totalLignes').innerHTML = lignesCount;
        
        let equilibre = totalDebit - totalCredit;
        let equilibreEl = document.getElementById('equilibre');
        if (equilibre === 0) {
            equilibreEl.innerHTML = '✓ Équilibré';
            equilibreEl.style.color = '#059669';
        } else {
            equilibreEl.innerHTML = Math.abs(equilibre).toLocaleString() + ' FC';
            equilibreEl.style.color = '#dc2626';
        }
    }
    
    function ajouterLabels() {
        const labels = ['N°', 'DATE', 'N° PIÈCE', 'COMPTE DÉBIT', 'COMPTE CRÉDIT', 'LIBELLÉ', 'DÉBIT', 'CRÉDIT'];
        document.querySelectorAll('#journalBody tr').forEach(row => {
            if (!row.classList.contains('empty-row')) {
                row.querySelectorAll('td').forEach((cell, idx) => {
                    if (idx < labels.length) {
                        cell.setAttribute('data-label', labels[idx]);
                    }
                });
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        mettreAJourStats();
        ajouterLabels();
    });
</script>
@endpush
@endsection