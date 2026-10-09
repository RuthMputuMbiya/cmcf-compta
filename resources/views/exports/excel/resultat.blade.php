<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Compte de résultat</title>
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
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px;
        }
        .resultat-container {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        .resultat-col {
            flex: 1;
        }
        .resultat-title {
            background: #ef4444;
            color: white;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .resultat-title.produits {
            background: #10b981;
        }
        .section-title {
            background: #f1f5f9;
            padding: 8px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 11px;
            border-left: 3px solid #ef4444;
        }
        .section-title.produits {
            border-left-color: #10b981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background: #f8fafc;
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
        .total-general {
            background: #e0e7ff;
            padding: 8px;
            margin-top: 10px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        .resultat-net {
            background: #d1fae5;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }
        .resultat-net.perte {
            background: #fee2e2;
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
        <h1>COMPTE DE RÉSULTAT</h1>
        <p>Période du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
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

    <div class="resultat-container">
        <!-- ==================== CHARGES ==================== -->
        <div class="resultat-col">
            <div class="resultat-title">CHARGES</div>
            
            <!-- Charges d'exploitation -->
            <div class="section-title">Charges d'exploitation</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Compte</th>
                        <th>Libellé</th>
                        <th class="text-right">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chargesExploitationDetails as $detail)
                    <tr>
                        <td>{{ $detail->compte }}</span>
                        <td>{{ $detail->nom_compte }}</span>
                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucune charge</span>
                    </tr>
                    @endforelse
                    <tr class="totaux">
                        <td colspan="2" class="fw-bold">TOTAL CHARGES D'EXPLOITATION</span>
                        <td class="text-right fw-bold">{{ number_format($totalChargesExploitation, 0, ',', ' ') }}</span>
                    </tr>
                </tbody>
            </table>

            <!-- Charges financières -->
            <div class="section-title">Charges financières</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Compte</th>
                        <th>Libellé</th>
                        <th class="text-right">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chargesFinancieresDetails as $detail)
                    <tr>
                        <td>{{ $detail->compte }}</span>
                        <td>{{ $detail->nom_compte }}</span>
                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucune charge</span>
                    </tr>
                    @endforelse
                    <tr class="totaux">
                        <td colspan="2" class="fw-bold">TOTAL CHARGES FINANCIÈRES</span>
                        <td class="text-right fw-bold">{{ number_format($totalChargesFinancieres, 0, ',', ' ') }}</span>
                    </tr>
                </tbody>
            </table>

            <!-- Charges exceptionnelles -->
            <div class="section-title">Charges exceptionnelles</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Compte</th>
                        <th>Libellé</th>
                        <th class="text-right">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chargesExceptionnellesDetails as $detail)
                    <tr>
                        <td>{{ $detail->compte }}</span>
                        <td>{{ $detail->nom_compte }}</span>
                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucune charge</span>
                    </tr>
                    @endforelse
                    <tr class="totaux">
                        <td colspan="2" class="fw-bold">TOTAL CHARGES EXCEPTIONNELLES</span>
                        <td class="text-right fw-bold">{{ number_format($totalChargesExceptionnelles, 0, ',', ' ') }}</span>
                    </tr>
                </tbody>
            </table>

            <!-- TOTAL GENERAL CHARGES -->
            <div class="total-general">
                <span>TOTAL GÉNÉRAL DES CHARGES</span>
                <span>{{ number_format($totalGeneralCharges, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <!-- ==================== PRODUITS ==================== -->
        <div class="resultat-col">
            <div class="resultat-title produits">PRODUITS</div>
            
            <!-- Produits d'exploitation -->
            <div class="section-title produits">Produits d'exploitation</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Compte</th>
                        <th>Libellé</th>
                        <th class="text-right">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produitsExploitationDetails as $detail)
                    <tr>
                        <td>{{ $detail->compte }}</span>
                        <td>{{ $detail->nom_compte }}</span>
                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucun produit</span>
                    </tr>
                    @endforelse
                    <tr class="totaux">
                        <td colspan="2" class="fw-bold">TOTAL PRODUITS D'EXPLOITATION</span>
                        <td class="text-right fw-bold">{{ number_format($totalProduitsExploitation, 0, ',', ' ') }}</span>
                    </tr>
                </tbody>
            </table>

            <!-- Produits financiers -->
            <div class="section-title produits">Produits financiers</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Compte</th>
                        <th>Libellé</th>
                        <th class="text-right">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produitsFinanciersDetails as $detail)
                    <tr>
                        <td>{{ $detail->compte }}</span>
                        <td>{{ $detail->nom_compte }}</span>
                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucun produit</span>
                    </tr>
                    @endforelse
                    <tr class="totaux">
                        <td colspan="2" class="fw-bold">TOTAL PRODUITS FINANCIERS</span>
                        <td class="text-right fw-bold">{{ number_format($totalProduitsFinancieres, 0, ',', ' ') }}</span>
                    </tr>
                </tbody>
            </table>

            <!-- Produits exceptionnels -->
            <div class="section-title produits">Produits exceptionnels</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Compte</th>
                        <th>Libellé</th>
                        <th class="text-right">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produitsExceptionnelsDetails as $detail)
                    <tr>
                        <td>{{ $detail->compte }}</span>
                        <td>{{ $detail->nom_compte }}</span>
                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucun produit</span>
                    </tr>
                    @endforelse
                    <tr class="totaux">
                        <td colspan="2" class="fw-bold">TOTAL PRODUITS EXCEPTIONNELS</span>
                        <td class="text-right fw-bold">{{ number_format($totalProduitsExceptionnels, 0, ',', ' ') }}</span>
                    </tr>
                </tbody>
            </table>

            <!-- TOTAL GENERAL PRODUITS -->
            <div class="total-general">
                <span>TOTAL GÉNÉRAL DES PRODUITS</span>
                <span>{{ number_format($totalGeneralProduits, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    <!-- RÉSULTAT NET -->
    <div class="resultat-net {{ $resultatNet < 0 ? 'perte' : '' }}">
        <strong>RÉSULTAT NET DE L'EXERCICE :</strong>
        {{ number_format(abs($resultatNet), 0, ',', ' ') }} FCFA
        @if($resultatNet >= 0)
        <span style="color: #059669;">(BÉNÉFICE)</span>
        @else
        <span style="color: #dc2626;">(PERTE)</span>
        @endif
    </div>

    <div class="footer">
        <p>Document généré automatiquement par CMCF Compta - Solution comptable OHADA</p>
    </div>
</body>
</html>