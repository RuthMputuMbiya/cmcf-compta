<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grand Livre</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #1e293b;
        }
        .header p {
            font-size: 11px;
            margin: 5px 0 0;
            color: #64748b;
        }
        .info {
            margin-bottom: 20px;
            font-size: 9px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f1f5f9;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            border: 1px solid #cbd5e1;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
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
        .fw-bold {
            font-weight: bold;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 8px;
            display: inline-block;
        }
        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }
        .badge-success {
            background: #d1fae5;
            color: #059669;
        }
        .badge-info {
            background: #dbeafe;
            color: #2563eb;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .totaux {
            background: #f8fafc;
            font-weight: bold;
        }
        .compte-header {
            background: #e0e7ff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>GRAND LIVRE</h1>
        <p>Période du {{ request('date_debut', 'début') }} au {{ request('date_fin', 'aujourd\'hui') }}</p>
        <p>Société CMCF - Système Comptable OHADA</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Date d'édition:</strong> {{ now()->format('d/m/Y H:i') }}</td>
                <td><strong>Utilisateur:</strong> {{ Auth::user()->name ?? 'Comptable' }}</td>
            </tr>
            <tr>
                <td><strong>Total Débit:</strong> {{ number_format($totalDebit, 0, ',', ' ') }} FC</td>
                <td><strong>Total Crédit:</strong> {{ number_format($totalCredit, 0, ',', ' ') }} FC</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Balance:</strong> {{ number_format($totalDebit - $totalCredit, 0, ',', ' ') }} FC</span>
            </tr>
        </table>
    </div>

    @forelse($ecrituresParCompte as $compteData)
    <div style="margin-top: 20px;">
        <table>
            <tr class="compte-header">
                <th colspan="8">
                    Compte {{ $compteData['compte'] }} - {{ $compteData['nom_compte'] }}
                    <span style="float: right;">
                        Solde final: {{ number_format(abs($compteData['solde_final']), 0, ',', ' ') }} FCFA
                        ({{ $compteData['nature_final'] }})
                    </span>
                </th>
            </tr>
            <tr>
                <th>Date</th>
                <th>N° Pièce</th>
                <th>Libellé</th>
                <th>Compte correspondant</th>
                <th class="text-right">Débit (FC)</th>
                <th class="text-right">Crédit (FC)</th>
                <th class="text-right">Solde (FC)</th>
                <th class="text-center">Nature</th>
            </tr>
            @php $soldeCumul = 0; @endphp
            @foreach($compteData['lignes'] as $ligne)
            @php 
                $soldeCumul += $ligne['debit'] - $ligne['credit'];
                $natureSolde = $soldeCumul > 0 ? 'Débiteur' : ($soldeCumul < 0 ? 'Créditeur' : 'Équilibré');
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($ligne['date'])->format('d/m/Y') }}</span>
                <td>{{ $ligne['numero_piece'] }}</span>
                <td>{{ $ligne['libelle'] }}</span>
                <td>{{ $ligne['compte_correspondant'] }}</span>
                <td class="text-right">{{ number_format($ligne['debit'], 0, ',', ' ') }}</span>
                <td class="text-right">{{ number_format($ligne['credit'], 0, ',', ' ') }}</span>
                <td class="text-right fw-bold {{ $soldeCumul > 0 ? 'text-danger' : ($soldeCumul < 0 ? 'text-success' : '') }}">
                    {{ number_format(abs($soldeCumul), 0, ',', ' ') }}
                </span>
                <td class="text-center">
                    <span class="badge {{ $soldeCumul > 0 ? 'badge-danger' : ($soldeCumul < 0 ? 'badge-success' : 'badge-info') }}">
                        {{ $natureSolde }}
                    </span>
                </span>
            </tr>
            @endforeach
            <tr class="totaux">
                <td colspan="4" class="text-right fw-bold">TOTAUX DU COMPTE :</span>
                <td class="text-right fw-bold">{{ number_format(collect($compteData['lignes'])->sum('debit'), 0, ',', ' ') }}</span>
                <td class="text-right fw-bold">{{ number_format(collect($compteData['lignes'])->sum('credit'), 0, ',', ' ') }}</span>
                <td class="text-right fw-bold {{ $compteData['solde_final'] > 0 ? 'text-danger' : ($compteData['solde_final'] < 0 ? 'text-success' : '') }}">
                    {{ number_format(abs($compteData['solde_final']), 0, ',', ' ') }}
                </span>
                <td class="text-center">
                    <span class="badge {{ $compteData['solde_final'] > 0 ? 'badge-danger' : ($compteData['solde_final'] < 0 ? 'badge-success' : 'badge-info') }}">
                        {{ $compteData['nature_final'] }}
                    </span>
                </span>
            </tr>
        </table>
    </div>
    @empty
    <div class="text-center">
        <p>Aucune écriture comptable trouvée pour la période sélectionnée.</p>
    </div>
    @endforelse

    <div class="footer">
        <p>Document généré automatiquement par CMCF Compta - Solution comptable OHADA</p>
        <p>Page {PAGE_NUM} / {PAGE_COUNT}</p>
    </div>
</body>
</html>