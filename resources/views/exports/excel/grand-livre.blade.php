<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grand Livre</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
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
            font-size: 10px;
        }
        .compte-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .compte-title {
            background: #e0e7ff;
            padding: 8px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background: #f1f5f9;
            padding: 6px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            border: 1px solid #cbd5e1;
        }
        td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .totaux {
            background: #f8fafc;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>GRAND LIVRE</h1>
        <p>Société CMCF - Système Comptable OHADA</p>
    </div>

    <div class="info">
        <table>
            <tr><td><strong>Date d'édition:</strong> {{ now()->format('d/m/Y H:i') }}</span><td><strong>Utilisateur:</strong> {{ Auth::user()->name ?? 'Comptable' }}</span></tr>
            <tr><td colspan="2"><strong>Total Débit:</strong> {{ number_format($totalDebit, 0, ',', ' ') }} FCFA</span><td><strong>Total Crédit:</strong> {{ number_format($totalCredit, 0, ',', ' ') }} FCFA</span></tr>
        </table>
    </div>

    @forelse($ecrituresParCompte as $compteData)
    <div class="compte-section">
        <div class="compte-title">
            Compte {{ $compteData['compte'] }} - {{ $compteData['nom_compte'] }}
            (Solde final: {{ number_format(abs($compteData['solde_final']), 0, ',', ' ') }} FCFA - {{ $compteData['nature_final'] }})
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° Pièce</th>
                    <th>Libellé</th>
                    <th>Compte correspondant</th>
                    <th class="text-right">Débit (FCFA)</th>
                    <th class="text-right">Crédit (FCFA)</th>
                    <th class="text-right">Solde (FCFA)</th>
                    <th>Nature</th>
                </tr>
            </thead>
            <tbody>
                @php $soldeCumul = 0; @endphp
                @foreach($compteData['lignes'] as $ligne)
                @php $soldeCumul += $ligne['debit'] - $ligne['credit']; @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ligne['date'])->format('d/m/Y') }}</span>
                    <td>{{ $ligne['numero_piece'] }}</span>
                    <td>{{ $ligne['libelle'] }}</span>
                    <td>{{ $ligne['compte_correspondant'] }}</span>
                    <td class="text-right">{{ number_format($ligne['debit'], 0, ',', ' ') }}</span>
                    <td class="text-right">{{ number_format($ligne['credit'], 0, ',', ' ') }}</span>
                    <td class="text-right">{{ number_format(abs($soldeCumul), 0, ',', ' ') }}</span>
                    <td>{{ $soldeCumul > 0 ? 'Débiteur' : ($soldeCumul < 0 ? 'Créditeur' : 'Équilibré') }}</span>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="totaux">
                <tr>
                    <td colspan="4" class="text-right"><strong>TOTAUX :</strong></span>
                    <td class="text-right"><strong>{{ number_format(collect($compteData['lignes'])->sum('debit'), 0, ',', ' ') }}</strong></span>
                    <td class="text-right"><strong>{{ number_format(collect($compteData['lignes'])->sum('credit'), 0, ',', ' ') }}</strong></span>
                    <td class="text-right"><strong>{{ number_format(abs($compteData['solde_final']), 0, ',', ' ') }}</strong></span>
                    <td><strong>{{ $compteData['nature_final'] }}</strong></span>
                </tr>
            </tfoot>
        </table>
    </div>
    @empty
    <div class="text-center">Aucune écriture trouvée</div>
    @endforelse

    <div class="footer">
        <p>Document généré automatiquement par CMCF Compta - Solution comptable OHADA</p>
    </div>
</body>
</html>