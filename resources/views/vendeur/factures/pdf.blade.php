<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: white; padding: 40px; }
        .facture-container { max-width: 800px; margin: 0 auto; background: white; }
        
        /* En-tête */
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f59e0b; }
        .logo { font-size: 28px; font-weight: bold; color: #1e293b; }
        .logo span { color: #f59e0b; }
        .sous-titre { color: #64748b; font-size: 12px; margin-top: 5px; }
        
        /* Informations */
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-title { font-weight: bold; color: #1e293b; margin-bottom: 8px; font-size: 14px; border-left: 3px solid #f59e0b; padding-left: 8px; }
        .info-content { font-size: 12px; color: #475569; line-height: 1.6; }
        
        /* Tableau */
        .facture-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .facture-table th { background: #f59e0b; color: white; padding: 10px; text-align: left; font-size: 12px; }
        .facture-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .facture-table tfoot td { border-top: 2px solid #e2e8f0; padding: 10px; font-weight: bold; }
        
        /* Totaux */
        .totaux { text-align: right; margin-bottom: 30px; }
        .totaux table { width: 300px; margin-left: auto; }
        .totaux td { padding: 5px; }
        
        /* Signature */
        .signature { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature-line { border-top: 1px solid #cbd5e1; padding-top: 10px; width: 200px; text-align: center; font-size: 10px; color: #64748b; }
        
        /* Badges */
        .badge-payee { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-impayee { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
        
        .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="facture-container">
        <!-- En-tête -->
        <div class="header">
            <div class="logo">CMCF<span>Consulting</span></div>
            <div class="sous-titre">Solution comptable professionnelle</div>
            <div style="margin-top: 10px;">
                <strong>FACTURE N° {{ $facture->numero }}</strong>
            </div>
        </div>

        <!-- Informations -->
        <div class="info-section">
            <div class="info-box">
                <div class="info-title">Émetteur</div>
                <div class="info-content">
                    CMCF CONSULTING-FORMATION SASU<br>
                    Lubumbashi, RDC<br>
                    Tél: +243 812 212 290<br>
                    Email: info@cmcf.cd
                </div>
            </div>
            <div class="info-box">
                <div class="info-title">Client</div>
                <div class="info-content">
                    <strong>{{ $facture->client->nom }}</strong><br>
                    {{ $facture->client->adresse ?? 'Adresse non renseignée' }}<br>
                    Tél: {{ $facture->client->telephone ?? 'Non renseigné' }}<br>
                    Email: {{ $facture->client->email ?? 'Non renseigné' }}
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <div class="info-title">Date d'émission</div>
                <div class="info-content">{{ $facture->date->format('d/m/Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-title">Statut</div>
                <div class="info-content">
                    @if($facture->statut == 'payee')
                        <span class="badge-payee">✓ PAYÉE</span>
                    @else
                        <span class="badge-impayee">⏳ IMPAYÉE</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tableau des produits -->
        <table class="facture-table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Quantité</th>
                    <th>Prix unitaire (FC)</th>
                    <th>Montant HT (FC)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $facture->description ?? 'Prestation de services / Vente de produits' }}</span>
                    <td>1</span>
                    <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }}</span>
                    <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }}</span>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">Montant HT :</span>
                    <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }} FC</span>
                </tr>
                @if($facture->tva > 0)
                <tr>
                    <td colspan="3" style="text-align: right;">TVA ({{ $facture->tva }}%) :</span>
                    <td>{{ number_format($facture->montant_ht * $facture->tva / 100, 0, ',', ' ') }} FC</span>
                </tr>
                @endif
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL TTC :</span>
                    <td style="font-weight: bold; color: #f59e0b; font-size: 14px;">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FC</span>
                </tr>
            </tfoot>
        </table>

        <!-- Arrêté -->
        <div class="totaux">
            <table>
                <tr><td style="text-align: right;">Arrêté la présente facture à la somme de :</td></tr>
                <td><td style="text-align: right; font-weight: bold; font-size: 13px;">
                    {{ ucfirst($this->convertNumberToWords($facture->montant_ttc)) }} Francs Congolais
                </td></tr>
            </table>
        </div>

        <!-- Signature -->
        <div class="signature">
            <div class="signature-line">Signature du client</div>
            <div class="signature-line">Cachet de l'entreprise</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>CMCF CONSULTING-FORMATION SASU - Capital social: 1.000.000 FC - RCCM: LU/001/2025</p>
            <p>Siège social: Lubumbashi, Avenue Kasa-Vubu N°15</p>
        </div>
    </div>
</body>
</html>

@php
function convertNumberToWords($number) {
    $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
    $tens = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
    
    if ($number == 0) return 'zéro';
    
    $words = '';
    $millions = floor($number / 1000000);
    if ($millions > 0) {
        $words .= ($millions > 1 ? convertNumberToWords($millions) . ' ' : '') . 'million' . ($millions > 1 ? 's' : '') . ' ';
        $number %= 1000000;
    }
    
    $thousands = floor($number / 1000);
    if ($thousands > 0) {
        $words .= ($thousands > 1 ? convertNumberToWords($thousands) . ' ' : '') . 'mille ';
        $number %= 1000;
    }
    
    $hundreds = floor($number / 100);
    if ($hundreds > 0) {
        $words .= ($hundreds > 1 ? convertNumberToWords($hundreds) . ' cent' : 'cent') . ($hundreds > 1 && $number % 100 == 0 ? 's' : '') . ' ';
        $number %= 100;
    }
    
    if ($number > 0) {
        if ($number < 20) {
            $words .= $units[$number];
        } else {
            $ten = floor($number / 10);
            $unit = $number % 10;
            $words .= $tens[$ten];
            if ($unit > 0) {
                $words .= ($ten == 7 || $ten == 9 ? '-' : '-') . $units[$unit];
            }
        }
    }
    
    return trim($words);
}
@endphp