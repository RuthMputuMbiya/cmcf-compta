<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte de résultat</title>
    <style>
        * { font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 20px; margin: 0; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e293b; padding-bottom: 15px; }
        .header h1 { color: #1e293b; margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 12px; color: #64748b; }
        .info {
            margin-bottom: 20px;
            font-size: 10px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px;
        }
        .result-card { 
            background: linear-gradient(135deg, #8b5cf6, #7c3aed); 
            color: white; 
            text-align: center; 
            padding: 20px; 
            border-radius: 16px; 
            margin-bottom: 30px; 
        }
        .result-card h2 { font-size: 28px; margin: 10px 0; }
        .resultat-container {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        .resultat-col {
            flex: 1;
        }
        .resultat-title { 
            background: #10b981; 
            color: white; 
            padding: 10px; 
            font-weight: bold; 
            margin-bottom: 10px;
            font-size: 14px;
        }
        .resultat-title.charges { background: #ef4444; }
        .section-title {
            background: #f1f5f9;
            padding: 8px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 11px;
            border-left: 3px solid #10b981;
        }
        .section-title.charges {
            border-left-color: #ef4444;
        }
        .resultat-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 6px 0; 
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .resultat-item-compte {
            display: flex;
            justify-content: space-between;
            padding: 4px 0 4px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9px;
        }
        .resultat-total { 
            font-weight: bold; 
            margin-top: 10px; 
            padding-top: 10px; 
            border-top: 2px solid #1e293b;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .total-general {
            background: #e0e7ff;
            padding: 10px;
            margin-top: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #e2e8f0; 
            font-size: 9px; 
            color: #94a3b8;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #059669;
        }
        .text-danger {
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CMCF CONSULTING-FORMATION SASU</h1>
        <p>Compte de résultat</p>
        <p>Période du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Date d'édition:</strong> {{ now()->format('d/m/Y H:i') }}</span>
                <td><strong>Utilisateur:</strong> {{ Auth::user()->name ?? 'Comptable' }}</span>
            </tr>
        </table>
    </div>

    <!-- Résultat net -->
    <div class="result-card">
        <p>RÉSULTAT NET DE L'EXERCICE</p>
        <h2>{{ number_format(abs($resultatNet), 0, ',', ' ') }} FCFA</h2>
        <p>{{ $resultatNet >= 0 ? 'BÉNÉFICE' : 'PERTE' }}</p>
    </div>

    <div class="resultat-container">
        <!-- ==================== CHARGES ==================== -->
        <div class="resultat-col">
            <div class="resultat-title charges">CHARGES</div>
            
            <!-- Charges d'exploitation -->
            <div class="section-title charges">Charges d'exploitation</div>
            @forelse($chargesExploitationDetails as $detail)
            <div class="resultat-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->montant, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="resultat-item">Aucune charge d'exploitation</div>
            @endforelse
            <div class="resultat-total">
                <span>TOTAL CHARGES D'EXPLOITATION</span>
                <span class="text-danger">{{ number_format($totalChargesExploitation, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Charges financières -->
            <div class="section-title charges">Charges financières</div>
            @forelse($chargesFinancieresDetails as $detail)
            <div class="resultat-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->montant, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="resultat-item">Aucune charge financière</div>
            @endforelse
            <div class="resultat-total">
                <span>TOTAL CHARGES FINANCIÈRES</span>
                <span class="text-danger">{{ number_format($totalChargesFinancieres, 0, ',', ' ') }} FCFA</span>
            </div>

            <!-- Charges exceptionnelles -->
            <div class="section-title charges">Charges exceptionnelles</div>
            @forelse($chargesExceptionnellesDetails as $detail)
            <div class="resultat-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->montant, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="resultat-item">Aucune charge exceptionnelle</div>
            @endforelse
            <div class="resultat-total">
                <span>TOTAL CHARGES EXCEPTIONNELLES</span>
                <span class="text-danger">{{ number_format($totalChargesExceptionnelles, 0, ',', ' ') }} FC</span>
            </div>

            <!-- TOTAL GENERAL CHARGES -->
            <div class="total-general">
                <span>TOTAL GÉNÉRAL DES CHARGES</span>
                <span class="text-danger">{{ number_format($totalGeneralCharges, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Bénéfice (si présent) -->
            @if($resultatNet > 0)
            <div class="total-general" style="background: #d1fae5; margin-top: 10px;">
                <span>BÉNÉFICE DE L'EXERCICE</span>
                <span class="text-success">{{ number_format($resultatNet, 0, ',', ' ') }} FC</span>
            </div>
            @endif
        </div>

        <!-- ==================== PRODUITS ==================== -->
        <div class="resultat-col">
            <div class="resultat-title">PRODUITS</div>
            
            <!-- Produits d'exploitation -->
            <div class="section-title">Produits d'exploitation</div>
            @forelse($produitsExploitationDetails as $detail)
            <div class="resultat-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->montant, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="resultat-item">Aucun produit d'exploitation</div>
            @endforelse
            <div class="resultat-total">
                <span>TOTAL PRODUITS D'EXPLOITATION</span>
                <span class="text-success">{{ number_format($totalProduitsExploitation, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Produits financiers -->
            <div class="section-title">Produits financiers</div>
            @forelse($produitsFinanciersDetails as $detail)
            <div class="resultat-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->montant, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="resultat-item">Aucun produit financier</div>
            @endforelse
            <div class="resultat-total">
                <span>TOTAL PRODUITS FINANCIERS</span>
                <span class="text-success">{{ number_format($totalProduitsFinancieres, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Produits exceptionnels -->
            <div class="section-title">Produits exceptionnels</div>
            @forelse($produitsExceptionnelsDetails as $detail)
            <div class="resultat-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->montant, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="resultat-item">Aucun produit exceptionnel</div>
            @endforelse
            <div class="resultat-total">
                <span>TOTAL PRODUITS EXCEPTIONNELS</span>
                <span class="text-success">{{ number_format($totalProduitsExceptionnels, 0, ',', ' ') }} FC</span>
            </div>

            <!-- TOTAL GENERAL PRODUITS -->
            <div class="total-general">
                <span>TOTAL GÉNÉRAL DES PRODUITS</span>
                <span class="text-success">{{ number_format($totalGeneralProduits, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Perte (si présente) -->
            @if($resultatNet < 0)
            <div class="total-general" style="background: #fee2e2; margin-top: 10px;">
                <span>PERTE DE L'EXERCICE</span>
                <span class="text-danger">{{ number_format(abs($resultatNet), 0, ',', ' ') }} FC</span>
            </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Document généré automatiquement par CMCF Compta - Solution comptable OHADA</p>
        <p>Page {PAGE_NUM} / {PAGE_COUNT}</p>
    </div>
</body>
</html>