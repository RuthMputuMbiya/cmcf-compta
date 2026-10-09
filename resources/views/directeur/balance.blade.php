@extends('layouts.directeur')

@section('title', 'Balance de vérification')
@section('header-title', '⚖️ Balance de vérification')
@section('header-subtitle', 'Soldes d\'ouverture, mouvements et soldes de clôture')

@section('directeur-content')
<div class="fade-up">
    
   

    @if($equilibre)
    <div class="alert alert-success rounded-4 mb-4">
        <i class="fas fa-check-circle me-2"></i> ✅ La balance est équilibrée : Total débiteur = Total créditeur
    </div>
    @else
    <div class="alert alert-danger rounded-4 mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i> ⚠️ Attention : La balance n'est pas équilibrée !
    </div>
    @endif

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table balance-table">
                <thead>
                    <tr class="table-header">
                        <th rowspan="2" style="vertical-align: middle;">N° Compte</th>
                        <th rowspan="2" style="vertical-align: middle;">Libellé</th>
                        <th colspan="2" class="text-center">Soldes d'ouverture</th>
                        <th colspan="2" class="text-center">Mouvements</th>
                        <th colspan="2" class="text-center">Soldes de clôture</th>
                     </tr>
                    <tr class="table-subheader">
                        <th class="text-right">Débit (FC)</th>
                        <th class="text-right">Crédit (FC)</th>
                        <th class="text-right">Débit (FC)</th>
                        <th class="text-right">Crédit (FC)</th>
                        <th class="text-right">Débit (FC)</th>
                        <th class="text-right">Crédit (FC)</th>
                     </tr>
                </thead>
                <tbody>
                    @forelse($balance as $ligne)
                    <tr>
                        <td><strong class="text-primary">{{ $ligne->compte }}</strong></td>
                        <td>{{ $ligne->nom_compte }}</td>
                        <td class="text-right {{ $ligne->solde_debit_ouverture > 0 ? 'text-success' : '' }}">
                            {{ number_format($ligne->solde_debit_ouverture, 0, ',', ' ') }}
                        </td>
                        <td class="text-right {{ $ligne->solde_credit_ouverture > 0 ? 'text-danger' : '' }}">
                            {{ number_format($ligne->solde_credit_ouverture, 0, ',', ' ') }}
                        </td>
                        <td class="text-right text-success">{{ number_format($ligne->mouvement_debit, 0, ',', ' ') }}</td>
                        <td class="text-right text-danger">{{ number_format($ligne->mouvement_credit, 0, ',', ' ') }}</td>
                        <td class="text-right {{ $ligne->solde_debit_cloture > 0 ? 'text-success' : '' }}">
                            {{ number_format($ligne->solde_debit_cloture, 0, ',', ' ') }}
                        </td>
                        <td class="text-right {{ $ligne->solde_credit_cloture > 0 ? 'text-danger' : '' }}">
                            {{ number_format($ligne->solde_credit_cloture, 0, ',', ' ') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-database fa-3x text-muted mb-3 d-block"></i>
                            Aucune écriture comptable trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-footer">
                    <tr>
                        <td colspan="2" class="text-end fw-bold">TOTAUX :</td>
                        <td class="text-right fw-bold text-success">{{ number_format($totalSoldeDebitOuverture, 0, ',', ' ') }}</td>
                        <td class="text-right fw-bold text-danger">{{ number_format($totalSoldeCreditOuverture, 0, ',', ' ') }}</td>
                        <td class="text-right fw-bold text-success">{{ number_format($totalMouvementDebit, 0, ',', ' ') }}</td>
                        <td class="text-right fw-bold text-danger">{{ number_format($totalMouvementCredit, 0, ',', ' ') }}</td>
                        <td class="text-right fw-bold text-success">{{ number_format($totalSoldeDebitCloture, 0, ',', ' ') }}</td>
                        <td class="text-right fw-bold text-danger">{{ number_format($totalSoldeCreditCloture, 0, ',', ' ') }}</td>
                    </tr>
                    <tr class="equilibre-row">
                        <td colspan="2" class="text-end fw-bold">ÉQUILIBRE :</td>
                        <td colspan="2" class="text-center">
                            @if($totalSoldeDebitOuverture == $totalSoldeCreditOuverture)
                            <span class="badge-success">✓ Équilibré</span>
                            @else
                            <span class="badge-danger">✗ Non équilibré</span>
                            @endif
                        </td>
                        <td colspan="2" class="text-center">
                            @if($totalMouvementDebit == $totalMouvementCredit)
                            <span class="badge-success">✓ Équilibré</span>
                            @else
                            <span class="badge-danger">✗ Non équilibré</span>
                            @endif
                        </td>
                        <td colspan="2" class="text-center">
                            @if($totalSoldeDebitCloture == $totalSoldeCreditCloture)
                            <span class="badge-success">✓ Équilibré</span>
                            @else
                            <span class="badge-danger">✗ Non équilibré</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Légende -->
    <div class="legend-card mt-3">
        <div class="legend-content">
            <span class="legend-item"><i class="fas fa-square text-success"></i> Solde débiteur (Actif/Charge)</span>
            <span class="legend-item"><i class="fas fa-square text-danger"></i> Solde créditeur (Passif/Produit)</span>
            <span class="legend-item"><i class="fas fa-chart-line me-1"></i> Balance équilibrée = Débit = Crédit</span>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Bouton export PDF */
    .btn-danger-custom {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.3);
        color: white;
    }
    
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .balance-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .balance-table thead th {
        background: #f8fafc;
        padding: 12px 15px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .table-header {
        background: #e0e7ff;
    }
    
    .table-header th {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        padding: 15px;
    }
    
    .table-subheader th {
        background: #f1f5f9;
        font-size: 10px;
        padding: 10px 12px;
    }
    
    .balance-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }
    
    .balance-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .table-footer {
        background: #f8fafc;
        font-weight: bold;
        border-top: 2px solid #e2e8f0;
    }
    
    .table-footer td {
        padding: 12px 15px;
    }
    
    .equilibre-row {
        background: #fef3c7;
    }
    
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    
    .legend-card {
        background: white;
        border-radius: 12px;
        padding: 12px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .legend-content {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
    }
    
    .legend-item i {
        font-size: 10px;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .text-success {
        color: #059669;
    }
    
    .text-danger {
        color: #dc2626;
    }
    
    .fw-bold {
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .balance-table thead {
            display: none;
        }
        .balance-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        .balance-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        .balance-table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 11px;
            color: #64748b;
            width: 45%;
        }
        .legend-content {
            flex-direction: column;
            align-items: center;
        }
        .btn-danger-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Ajouter les labels pour responsive
    document.querySelectorAll('.balance-table tbody tr').forEach(row => {
        let cells = row.querySelectorAll('td');
        if (cells.length >= 8) {
            cells[0].setAttribute('data-label', 'N° Compte');
            cells[1].setAttribute('data-label', 'Libellé');
            cells[2].setAttribute('data-label', 'SD Ouverture');
            cells[3].setAttribute('data-label', 'SC Ouverture');
            cells[4].setAttribute('data-label', 'Mouvt Débit');
            cells[5].setAttribute('data-label', 'Mouvt Crédit');
            cells[6].setAttribute('data-label', 'SD Clôture');
            cells[7].setAttribute('data-label', 'SC Clôture');
        }
    });
</script>
@endpush
@endsection