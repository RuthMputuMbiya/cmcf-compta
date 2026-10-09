@extends('layouts.directeur')

@section('title', 'Journal comptable')
@section('header-title', '📋 Journal comptable')
@section('header-subtitle', 'Historique chronologique des opérations')

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
                    <label class="form-label"><i class="fas fa-calendar-alt me-1 text-primary"></i>Date début</label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-calendar-alt me-1 text-primary"></i>Date fin</label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-book me-1 text-primary"></i>Compte</label>
                    <select id="compte" class="form-control-custom">
                        <option value="">Tous les comptes</option>
                        @foreach($comptes ?? [] as $compte)
                        <option value="{{ $compte->numero }}" {{ request('compte') == $compte->numero ? 'selected' : '' }}>
                            {{ $compte->numero }} - {{ $compte->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button onclick="filtrer()" class="btn-filter w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
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

    <!-- Tableau principal style journal comptable -->
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon"><i class="fas fa-list-alt"></i></div>
            <div>
                <h3 class="card-header-title">JOURNAL COMPTABLE</h3>
                <p class="card-header-subtitle">Liste détaillée des opérations</p>
            </div>
            <div class="ms-auto">
                <button onclick="exporterPDF()" class="btn-export">
                    <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                </button>
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
                        <th class="col-intitule">INTITULÉ DÉBIT</th>
                        <th class="col-compte">COMPTE CRÉDIT</th>
                        <th class="col-intitule">INTITULÉ CRÉDIT</th>
                        <th class="col-libelle">LIBELLÉ</th>
                        <th class="col-montant">DÉBIT (FC)</th>
                        <th class="col-montant">CRÉDIT (FC)</th>
                    </tr>
                </thead>
                <tbody id="journalBody">
                    @php
                        $totalDebitGeneral = 0;
                        $totalCreditGeneral = 0;
                        $currentNumero = 1;
                        $groupedEntries = [];

                        foreach($journal as $ligne) {
                            $dateStr = $ligne->date ? \Carbon\Carbon::parse($ligne->date)->format('d/m/Y') : '-';
                            $pieceRef = $ligne->reference ?? '-';
                            $key = $dateStr . '_' . $pieceRef;

                            if(!isset($groupedEntries[$key])) {
                                $groupedEntries[$key] = ['date' => $dateStr, 'piece' => $pieceRef, 'lignes' => []];
                            }

                            $debit = $ligne->debit ?? 0;
                            $credit = $ligne->credit ?? 0;

                            $nomCompte = '';
                            if(isset($ligne->compte) && !empty($ligne->compte) && isset($comptes)) {
                                $found = $comptes->firstWhere('numero', $ligne->compte);
                                if($found) $nomCompte = $found->nom;
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
                        }
                    @endphp

                    @forelse($groupedEntries as $entry)
                        @php
                            $debitLines = [];
                            $creditLines = [];
                            $libellePrincipal = '';
                            $debitIntitule = '';
                            $creditIntitule = '';

                            foreach($entry['lignes'] as $idx => $ligne) {
                                if($ligne['debit'] > 0) {
                                    $debitLines[] = ['compte' => $ligne['compte'], 'montant' => $ligne['debit']];
                                    if(empty($debitIntitule) && !empty($ligne['nom_compte'])) {
                                        $debitIntitule = $ligne['nom_compte'];
                                    }
                                }
                                if($ligne['credit'] > 0) {
                                    $creditLines[] = ['compte' => $ligne['compte'], 'montant' => $ligne['credit']];
                                    if(empty($creditIntitule) && !empty($ligne['nom_compte'])) {
                                        $creditIntitule = $ligne['nom_compte'];
                                    }
                                }
                                if($idx == 0 && !empty($ligne['libelle']) && $ligne['libelle'] != '-')
                                    $libellePrincipal = $ligne['libelle'];
                            }

                            $totalLignesEcriture = max(count($debitLines), count($creditLines));
                            if($totalLignesEcriture == 0) $totalLignesEcriture = 1;
                            $numeroActuel = $currentNumero++;
                        @endphp

                        @for($i = 0; $i < $totalLignesEcriture; $i++)
                        <tr>
                            @if($i == 0)
                                <td class="text-center" rowspan="{{ $totalLignesEcriture }}">
                                    <span class="numero-badge">{{ $numeroActuel }}</span>
                                </td>
                                <td class="text-center" rowspan="{{ $totalLignesEcriture }}">
                                    <span class="date-badge">{{ $entry['date'] }}</span>
                                </td>
                                <td class="text-center" rowspan="{{ $totalLignesEcriture }}">
                                    <span class="piece-number">{{ $entry['piece'] }}</span>
                                </td>
                            @endif

                            <!-- COMPTE DÉBIT -->
                            <td class="text-center">
                                @if(isset($debitLines[$i]))
                                    <span class="compte-code-debit">{{ $debitLines[$i]['compte'] }}</span>
                                @else
                                    <span class="empty-cell">—</span>
                                @endif
                            </td>

                            <!-- INTITULÉ DÉBIT -->
                            <td class="text-left">
                                @if($i == 0 && !empty($debitIntitule))
                                    <span class="intitule-debit">{{ $debitIntitule }}</span>
                                @elseif($i == 0)
                                    <span class="empty-cell">—</span>
                                @endif
                            </td>

                            <!-- COMPTE CRÉDIT -->
                            <td class="text-center">
                                @if(isset($creditLines[$i]))
                                    <span class="compte-code-credit">{{ $creditLines[$i]['compte'] }}</span>
                                @else
                                    <span class="empty-cell">—</span>
                                @endif
                            </td>

                            <!-- INTITULÉ CRÉDIT -->
                            <td class="text-left">
                                @if($i == 0 && !empty($creditIntitule))
                                    <span class="intitule-credit">{{ $creditIntitule }}</span>
                                @elseif($i == 0)
                                    <span class="empty-cell">—</span>
                                @endif
                            </td>

                            <!-- LIBELLÉ -->
                            @if($i == 0)
                                <td rowspan="{{ $totalLignesEcriture }}" class="text-left">
                                    @if(!empty($libellePrincipal))
                                        <span class="libelle-texte">{{ $libellePrincipal }}</span>
                                    @else
                                        <span class="empty-cell">—</span>
                                    @endif
                                </td>
                            @endif

                            <!-- MONTANT DÉBIT -->
                            <td class="text-right">
                                @if(isset($debitLines[$i]))
                                    <span class="debit-montant">{{ number_format($debitLines[$i]['montant'], 0, ',', ' ') }}</span>
                                @else
                                    <span class="empty-cell">—</span>
                                @endif
                            </td>

                            <!-- MONTANT CRÉDIT -->
                            <td class="text-right">
                                @if(isset($creditLines[$i]))
                                    <span class="credit-montant">{{ number_format($creditLines[$i]['montant'], 0, ',', ' ') }}</span>
                                @else
                                    <span class="empty-cell">—</span>
                                @endif
                            </td>
                        </tr>
                        @endfor
                    @empty
                    <tr class="empty-row">
                        <td colspan="10" class="text-center" style="padding:30px;">Aucune donnée trouvée</span>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="8" class="text-right">TOTAUX GÉNÉRAUX</span>
                        <td class="total-debit">{{ number_format($totalDebitGeneral, 0, ',', ' ') }}</span>
                        <td class="total-credit">{{ number_format($totalCreditGeneral, 0, ',', ' ') }}</span>
                    </tr>
                    <tr class="equilibre-row">
                        <td colspan="8" class="text-right">ÉQUILIBRE</span>
                        <td colspan="2" class="text-center">
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

    /* Filtres Card */
    .filters-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .filters-header {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
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
        border-color: #8b5cf6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }
    .btn-filter, .btn-reset {
        padding: 10px 16px;
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

    /* Stats Grid */
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
    .stat-debit .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-credit .stat-icon { background: #fee2e2; color: #ef4444; }
    .stat-lignes .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-equilibre .stat-icon { background: #fef3c7; color: #f59e0b; }
    .stat-icon i { font-size: 20px; }
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

    /* Data Card */
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .card-header-custom {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .card-header-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .card-header-title {
        font-size: 14px;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    .card-header-subtitle {
        font-size: 11px;
        margin: 2px 0 0;
        color: rgba(255,255,255,0.8);
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

    /* Tableau */
    .journal-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .table-header-main th {
        background: #1e293b;
        color: white;
        padding: 10px 8px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        text-align: center;
        border: 1px solid #334155;
    }
    .journal-table td {
        padding: 10px 8px;
        border: 1px solid #e2e8f0;
        vertical-align: top;
    }
    .journal-table tbody tr:hover {
        background: #f8fafc;
    }

    /* Badges */
    .numero-badge {
        font-weight: 700;
        background: #e2e8f0;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
        font-size: 12px;
    }
    .date-badge {
        font-family: monospace;
        font-weight: 600;
        background: #e2e8f0;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        font-size: 11px;
    }
    .piece-number {
        font-family: monospace;
        font-weight: 700;
        color: #4f46e5;
        background: #eef2ff;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
        font-size: 11px;
    }
    .compte-code-debit {
        font-weight: 800;
        color: #059669;
        background: #d1fae5;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
        font-size: 12px;
    }
    .compte-code-credit {
        font-weight: 800;
        color: #dc2626;
        background: #fee2e2;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
        font-size: 12px;
    }
    .intitule-debit {
        font-size: 11px;
        color: #059669;
        font-weight: 500;
    }
    .intitule-credit {
        font-size: 11px;
        color: #dc2626;
        font-weight: 500;
    }
    .libelle-texte {
        font-size: 11px;
        color: #475569;
        font-style: italic;
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
    .empty-cell {
        color: #cbd5e1;
        font-style: italic;
    }

    /* Totaux */
    .total-row {
        background: #f8fafc;
        font-weight: 800;
    }
    .total-debit {
        text-align: right;
        color: #059669;
        background: #d1fae5;
    }
    .total-credit {
        text-align: right;
        color: #dc2626;
        background: #fee2e2;
    }
    .equilibre-row {
        background: #fef3c7;
    }
    .equilibre-ok {
        color: #059669;
    }
    .equilibre-ko {
        color: #dc2626;
    }

    /* Pagination */
    .pagination-container {
        padding: 12px 20px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    /* Empty state */
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

    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .journal-table thead {
            display: none;
        }
        .journal-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
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
            font-weight: 600;
            font-size: 10px;
            color: #64748b;
            width: 35%;
            text-transform: uppercase;
        }
        .card-header-custom {
            flex-direction: column;
            text-align: center;
        }
        .ms-auto {
            margin-left: 0 !important;
            margin-top: 10px;
        }
        .filters-header {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function filtrer() {
        let url = '{{ url("/directeur/journal") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('compte').value) url += 'compte=' + document.getElementById('compte').value;
        window.location.href = url;
    }
    
    function resetFilters() {
        window.location.href = '{{ url("/directeur/journal") }}';
    }
    
    function exporterPDF() {
        let url = '{{ url("/directeur/export/journal/pdf") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('compte').value) url += 'compte=' + document.getElementById('compte').value;
        window.open(url, '_blank');
    }

    // Ajouter les labels pour le responsive
    const labels = ['N°', 'DATE', 'N° PIÈCE', 'COMPTE DÉBIT', 'INTITULÉ DÉBIT', 'COMPTE CRÉDIT', 'INTITULÉ CRÉDIT', 'LIBELLÉ', 'DÉBIT', 'CRÉDIT'];
    document.querySelectorAll('#journalBody tr').forEach(row => {
        if (!row.classList.contains('empty-row')) {
            row.querySelectorAll('td').forEach((cell, idx) => {
                if (idx < labels.length) {
                    cell.setAttribute('data-label', labels[idx]);
                }
            });
        }
    });
</script>
@endpush
@endsection