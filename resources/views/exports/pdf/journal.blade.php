<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Journal comptable - CMCF Compta Pro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Inter', Arial, sans-serif;
            background: white;
            padding: 20px;
            color: #1e293b;
        }

        /* ========== EN-TÊTE ========== */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }

        .header h1 {
            color: #1e293b;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .header h1 span {
            color: #3b82f6;
        }

        .header p {
            color: #64748b;
            font-size: 11px;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .entreprise-badge {
            display: inline-block;
            background: #f1f5f9;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9px;
            color: #64748b;
            margin-top: 8px;
        }

        /* ========== INFORMATIONS ========== */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
        }

        .info-item {
            text-align: center;
            flex: 1;
        }

        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
        }

        .date-range {
            background: linear-gradient(135deg, #e0f2fe, #dbeafe);
            padding: 8px 12px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 11px;
            font-weight: 600;
            color: #1e40af;
        }

        /* ========== TABLEAU ========== */
        .data-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header-icon {
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
        }

        .card-header-title {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .card-header-subtitle {
            font-size: 10px;
            margin: 2px 0 0;
            color: #94a3b8;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .journal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .journal-table th {
            background: #1e293b;
            color: white;
            padding: 10px 6px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            border: 1px solid #334155;
        }

        .journal-table td {
            padding: 8px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        /* ========== BADGES ========== */
        .numero-badge {
            font-weight: 700;
            background: #e2e8f0;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .date-badge {
            font-family: monospace;
            font-weight: 600;
            background: #e2e8f0;
            padding: 3px 6px;
            border-radius: 6px;
            display: inline-block;
        }

        .piece-number {
            font-family: monospace;
            font-weight: 700;
            color: #4f46e5;
            background: #eef2ff;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .compte-code-debit {
            font-weight: 800;
            color: #059669;
            background: #d1fae5;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-block;
            font-size: 11px;
        }

        .compte-code-credit {
            font-weight: 800;
            color: #dc2626;
            background: #fee2e2;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-block;
            font-size: 11px;
        }

        .badge-debit {
            font-size: 9px;
            color: #059669;
            background: #d1fae5;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .badge-credit {
            font-size: 9px;
            color: #dc2626;
            background: #fee2e2;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .libelle-content {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .libelle-texte {
            font-size: 9px;
            color: #475569;
            font-style: italic;
        }

        .debit-montant {
            color: #059669;
            font-weight: 700;
        }

        .credit-montant {
            color: #dc2626;
            font-weight: 700;
        }

        /* ========== TOTAUX ========== */
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

        /* ========== PIED ========== */
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        @media print {
            body { padding: 10px; }
            th { background: #1e293b !important; color: white !important; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo-icon" style="margin: 0 auto;">📊</div>
    <h1>CMCF <span>Consulting</span></h1>
    <p>Solution comptable professionnelle - OHADA</p>
    <div class="entreprise-badge">CMCF CONSULTING-FORMATION SASU</div>
</div>

<div class="info-section">
    <div class="info-item">
        <div class="info-label">Date d'édition</div>
        <div class="info-value">{{ now()->format('d/m/Y H:i') }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Document</div>
        <div class="info-value">JOURNAL COMPTABLE</div>
    </div>
    <div class="info-item">
        <div class="info-label">Généré par</div>
        <div class="info-value">{{ Auth::user()->name ?? 'Comptable' }}</div>
    </div>
</div>

<div class="date-range">
    Période du <strong>{{ request('date_debut', date('d/m/Y')) }}</strong> au <strong>{{ request('date_fin', date('d/m/Y')) }}</strong>
</div>

<!-- TABLEAU JOURNAL -->
<div class="data-card">
    <div class="card-header-custom">
        <div class="card-header-icon">📄</div>
        <div>
            <h3 class="card-header-title">LISTE DÉTAILLÉE DES OPÉRATIONS</h3>
            <p class="card-header-subtitle">Historique chronologique des écritures</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="journal-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>DATE</th>
                    <th>N° PIÈCE</th>
                    <th>COMPTE DÉBIT</th>
                    <th>COMPTE CRÉDIT</th>
                    <th>LIBELLÉ</th>
                    <th>DÉBIT (FC)</th>
                    <th>CRÉDIT (FC)</th>
                </tr>
            </thead>
            <tbody>
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
                        $debitIntitules = [];
                        $creditIntitules = [];

                        foreach($entry['lignes'] as $idx => $ligne) {
                            if($ligne['debit'] > 0) {
                                $debitLines[] = ['compte' => $ligne['compte'], 'montant' => $ligne['debit']];
                                if(!empty($ligne['nom_compte']) && !in_array($ligne['nom_compte'], $debitIntitules))
                                    $debitIntitules[] = $ligne['nom_compte'];
                            }
                            if($ligne['credit'] > 0) {
                                $creditLines[] = ['compte' => $ligne['compte'], 'montant' => $ligne['credit']];
                                if(!empty($ligne['nom_compte']) && !in_array($ligne['nom_compte'], $creditIntitules))
                                    $creditIntitules[] = $ligne['nom_compte'];
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

                        <td class="text-center">
                            @if(isset($debitLines[$i]))
                                <span class="compte-code-debit">{{ $debitLines[$i]['compte'] }}</span>
                            @else — @endif
                        </td>

                        <td class="text-center">
                            @if(isset($creditLines[$i]))
                                <span class="compte-code-credit">{{ $creditLines[$i]['compte'] }}</span>
                            @else — @endif
                        </td>

                        @if($i == 0)
                            <td rowspan="{{ $totalLignesEcriture }}">
                                <div class="libelle-content">
                                    <div>
                                        @foreach($debitIntitules as $intitule)
                                            <span class="badge-debit">{{ $intitule }}</span>
                                        @endforeach
                                        @foreach($creditIntitules as $intitule)
                                            <span class="badge-credit">{{ $intitule }}</span>
                                        @endforeach
                                    </div>
                                    @if(!empty($libellePrincipal))
                                        <div class="libelle-texte">{{ $libellePrincipal }}</div>
                                    @endif
                                </div>
                            </td>
                        @endif

                        <td class="text-right">
                            @if(isset($debitLines[$i]))
                                <span class="debit-montant">{{ number_format($debitLines[$i]['montant'], 0, ',', ' ') }}</span>
                            @else — @endif
                        </td>

                        <td class="text-right">
                            @if(isset($creditLines[$i]))
                                <span class="credit-montant">{{ number_format($creditLines[$i]['montant'], 0, ',', ' ') }}</span>
                            @else — @endif
                        </td>
                    </tr>
                    @endfor
                @empty
                <tr><td colspan="8" class="text-center" style="padding:30px;">Aucune donnée trouvée</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-right">TOTAUX GÉNÉRAUX</td>
                    <td class="total-debit">{{ number_format($totalDebitGeneral, 0, ',', ' ') }}</td>
                    <td class="total-credit">{{ number_format($totalCreditGeneral, 0, ',', ' ') }}</td>
                </tr>
                <tr class="equilibre-row">
                    <td colspan="6" class="text-right">ÉQUILIBRE</td>
                    <td colspan="2" class="text-center">
                        @if($totalDebitGeneral == $totalCreditGeneral)
                            <span class="equilibre-ok">✓ ÉQUILIBRÉ</span>
                        @else
                            <span class="equilibre-ko">✗ NON ÉQUILIBRÉ (Diff: {{ number_format(abs($totalDebitGeneral - $totalCreditGeneral), 0, ',', ' ') }} FC)</span>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="footer">
    <p>Document généré automatiquement par CMCF Compta Pro - Solution comptable OHADA</p>
    <p>Siège social : Lubumbashi, RDC | Tel : +243 812 212 290</p>
    <p>© {{ date('Y') }} CMCF CONSULTING-FORMATION SASU - Tous droits réservés</p>
    <p>Page {PAGE_NUM} / {PAGE_COUNT}</p>
</div>

</body>
</html>