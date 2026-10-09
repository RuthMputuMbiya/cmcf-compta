<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance de vérification</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #1e293b;
        }
        .header p {
            font-size: 10px;
            margin: 5px 0 0;
            color: #64748b;
        }
        .info {
            margin-bottom: 15px;
            font-size: 8px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #f1f5f9;
            padding: 6px;
            text-align: center;
            font-size: 8px;
            font-weight: 600;
            border: 1px solid #cbd5e1;
        }
        td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .totaux {
            background: #f8fafc;
            font-weight: bold;
        }
        .equilibre {
            background: #fef3c7;
        }
        .footer {
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BALANCE DE VÉRIFICATION</h1>
        <p>Arrêté au {{ now()->format('d/m/Y') }}</p>
        <p>Société CMCF - Système Comptable OHADA</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Date d'édition:</strong> {{ now()->format('d/m/Y H:i') }}</span>
                <td><strong>Utilisateur:</strong> {{ Auth::user()->name ?? 'Comptable' }}</span>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">N°</th>
                <th rowspan="2">Libellé du compte</th>
                <th colspan="2">Soldes d'ouverture</th>
                <th colspan="2">Mouvements</th>
                <th colspan="2">Soldes de clôture</th>
            </tr>
            <tr>
                <th>Débit (FC)</th>
                <th>Crédit (FC)</th>
                <th>Débit (FC)</th>
                <th>Crédit (FC)</th>
                <th>Débit (FC)</th>
                <th>Crédit (FC)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($balance as $ligne)
            <tr>
                <td>{{ $ligne->compte }}</span>
                <td>{{ $ligne->nom_compte }}</span>
                <td class="text-right">{{ number_format($ligne->solde_debit_ouverture, 0, ',', ' ') }}</span>
                <td class="text-right">{{ number_format($ligne->solde_credit_ouverture, 0, ',', ' ') }}</span>
                <td class="text-right">{{ number_format($ligne->mouvement_debit, 0, ',', ' ') }}</span>
                <td class="text-right">{{ number_format($ligne->mouvement_credit, 0, ',', ' ') }}</span>
                <td class="text-right">{{ number_format($ligne->solde_debit_cloture, 0, ',', ' ') }}</span>
                <td class="text-right">{{ number_format($ligne->solde_credit_cloture, 0, ',', ' ') }}</span>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Aucune écriture comptable trouvée</span>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totaux">
                <td colspan="2" class="text-right fw-bold">TOTAUX :</span>
                <td class="text-right fw-bold">{{ number_format($totalSoldeDebitOuverture, 0, ',', ' ') }}</span>
                <td class="text-right fw-bold">{{ number_format($totalSoldeCreditOuverture, 0, ',', ' ') }}</span>
                <td class="text-right fw-bold">{{ number_format($totalMouvementDebit, 0, ',', ' ') }}</span>
                <td class="text-right fw-bold">{{ number_format($totalMouvementCredit, 0, ',', ' ') }}</span>
                <td class="text-right fw-bold">{{ number_format($totalSoldeDebitCloture, 0, ',', ' ') }}</span>
                <td class="text-right fw-bold">{{ number_format($totalSoldeCreditCloture, 0, ',', ' ') }}</span>
            </tr>
            <tr class="equilibre">
                <td colspan="2" class="text-right fw-bold">ÉQUILIBRE :</span>
                <td colspan="2" class="text-center">
                    @if($totalSoldeDebitOuverture == $totalSoldeCreditOuverture)
                    ✓ Équilibré
                    @else
                    ✗ Non équilibré
                    @endif
                </span>
                <td colspan="2" class="text-center">
                    @if($totalMouvementDebit == $totalMouvementCredit)
                    ✓ Équilibré
                    @else
                    ✗ Non équilibré
                    @endif
                </span>
                <td colspan="2" class="text-center">
                    @if($totalSoldeDebitCloture == $totalSoldeCreditCloture)
                    ✓ Équilibré
                    @else
                    ✗ Non équilibré
                    @endif
                </span>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Document généré automatiquement par CMCF Compta - Solution comptable OHADA</p>
        <p>Page {PAGE_NUM} / {PAGE_COUNT}</p>
    </div>
</body>
</html>