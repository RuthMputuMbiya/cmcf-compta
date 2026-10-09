@extends('layouts.caissier')

@section('title', 'Rapport journalier')
@section('header-title', '📊 Rapport journalier de caisse')
@section('header-subtitle', 'Récapitulatif des opérations du jour')

@section('caissier-content')
<div class="fade-up">

    <!-- Sélecteur de date -->
    <div class="data-card mb-4">
        <div class="card-header-custom">
            <div class="card-header-icon"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <h3 class="card-header-title">Sélection de la période</h3>
                <p class="card-header-subtitle">Choisissez la date du rapport</p>
            </div>
        </div>
        <div class="p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Date du rapport</label>
                    <input type="date" id="date_rapport" class="form-control-custom" value="{{ $date ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <button onclick="chargerRapport()" class="btn-primary-custom w-100"><i class="fas fa-search me-2"></i>Voir</button>
                </div>
                <div class="col-md-2">
                    <button onclick="exporterPDF()" class="btn-success-custom w-100"><i class="fas fa-file-pdf me-2"></i>PDF</button>
                </div>
                <div class="col-md-2">
                    <button onclick="exporterExcel()" class="btn-info-custom w-100"><i class="fas fa-file-excel me-2"></i>Excel</button>
                </div>
                <div class="col-md-2">
                    <button onclick="window.print()" class="btn-secondary-custom w-100"><i class="fas fa-print me-2"></i>Imprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête du rapport -->
    <div class="rapport-header mb-4">
        <div class="text-center">
            <div class="logo-print mb-3">
                <div class="logo-icon mx-auto">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <h2 class="mb-1">CMCF CONSULTING-FORMATION SASU</h2>
            <p class="text-muted">Rapport journalier de caisse</p>
            <h4 class="text-primary">Date : {{ \Carbon\Carbon::parse($date ?? now())->translatedFormat('l d F Y') }}</h4>
        </div>
    </div>

    <!-- Cartes récapitulatives -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total entrées</div>
                <div class="stat-value">{{ number_format($total_entrees ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total sorties</div>
                <div class="stat-value">{{ number_format($total_sorties ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <div class="stat-label">Solde de clôture</div>
                <div class="stat-value">{{ number_format($solde_cloture ?? 0, 0, ',', ' ') }} FC</div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
            <div class="stat-info">
                <div class="stat-label">Nombre opérations</div>
                <div class="stat-value">{{ ($operations_caisse->count() ?? 0) + ($operations_banque->count() ?? 0) }}</div>
            </div>
        </div>
    </div>

    <!-- Opérations caisse -->
    <div class="data-card mb-4">
        <div class="card-header-custom green">
            <div class="card-header-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <h3 class="card-header-title">Mouvements de caisse</h3>
                <p class="card-header-subtitle">Détail des opérations en espèces</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Heure</th>
                        <th>Type</th>
                        <th>Libellé</th>
                        <th class="text-end">Montant (FC)</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operations_caisse ?? [] as $op)
                    <tr>
                        <td data-label="Heure">{{ $op->created_at->format('H:i') }}</span>
                        <td data-label="Type">
                            @if($op->type == 'entree')
                                <span class="badge-success"><i class="fas fa-arrow-down me-1"></i>Entrée</span>
                            @else
                                <span class="badge-danger"><i class="fas fa-arrow-up me-1"></i>Sortie</span>
                            @endif
                        </span>
                        <td data-label="Libellé">{{ $op->libelle }}</span>
                        <td data-label="Montant" class="text-end {{ $op->type == 'entree' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ number_format($op->montant, 0, ',', ' ') }}
                        </span>
                        <td data-label="Référence">{{ $op->reference ?? '-' }}</span>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <td colspan="5" class="py-4">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <p class="text-muted">Aucune opération en caisse ce jour</p>
                            </div>
                        </span>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-footer">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAUX :</span>
                        <td class="text-end text-success fw-bold">{{ number_format($total_entrees ?? 0, 0, ',', ' ') }}</span>
                        <td class="text-end text-danger fw-bold">{{ number_format($total_sorties ?? 0, 0, ',', ' ') }}</span>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Opérations banque -->
    <div class="data-card mb-4">
        <div class="card-header-custom blue">
            <div class="card-header-icon"><i class="fas fa-university"></i></div>
            <div>
                <h3 class="card-header-title">Mouvements bancaires</h3>
                <p class="card-header-subtitle">Détail des transactions bancaires</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Heure</th>
                        <th>Type</th>
                        <th>Libellé</th>
                        <th class="text-end">Montant (FC)</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operations_banque ?? [] as $op)
                    <tr>
                        <td data-label="Heure">{{ $op->created_at->format('H:i') }}</span>
                        <td data-label="Type">
                            @if($op->type == 'depot')
                                <span class="badge-success"><i class="fas fa-arrow-down me-1"></i>Dépôt</span>
                            @else
                                <span class="badge-danger"><i class="fas fa-arrow-up me-1"></i>Retrait</span>
                            @endif
                        </span>
                        <td data-label="Libellé">{{ $op->libelle }}</span>
                        <td data-label="Montant" class="text-end {{ $op->type == 'depot' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ number_format(abs($op->montant), 0, ',', ' ') }}
                        </span>
                        <td data-label="Référence">{{ $op->reference ?? '-' }}</span>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <td colspan="5" class="py-4">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <p class="text-muted">Aucune opération bancaire ce jour</p>
                            </div>
                        </span>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-footer">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAUX :</span>
                        <td class="text-end text-success fw-bold">{{ number_format($total_depots ?? 0, 0, ',', ' ') }}</span>
                        <td class="text-end text-danger fw-bold">{{ number_format($total_retraits ?? 0, 0, ',', ' ') }}</span>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Récapitulatif et signature -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="card-header-custom purple">
                    <div class="card-header-icon"><i class="fas fa-chart-pie"></i></div>
                    <div>
                        <h3 class="card-header-title">Récapitulatif</h3>
                        <p class="card-header-subtitle">Synthèse de la journée</p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="recap-item">
                        <div class="recap-label">Solde initial (matin)</div>
                        <div class="recap-value">{{ number_format($solde_initial ?? 0, 0, ',', ' ') }} FC</div>
                    </div>
                    <div class="recap-item">
                        <div class="recap-label">Total entrées</div>
                        <div class="recap-value text-success">+ {{ number_format($total_entrees ?? 0, 0, ',', ' ') }} FC</div>
                    </div>
                    <div class="recap-item">
                        <div class="recap-label">Total sorties</div>
                        <div class="recap-value text-danger">- {{ number_format($total_sorties ?? 0, 0, ',', ' ') }} FC</div>
                    </div>
                    <div class="recap-item total">
                        <div class="recap-label">Solde de clôture</div>
                        <div class="recap-value text-primary fs-3 fw-bold">{{ number_format($solde_cloture ?? 0, 0, ',', ' ') }} FC</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="card-header-custom secondary">
                    <div class="card-header-icon"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <h3 class="card-header-title">Validation</h3>
                        <p class="card-header-subtitle">Certification du rapport</p>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <div class="signature-box">
                        <p class="text-muted mb-4">Je certifie que les informations ci-dessus sont exactes</p>
                        <div class="row">
                            <div class="col-6">
                                <div class="signature-line">
                                    <p>Fait à Lubumbashi, le {{ now()->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="signature-line">
                                    <p>Signature de la caissière</p>
                                    <div class="mt-4">_________________</div>
                                    <p class="mt-2">{{ auth()->user()->name }} {{ auth()->user()->prenom }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .card-header-custom.green {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .card-header-custom.blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .card-header-custom.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }
    .card-header-custom.secondary {
        background: linear-gradient(135deg, #64748b, #475569);
    }
    .card-header-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .card-header-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    .card-header-subtitle {
        font-size: 11px;
        margin: 2px 0 0;
        color: rgba(255,255,255,0.8);
    }
    .rapport-header {
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }
    .logo-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s;
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-success .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-danger .stat-icon { background: #fee2e2; color: #ef4444; }
    .stat-primary .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-info .stat-icon { background: #e0e7ff; color: #6366f1; }
    .stat-icon i { font-size: 20px; }
    .stat-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
    }
    .stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
    }
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead th {
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    .data-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    .table-footer {
        background: #f8fafc;
        font-weight: bold;
        border-top: 2px solid #e2e8f0;
    }
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .recap-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    .recap-item.total {
        border-top: 2px solid #e2e8f0;
        border-bottom: none;
        margin-top: 10px;
        padding-top: 15px;
    }
    .recap-label {
        font-size: 13px;
        color: #64748b;
    }
    .recap-value {
        font-size: 14px;
        font-weight: 600;
    }
    .signature-box {
        padding: 20px;
    }
    .signature-line {
        border-top: 1px solid #cbd5e1;
        padding-top: 20px;
        margin-top: 20px;
    }
    .btn-primary-custom, .btn-success-custom, .btn-info-custom, .btn-secondary-custom {
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        border: none;
    }
    .btn-primary-custom {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
    }
    .btn-success-custom {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    .btn-info-custom {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }
    .btn-info-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(6,182,212,0.3);
    }
    .btn-secondary-custom {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
    }
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(100,116,139,0.3);
    }
    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        transition: all 0.2s;
        background: white;
    }
    .form-control-custom:focus {
        border-color: #8b5cf6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }
    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .empty-state {
        text-align: center;
        padding: 30px;
    }
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .data-table thead {
            display: none;
        }
        .data-table tbody tr {
            display: block;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        .data-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        .data-table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 11px;
            color: #64748b;
        }
    }
    @media print {
        .btn-primary-custom, .btn-success-custom, .btn-info-custom, .btn-secondary-custom, .data-card.mb-4:first-child {
            display: none;
        }
        body {
            background: white;
            padding: 20px;
        }
        .stat-card {
            box-shadow: none;
            border: 1px solid #e2e8f0;
        }
        .card-header-custom {
            background: #e2e8f0;
            color: #1e293b;
        }
        .card-header-custom .card-header-icon {
            background: #cbd5e1;
        }
        .card-header-title, .card-header-subtitle {
            color: #1e293b;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function chargerRapport() {
        let date = document.getElementById('date_rapport').value;
        window.location.href = '{{ url("/caissier/rapports/journalier") }}?date=' + date;
    }
    
    function exporterPDF() {
        let date = document.getElementById('date_rapport').value;
        window.open('{{ url("/caissier/rapports/journalier/pdf") }}?date=' + date, '_blank');
    }
    
    function exporterExcel() {
        let date = document.getElementById('date_rapport').value;
        window.location.href = '{{ url("/caissier/rapports/journalier/excel") }}?date=' + date;
    }
</script>
@endpush
@endsection