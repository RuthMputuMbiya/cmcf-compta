<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Journal comptable</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f1f5f9;
            padding: 8px;
            text-align: left;
            font-size: 10px;
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
        <h1>JOURNAL COMPTABLE</h1>
        <p>Période du {{ \Carbon\Carbon::parse($dateDebut ?? now()->startOfMonth())->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin ?? now())->format('d/m/Y') }}</p>
        <p>Société CMCF - Système Comptable OHADA</p>
    </div>

    <div class="info">
        <table>
            <tr><td><strong>Date d'édition:</strong> {{ now()->format('d/m/Y H:i') }}</span><td><strong>Utilisateur:</strong> {{ Auth::user()->name ?? 'Comptable' }}</span></tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Compte</th>
                <th>Libellé</th>
                <th class="text-right">Débit (FCFA)</th>
                <th class="text-right">Crédit (FCFA)</th>
                <th>Référence</th>
            </tr>
        </thead>
        <tbody>
            @forelse($journal as $ligne)
            <tr>
                <td>{{ $ligne->date->format('d/m/Y') }}</span>
                <td>{{ $ligne->compte }}</span>
                <td>{{ $ligne->libelle }}</span>
                <td class="text-right">{{ number_format($ligne->debit, 0, ',', ' ') }}</span>
                <td class="text-right">{{ number_format($ligne->credit, 0, ',', ' ') }}</span>
                <td>{{ $ligne->reference }}</span>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Aucune écriture trouvée</span>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totaux">
                <td colspan="3" class="text-right"><strong>TOTAUX :</strong></span>
                <td class="text-right"><strong>{{ number_format($totalDebit, 0, ',', ' ') }}</strong></span>
                <td class="text-right"><strong>{{ number_format($totalCredit, 0, ',', ' ') }}</strong></span>
                <td></span>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Document généré automatiquement par CMCF Compta - Solution comptable OHADA</p>
    </div>
</body>
</html>