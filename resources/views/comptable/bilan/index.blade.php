@extends('layouts.comptable')

@section('title', 'Bilan comptable')
@section('header-title', '📊 Bilan comptable')
@section('header-subtitle', 'Situation financière de l\'entreprise')

@section('comptable-content')
<div class="fade-up">
    
    <!-- Filtre par date -->
    <div class="filters-card mb-4">
        <div class="filters-header">
            <div class="filters-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <h3 class="filters-title">Période du bilan</h3>
                <p class="filters-subtitle">Sélectionnez la date de clôture</p>
            </div>
        </div>
        <div class="filters-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Date de clôture</label>
                    <input type="date" id="date_bilan" class="form-control-custom" value="{{ $dateBilan ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button onclick="filterBilan()" class="btn-filter w-100">
                        <i class="fas fa-search me-2"></i>Actualiser
                    </button>
                </div>
                <div class="col-md-3">
                    <button onclick="exporterPDF()" class="btn-danger-custom w-100">
                        <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultat de l'exercice -->
    <div class="alert {{ $resultatNet >= 0 ? 'alert-success' : 'alert-danger' }} rounded-4 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-{{ $resultatNet >= 0 ? 'chart-line' : 'exclamation-triangle' }} fa-2x me-3"></i>
                <strong>Résultat de l'exercice :</strong>
                {{ number_format(abs($resultatNet), 0, ',', ' ') }} FC
                @if($resultatNet >= 0)
                <span class="badge-success ms-2">BÉNÉFICE</span>
                @else
                <span class="badge-danger ms-2">PERTE</span>
                @endif
            </div>
            <div class="text-end">
                <small>Clôture au {{ \Carbon\Carbon::parse($dateBilan ?? now())->format('d/m/Y') }}</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- ==================== ACTIF ==================== -->
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-primary text-white">
                    <h3 class="mb-0 text-white"><i class="fas fa-chart-line me-2"></i>ACTIF</h3>
                </div>
                <div class="p-0">
                    <!-- Actif immobilisé -->
                    <div class="bilan-section">
                        <div class="section-title">
                            <i class="fas fa-building me-2"></i> Actif immobilisé
                        </div>
                        <table class="bilan-table">
                            <thead>
                                <tr><th>N° Compte</th><th>Libellé</th><th class="text-right">Montant (FC)</th></tr>
                            </thead>
                            <tbody>
                                @forelse($actifImmobiliseDetails ?? [] as $detail)
                                <tr>
                                    <td>{{ $detail->compte }}</td>
                                    <td>{{ $detail->nom_compte }}</td>
                                    <td class="text-right">{{ number_format($detail->solde, 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Aucun compte d'immobilisation</td></tr>
                                @endforelse
                                <tr class="table-total">
                                    <td colspan="2" class="fw-bold">TOTAL ACTIF IMMOBILISÉ</td>
                                    <td class="text-right fw-bold text-primary">{{ number_format($totalActifImmobilise ?? 0, 0, ',', ' ') }} FC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Actif circulant (Stocks + Créances) -->
                    <div class="bilan-section">
                        <div class="section-title">
                            <i class="fas fa-chart-simple me-2"></i> Actif circulant
                        </div>
                        
                        <!-- Sous-section Stocks -->
                        <div class="subsection-title">Stocks (Classe 3)</div>
                        <table class="bilan-table">
                            <thead>
                                <tr><th>N° Compte</th><th>Libellé</th><th class="text-right">Montant (FC)</th></tr>
                            </thead>
                            <tbody>
                                @php $stocksFound = false; @endphp
                                @foreach($actifCirculantDetails ?? [] as $detail)
                                    @if(substr($detail->compte, 0, 1) == '3')
                                        @php $stocksFound = true; @endphp
                                        <tr>
                                            <td>{{ $detail->compte }}</td>
                                            <td>{{ $detail->nom_compte }}</td>
                                            <td class="text-right">{{ number_format($detail->solde, 0, ',', ' ') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$stocksFound)
                                <tr><td colspan="3" class="text-center text-muted">Aucun stock</td></tr>
                                @endif
                                <tr class="table-total">
                                    <td colspan="2" class="fw-bold">TOTAL STOCKS</td>
                                    <td class="text-right fw-bold">{{ number_format($totalStocks ?? 0, 0, ',', ' ') }} FC</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Sous-section Créances (41, 46, 47, 48, etc.) -->
                        <div class="subsection-title mt-3">Créances (Clients, débiteurs)</div>
                        <table class="bilan-table">
                            <thead>
                                <tr><th>N° Compte</th><th>Libellé</th><th class="text-right">Montant (FC)</th></tr>
                            </thead>
                            <tbody>
                                @php $creancesFound = false; @endphp
                                @foreach($actifCirculantDetails ?? [] as $detail)
                                    @if(substr($detail->compte, 0, 1) == '4')
                                        @php $creancesFound = true; @endphp
                                        <tr>
                                            <td>{{ $detail->compte }}</td>
                                            <td>{{ $detail->nom_compte }}</td>
                                            <td class="text-right">{{ number_format($detail->solde, 0, ',', ' ') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$creancesFound)
                                <tr><td colspan="3" class="text-center text-muted">Aucune créance (compte 41, 46, 47, 48)</td></tr>
                                @endif
                                <tr class="table-total">
                                    <td colspan="2" class="fw-bold">TOTAL CRÉANCES</td>
                                    <td class="text-right fw-bold text-primary">{{ number_format($totalCreances ?? 0, 0, ',', ' ') }} FC</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Total Actif Circulant -->
                        <div class="bilan-total-general" style="background: #dbeafe; margin: 10px 0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">TOTAL ACTIF CIRCULANT</span>
                                <span class="fw-bold text-primary">{{ number_format($totalActifCirculant ?? 0, 0, ',', ' ') }} FC</span>
                            </div>
                        </div>
                    </div>

                    <!-- Trésorerie Actif -->
                    <div class="bilan-section">
                        <div class="section-title">
                            <i class="fas fa-coins me-2 text-success"></i> Trésorerie Actif
                            <small class="text-muted ms-2">(Banques créditrices, Caisse)</small>
                        </div>
                        <table class="bilan-table">
                            <thead>
                                <tr><th>N° Compte</th><th>Libellé</th><th class="text-right">Montant (FC)</th></tr>
                            </thead>
                            <tbody>
                                @forelse($tresorerieActifDetails ?? [] as $detail)
                                <tr>
                                    <td>{{ $detail->compte }}</td>
                                    <td>{{ $detail->nom_compte }}</td>
                                    <td class="text-right">{{ number_format($detail->solde, 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Aucune trésorerie active</td></tr>
                                @endforelse
                                <tr class="table-total">
                                    <td colspan="2" class="fw-bold">TOTAL TRÉSORERIE ACTIF</td>
                                    <td class="text-right fw-bold text-primary">{{ number_format($totalTresorerieActif ?? 0, 0, ',', ' ') }} FC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Général Actif -->
                    <div class="bilan-total-general">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">TOTAL GÉNÉRAL ACTIF</span>
                            <span class="fw-bold fs-4 text-primary">{{ number_format($totalGeneralActif ?? 0, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== PASSIF ==================== -->
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-success text-white">
                    <h3 class="mb-0 text-white"><i class="fas fa-chart-pie me-2"></i>PASSIF</h3>
                </div>
                <div class="p-0">
                    <!-- Capitaux propres -->
                    <div class="bilan-section">
                        <div class="section-title">
                            <i class="fas fa-arrow-down me-2"></i> CAPITAUX PROPRES (Classe 1)
                        </div>
                        <table class="bilan-table">
                            <thead>
                                <tr><th>N° Compte</th><th>Libellé</th><th class="text-right">Montant (FC)</th></tr>
                            </thead>
                            <tbody>
                                @forelse($capitauxProporesDetails ?? [] as $detail)
                                <tr>
                                    <td>{{ $detail->compte }}</td>
                                    <td>{{ $detail->nom_compte }}</td>
                                    <td class="text-right">{{ number_format($detail->solde, 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Aucun compte</td></tr>
                                @endforelse
                                <tr class="table-total">
                                    <td colspan="2" class="fw-bold">TOTAL CAPITAUX PROPRES</td>
                                    <td class="text-right fw-bold text-success">{{ number_format($totalCapitauxPropores ?? 0, 0, ',', ' ') }} FC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Passif circulant (Dettes) -->
                    <div class="bilan-section">
                        <div class="section-title">
                            <i class="fas fa-chart-line me-2"></i> PASSIF CIRCULANT
                            <small class="text-muted ms-2">(Fournisseurs, dettes fiscales, sociales)</small>
                        </div>
                        <table class="bilan-table">
                            <thead>
                                <tr><th>N° Compte</th><th>Libellé</th><th class="text-right">Montant (FC)</th></tr>
                            </thead>
                            <tbody>
                                @forelse($passifCirculantDetails ?? [] as $detail)
                                <tr>
                                    <td>{{ $detail->compte }}</td>
                                    <td>{{ $detail->nom_compte }}</td>
                                    <td class="text-right">{{ number_format($detail->solde, 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Aucune dette circulante</td></tr>
                                @endforelse
                                <tr class="table-total">
                                    <td colspan="2" class="fw-bold">TOTAL PASSIF CIRCULANT</td>
                                    <td class="text-right fw-bold text-success">{{ number_format($totalPassifCirculant ?? 0, 0, ',', ' ') }} FC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Trésorerie Passif (Découverts) -->
                    <div class="bilan-section">
                        <div class="section-title">
                            <i class="fas fa-coins me-2 text-danger"></i> Trésorerie Passif
                            <small class="text-muted ms-2">(Découverts bancaires)</small>
                        </div>
                        <table class="bilan-table">
                            <thead>
                                <tr><th>N° Compte</th><th>Libellé</th><th class="text-right">Montant (FC)</th></tr>
                            </thead>
                            <tbody>
                                @forelse($tresoreriePassifDetails ?? [] as $detail)
                                <tr>
                                    <td>{{ $detail->compte }}</td>
                                    <td>{{ $detail->nom_compte }}</td>
                                    <td class="text-right">{{ number_format($detail->solde, 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Aucun découvert bancaire</td></tr>
                                @endforelse
                                <tr class="table-total">
                                    <td colspan="2" class="fw-bold">TOTAL TRÉSORERIE PASSIF</td>
                                    <td class="text-right fw-bold text-success">{{ number_format($totalTresoreriePassif ?? 0, 0, ',', ' ') }} FC</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Général Passif -->
                    <div class="bilan-total-general">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">TOTAL GÉNÉRAL PASSIF</span>
                            <span class="fw-bold fs-4 text-success">{{ number_format($totalGeneralPassif ?? 0, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>
                </div>
            </div>
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
    
    .filters-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .filters-header {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .filters-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .filters-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    .filters-subtitle {
        font-size: 11px;
        margin: 2px 0 0;
        color: rgba(255,255,255,0.8);
    }
    .filters-body {
        padding: 20px;
    }
    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
    }
    .btn-filter {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
    }
    .btn-danger-custom {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-danger-custom:hover, .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .data-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        height: 100%;
    }
    .data-header {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .bilan-section {
        border-bottom: 1px solid #e2e8f0;
    }
    .section-title {
        background: #f1f5f9;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 14px;
        color: #1e293b;
        border-left: 4px solid #3b82f6;
    }
    .subsection-title {
        background: #f8fafc;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 12px;
        color: #475569;
        border-left: 3px solid #10b981;
        margin-top: 5px;
    }
    .bilan-table {
        width: 100%;
        border-collapse: collapse;
    }
    .bilan-table th {
        background: #f8fafc;
        padding: 8px 15px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    .bilan-table td {
        padding: 8px 15px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12px;
    }
    .bilan-table tr:hover td {
        background: #f8fafc;
    }
    .table-total {
        background: #f8fafc;
        font-weight: bold;
    }
    .table-total td {
        padding: 10px 15px;
        border-top: 1px solid #e2e8f0;
    }
    .bilan-total-general {
        background: #e0e7ff;
        padding: 15px 20px;
        margin: 10px 0;
    }
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: none;
    }
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: none;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    @media (max-width: 768px) {
        .bilan-table th, .bilan-table td {
            padding: 6px 10px;
            font-size: 10px;
        }
        .section-title {
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function filterBilan() {
        let date = document.getElementById('date_bilan').value;
        let url = '{{ url("/comptable/bilan") }}';
        if(date) url += '?date=' + date;
        window.location.href = url;
    }
    
    function exporterPDF() {
        let date = document.getElementById('date_bilan').value;
        let url = '{{ url("/comptable/export/bilan/pdf") }}';
        if(date) url += '?date=' + date;
        window.open(url, '_blank');
    }
</script>
@endpush
@endsection