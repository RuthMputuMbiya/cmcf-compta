<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bilan comptable</title>
    <style>
        * { font-family: 'Segoe UI', Arial, sans-serif; }
        body { padding: 20px; margin: 0; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e293b; padding-bottom: 15px; }
        .header h1 { color: #1e293b; margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 12px; color: #64748b; }
        .bilan-container { display: flex; gap: 30px; }
        .bilan-col { flex: 1; }
        .bilan-title { background: #3b82f6; color: white; padding: 10px; font-weight: bold; margin-bottom: 10px; font-size: 14px; }
        .bilan-title.passif { background: #10b981; }
        .section-title { background: #e0e7ff; padding: 8px 10px; font-weight: bold; margin-top: 10px; margin-bottom: 5px; font-size: 12px; color: #1e293b; }
        .section-subtitle { background: #f1f5f9; padding: 6px 10px; font-weight: bold; margin-top: 5px; margin-bottom: 5px; font-size: 11px; color: #475569; }
        .bilan-item-compte { display: flex; justify-content: space-between; padding: 4px 10px 4px 20px; border-bottom: 1px solid #f1f5f9; font-size: 9px; }
        .bilan-total { font-weight: bold; margin-top: 10px; padding: 8px 10px; border-top: 2px solid #1e293b; font-size: 12px; display: flex; justify-content: space-between; }
        .resultat { background: #fef3c7; padding: 10px; margin-top: 15px; border-radius: 5px; font-weight: bold; text-align: center; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CMCF CONSULTING-FORMATION SASU</h1>
        <p>Bilan comptable au {{ \Carbon\Carbon::parse($dateBilan)->format('d/m/Y') }}</p>
        <p>Société à responsabilité limitée - Capital social: 10.000.000 FC</p>
    </div>

    <div class="bilan-container">
        <!-- ==================== ACTIF ==================== -->
        <div class="bilan-col">
            <div class="bilan-title">ACTIF</div>
            
            <!-- Actif immobilisé -->
            <div class="section-title">Actif immobilisé (Classe 2)</div>
            @forelse($actifImmobiliseDetails as $detail)
            <div class="bilan-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->solde, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="bilan-item-compte"><span>Aucun compte</span></div>
            @endforelse
            <div class="bilan-total">
                <span>TOTAL ACTIF IMMOBILISÉ</span>
                <span>{{ number_format($totalActifImmobilise, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Actif circulant - Stocks -->
            <div class="section-title">Actif circulant - Stocks (Classe 3)</div>
            @php $stocksDisplayed = false; @endphp
            @foreach($actifCirculantDetails as $detail)
                @if(substr($detail->compte, 0, 1) == '3')
                    @php $stocksDisplayed = true; @endphp
                    <div class="bilan-item-compte">
                        <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                        <span>{{ number_format($detail->solde, 0, ',', ' ') }} FC</span>
                    </div>
                @endif
            @endforeach
            @if(!$stocksDisplayed)
            <div class="bilan-item-compte"><span>Aucun stock</span></div>
            @endif
            <div class="bilan-total">
                <span>TOTAL STOCKS</span>
                <span>{{ number_format($totalStocks ?? 0, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Actif circulant - Créances -->
            <div class="section-subtitle">Créances (Clients, débiteurs) - Classe 4 Actif</div>
            @php $creancesDisplayed = false; @endphp
            @foreach($actifCirculantDetails as $detail)
                @if(substr($detail->compte, 0, 1) == '4')
                    @php $creancesDisplayed = true; @endphp
                    <div class="bilan-item-compte">
                        <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                        <span>{{ number_format($detail->solde, 0, ',', ' ') }} FC</span>
                    </div>
                @endif
            @endforeach
            @if(!$creancesDisplayed)
            <div class="bilan-item-compte"><span>Aucune créance</span></div>
            @endif
            <div class="bilan-total">
                <span>TOTAL CRÉANCES</span>
                <span>{{ number_format($totalCreances ?? 0, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Total Actif Circulant -->
            <div class="bilan-total" style="background: #dbeafe;">
                <span>TOTAL ACTIF CIRCULANT</span>
                <span>{{ number_format($totalActifCirculant, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Trésorerie Actif -->
            <div class="section-title">Trésorerie Actif (Classe 5)</div>
            @forelse($tresorerieActifDetails as $detail)
            <div class="bilan-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->solde, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="bilan-item-compte"><span>Aucune trésorerie active</span></div>
            @endforelse
            <div class="bilan-total">
                <span>TOTAL TRÉSORERIE ACTIF</span>
                <span>{{ number_format($totalTresorerieActif, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Total Général Actif -->
            <div class="bilan-total" style="background: #dbeafe; margin-top: 15px;">
                <span>TOTAL GÉNÉRAL ACTIF</span>
                <span>{{ number_format($totalGeneralActif, 0, ',', ' ') }} FC</span>
            </div>
        </div>

        <!-- ==================== PASSIF ==================== -->
        <div class="bilan-col">
            <div class="bilan-title passif">PASSIF</div>
            
            <!-- Capitaux propres -->
            <div class="section-title">CAPITAUX PROPRES (Classe 1)</div>
            @forelse($capitauxProporesDetails as $detail)
            <div class="bilan-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->solde, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="bilan-item-compte"><span>Aucun compte</span></div>
            @endforelse
            <div class="bilan-total">
                <span>TOTAL CAPITAUX PROPRES</span>
                <span>{{ number_format($totalCapitauxPropores, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Passif circulant (Dettes) -->
            <div class="section-title">PASSIF CIRCULANT (Dettes - Classe 4 Passif)</div>
            @forelse($passifCirculantDetails as $detail)
            <div class="bilan-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->solde, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="bilan-item-compte"><span>Aucune dette circulante</span></div>
            @endforelse
            <div class="bilan-total">
                <span>TOTAL PASSIF CIRCULANT</span>
                <span>{{ number_format($totalPassifCirculant, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Trésorerie Passif (Découverts) -->
            <div class="section-title">Trésorerie Passif (Découverts bancaires)</div>
            @forelse($tresoreriePassifDetails as $detail)
            <div class="bilan-item-compte">
                <span>{{ $detail->compte }} - {{ $detail->nom_compte }}</span>
                <span>{{ number_format($detail->solde, 0, ',', ' ') }} FC</span>
            </div>
            @empty
            <div class="bilan-item-compte"><span>Aucun découvert bancaire</span></div>
            @endforelse
            <div class="bilan-total">
                <span>TOTAL TRÉSORERIE PASSIF</span>
                <span>{{ number_format($totalTresoreriePassif, 0, ',', ' ') }} FC</span>
            </div>

            <!-- Total Général Passif -->
            <div class="bilan-total" style="background: #d1fae5; margin-top: 15px;">
                <span>TOTAL GÉNÉRAL PASSIF</span>
                <span>{{ number_format($totalGeneralPassif, 0, ',', ' ') }} FC</span>
            </div>
        </div>
    </div>

    <!-- Résultat -->
    <div class="resultat">
        @if($resultatNet >= 0)
            <span class="text-success">📈 RÉSULTAT DE L'EXERCICE : BÉNÉFICE DE {{ number_format($resultatNet, 0, ',', ' ') }} FC</span>
        @else
            <span class="text-danger">📉 RÉSULTAT DE L'EXERCICE : PERTE DE {{ number_format(abs($resultatNet), 0, ',', ' ') }} FC</span>
        @endif
    </div>

    <div class="footer">
        <p>Document généré automatiquement par CMCF Compta - Solution comptable OHADA</p>
        <p>Généré le {{ now()->format('d/m/Y H:i:s') }} par {{ Auth::user()->name ?? 'Comptable' }}</p>
        <p>Page {PAGE_NUM} / {PAGE_COUNT}</p>
    </div>
</body>
</html>