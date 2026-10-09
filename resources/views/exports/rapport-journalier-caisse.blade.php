<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport journalier - Caisse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 30px; background: white; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #10b981; padding-bottom: 20px; }
        .header h1 { color: #1e293b; font-size: 24px; }
        .header p { color: #64748b; font-size: 12px; }
        .date-info { text-align: center; margin-bottom: 20px; background: #f8fafc; padding: 10px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #10b981; color: white; padding: 10px; font-size: 11px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .text-right { text-align: right; }
        .text-success { color: #059669; font-weight: bold; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .totaux { background: #f8fafc; font-weight: bold; border-top: 2px solid #e2e8f0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CMCF CONSULTING-FORMATION SASU</h1>
        <p>Rapport journalier de caisse</p>
    </div>
    <div class="date-info">Date : {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}</div>
    
    <h3 style="margin: 15px 0 10px;">Mouvements de caisse</h3>
    <table>
        <thead><tr><th>Heure</th><th>Type</th><th>Libellé</th><th class="text-right">Montant (FC)</th><th>Référence</th></tr></thead>
        <tbody>
            @foreach($operations_caisse as $op)
            <tr>
                <td>{{ $op->created_at->format('H:i') }}</td>
                <td>{{ $op->type == 'entree' ? 'Entrée' : 'Sortie' }}</td>
                <td>{{ $op->libelle }}</td>
                <td class="text-right {{ $op->type == 'entree' ? 'text-success' : 'text-danger' }}">{{ number_format($op->montant, 0, ',', ' ') }}</td>
                <td>{{ $op->reference ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="totaux">
            <tr><td colspan="3" class="text-right">TOTAUX :</td><td class="text-right text-success">{{ number_format($total_entrees, 0, ',', ' ') }}</td><td class="text-right text-danger">{{ number_format($total_sorties, 0, ',', ' ') }}</td></tr>
        </tfoot>
    </table>
    
    <h3 style="margin: 15px 0 10px;">Mouvements bancaires</h3>
    <table>
        <thead><tr><th>Heure</th><th>Type</th><th>Libellé</th><th class="text-right">Montant (FC)</th><th>Référence</th></tr></thead>
        <tbody>
            @foreach($operations_banque as $op)
            <tr>
                <td>{{ $op->created_at->format('H:i') }}</td>
                <td>{{ $op->type == 'depot' ? 'Dépôt' : 'Retrait' }}</td>
                <td>{{ $op->libelle }}</td>
                <td class="text-right {{ $op->type == 'depot' ? 'text-success' : 'text-danger' }}">{{ number_format(abs($op->montant), 0, ',', ' ') }}</td>
                <td>{{ $op->reference ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="totaux">
            <tr><td colspan="3" class="text-right">TOTAUX :</td><td class="text-right text-success">{{ number_format($total_depots, 0, ',', ' ') }}</td><td class="text-right text-danger">{{ number_format($total_retraits, 0, ',', ' ') }}</td></tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y H:i:s') }} - CMCF Compta Pro</p>
        <p>Signature : _________________</p>
    </div>
</body>
</html>