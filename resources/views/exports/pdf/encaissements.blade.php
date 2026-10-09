<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des encaissements</title>
    <style>
        * { font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f59e0b; color: white; padding: 10px; font-size: 11px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .text-right { text-align: right; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header"><h1>CMCF CONSULTING-FORMATION SASU</h1><p>Historique des encaissements</p><p>Période: {{ request('date_debut', 'début') }} au {{ request('date_fin', date('d/m/Y')) }}</p></div>
    <table>
        <thead><tr><th>Date</th><th>Facture N°</th><th>Client</th><th class="text-right">Montant (FC)</th><th>Mode</th><th>Référence</th></tr></thead>
        <tbody>
            @foreach($encaissements as $enc)
            <tr>
                <td>{{ $enc->date->format('d/m/Y') }}</td>
                <td><strong>{{ $enc->reference ?? '-' }}</strong></td>
                <td>{{ $enc->libelle ?? '-' }}</td>
                <td class="text-right">{{ number_format($enc->montant, 0, ',', ' ') }}</td>
                <td>@if($enc->mode_paiement == 'espece')Espèces@elseif($enc->mode_paiement == 'banque')Virement@elseChèque@endif</td>
                <td>{{ $enc->reference_paiement ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="background: #f8fafc; font-weight: bold;"><tr><td colspan="3" class="text-right">TOTAL :</td><td class="text-right">{{ number_format($total_encaisse, 0, ',', ' ') }} FC</td><td colspan="2"></td></tr></tfoot>
    </table>
    <div class="footer"><p>Document généré le {{ now()->format('d/m/Y H:i') }}</p></div>
</body>
</html>