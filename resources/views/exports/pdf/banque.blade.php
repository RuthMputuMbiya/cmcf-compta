<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation bancaire</title>
    <style>
        * { font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1e293b; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .stat-card { background: #f8fafc; padding: 15px; border-radius: 12px; text-align: center; width: 32%; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #3b82f6; color: white; padding: 10px; font-size: 11px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .text-right { text-align: right; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header"><h1>CMCF CONSULTING-FORMATION SASU</h1><p>Situation bancaire</p><p>Date: {{ now()->format('d/m/Y') }}</p></div>
    <div class="stats">
        <div class="stat-card"><strong>Solde actuel</strong><br><span style="font-size: 18px; color:#3b82f6;">{{ number_format($solde, 0, ',', ' ') }} FC</span></div>
        <div class="stat-card"><strong>Total dépôts</strong><br><span style="color:#10b981;">{{ number_format($depots ?? 0, 0, ',', ' ') }} FC</span></div>
        <div class="stat-card"><strong>Total retraits</strong><br><span style="color:#ef4444;">{{ number_format($retraits ?? 0, 0, ',', ' ') }} FC</span></div>
    </div>
    <table>
        <thead><tr><th>Date</th><th>Type</th><th>Libellé</th><th class="text-right">Montant (FC)</th><th>Référence</th></tr></thead>
        <tbody>
            @foreach($operations as $op)
            <tr>
                <td>{{ $op->date->format('d/m/Y') }}</td>
                <td>@if($op->type == 'depot')<span style="color:#10b981;">Dépôt</span>@else<span style="color:#ef4444;">Retrait</span>@endif</td>
                <td>{{ $op->libelle }}</td>
                <td class="text-right">{{ number_format(abs($op->montant), 0, ',', ' ') }}</td>
                <td>{{ $op->reference ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer"><p>Document généré le {{ now()->format('d/m/Y H:i') }}</p></div>
</body>
</html>