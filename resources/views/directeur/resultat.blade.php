@extends('layouts.directeur')

@section('title', 'Compte de résultat')
@section('header-title', '📉 COMPTE DE RÉSULTAT')
@section('header-subtitle', 'Performance financière de l\'entreprise')

@section('directeur-content')
<div class="fade-up">
    
    <!-- Filtre par date -->
    <div class="filters-card mb-4">
        <div class="filters-header">
            <div class="filters-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <h3 class="filters-title">Période du compte de résultat</h3>
                <p class="filters-subtitle">Sélectionnez la période</p>
            </div>
        </div>
        <div class="filters-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Date de début</label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ $dateDebut ?? date('Y-m-01') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de fin</label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ $dateFin ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <button onclick="filterResultat()" class="btn-filter w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
                <div class="col-md-2">
                    <button onclick="exporterPDF()" class="btn-pdf-custom w-100">
                        <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultat net -->
    <div class="resultat-card mb-4">
        <div class="resultat-card-body">
            <h4 class="resultat-label">RÉSULTAT NET DE L'EXERCICE</h4>
            <h1 class="resultat-montant {{ $resultatNet >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format(abs($resultatNet), 0, ',', ' ') }} FC
            </h1>
            <p class="resultat-type">
                <i class="fas {{ $resultatNet >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger' }} me-1"></i>
                {{ $resultatNet >= 0 ? 'BÉNÉFICE' : 'PERTE' }}
            </p>
            <p class="resultat-periode">Période du {{ \Carbon\Carbon::parse($dateDebut ?? date('Y-m-01'))->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin ?? date('Y-m-d'))->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- ==================== CHARGES ==================== -->
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-danger">
                    <h3 class="data-header-title"><i class="fas fa-minus-circle me-2"></i>CHARGES</h3>
                </div>
                <div class="data-body">
                    
                    <!-- Charges d'exploitation -->
                    <div class="resultat-section">
                        <div class="section-title section-title-danger">
                            <i class="fas fa-chart-line me-2"></i> Charges d'exploitation
                        </div>
                        <div class="table-responsive">
                            <table class="resultat-table">
                                <thead>
                                    <tr>
                                        <th>N° Compte</th>
                                        <th>Libellé</th>
                                        <th class="text-right">Montant (FC)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chargesExploitationDetails as $detail)
                                    <tr>
                                        <td><span class="compte-badge">{{ $detail->compte }}</span></td>
                                        <td>{{ $detail->nom_compte }}</td>
                                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Aucune charge d'exploitation</td>
                                    </tr>
                                    @endforelse
                                    <tr class="table-total">
                                        <td colspan="2" class="fw-bold">TOTAL CHARGES D'EXPLOITATION</td>
                                        <td class="text-right fw-bold text-danger">{{ number_format($totalChargesExploitation, 0, ',', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Charges financières -->
                    <div class="resultat-section">
                        <div class="section-title section-title-danger">
                            <i class="fas fa-chart-pie me-2"></i> Charges financières
                        </div>
                        <div class="table-responsive">
                            <table class="resultat-table">
                                <thead>
                                    <tr>
                                        <th>N° Compte</th>
                                        <th>Libellé</th>
                                        <th class="text-right">Montant (FC)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chargesFinancieresDetails as $detail)
                                    <tr>
                                        <td><span class="compte-badge">{{ $detail->compte }}</span></td>
                                        <td>{{ $detail->nom_compte }}</td>
                                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Aucune charge financière</td>
                                    </tr>
                                    @endforelse
                                    <tr class="table-total">
                                        <td colspan="2" class="fw-bold">TOTAL CHARGES FINANCIÈRES</td>
                                        <td class="text-right fw-bold text-danger">{{ number_format($totalChargesFinancieres, 0, ',', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Charges exceptionnelles -->
                    <div class="resultat-section">
                        <div class="section-title section-title-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i> Charges exceptionnelles
                        </div>
                        <div class="table-responsive">
                            <table class="resultat-table">
                                <thead>
                                    <tr>
                                        <th>N° Compte</th>
                                        <th>Libellé</th>
                                        <th class="text-right">Montant (FC)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chargesExceptionnellesDetails as $detail)
                                    <tr>
                                        <td><span class="compte-badge">{{ $detail->compte }}</span></td>
                                        <td>{{ $detail->nom_compte }}</td>
                                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Aucune charge exceptionnelle</td>
                                    </tr>
                                    @endforelse
                                    <tr class="table-total">
                                        <td colspan="2" class="fw-bold">TOTAL CHARGES EXCEPTIONNELLES</td>
                                        <td class="text-right fw-bold text-danger">{{ number_format($totalChargesExceptionnelles, 0, ',', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TOTAL GENERAL CHARGES -->
                    <div class="resultat-total-general resultat-total-danger">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">TOTAL GÉNÉRAL DES CHARGES</span>
                            <span class="fw-bold fs-4 text-danger">{{ number_format($totalGeneralCharges, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>

                    <!-- Bénéfice (si présent) -->
                    @if($resultatNet > 0)
                    <div class="resultat-benefice">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><i class="fas fa-plus-circle me-2 text-success"></i>Bénéfice de l'exercice</span>
                            <span class="fw-bold text-success">{{ number_format($resultatNet, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ==================== PRODUITS ==================== -->
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-success">
                    <h3 class="data-header-title"><i class="fas fa-plus-circle me-2"></i>PRODUITS</h3>
                </div>
                <div class="data-body">
                    
                    <!-- Produits d'exploitation -->
                    <div class="resultat-section">
                        <div class="section-title section-title-success">
                            <i class="fas fa-chart-line me-2"></i> Produits d'exploitation
                        </div>
                        <div class="table-responsive">
                            <table class="resultat-table">
                                <thead>
                                    <tr>
                                        <th>N° Compte</th>
                                        <th>Libellé</th>
                                        <th class="text-right">Montant (FC)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($produitsExploitationDetails as $detail)
                                    <tr>
                                        <td><span class="compte-badge">{{ $detail->compte }}</span></td>
                                        <td>{{ $detail->nom_compte }}</td>
                                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Aucun produit d'exploitation</td>
                                    </tr>
                                    @endforelse
                                    <tr class="table-total">
                                        <td colspan="2" class="fw-bold">TOTAL PRODUITS D'EXPLOITATION</td>
                                        <td class="text-right fw-bold text-success">{{ number_format($totalProduitsExploitation, 0, ',', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Produits financiers -->
                    <div class="resultat-section">
                        <div class="section-title section-title-success">
                            <i class="fas fa-chart-pie me-2"></i> Produits financiers
                        </div>
                        <div class="table-responsive">
                            <table class="resultat-table">
                                <thead>
                                    <tr>
                                        <th>N° Compte</th>
                                        <th>Libellé</th>
                                        <th class="text-right">Montant (FC)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($produitsFinanciersDetails as $detail)
                                    <tr>
                                        <td><span class="compte-badge">{{ $detail->compte }}</span></td>
                                        <td>{{ $detail->nom_compte }}</td>
                                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Aucun produit financier</td>
                                    </tr>
                                    @endforelse
                                    <tr class="table-total">
                                        <td colspan="2" class="fw-bold">TOTAL PRODUITS FINANCIERS</td>
                                        <td class="text-right fw-bold text-success">{{ number_format($totalProduitsFinancieres, 0, ',', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Produits exceptionnels -->
                    <div class="resultat-section">
                        <div class="section-title section-title-success">
                            <i class="fas fa-gift me-2"></i> Produits exceptionnels
                        </div>
                        <div class="table-responsive">
                            <table class="resultat-table">
                                <thead>
                                    <tr>
                                        <th>N° Compte</th>
                                        <th>Libellé</th>
                                        <th class="text-right">Montant (FC)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($produitsExceptionnelsDetails as $detail)
                                    <tr>
                                        <td><span class="compte-badge">{{ $detail->compte }}</span></td>
                                        <td>{{ $detail->nom_compte }}</td>
                                        <td class="text-right">{{ number_format($detail->montant, 0, ',', ' ') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Aucun produit exceptionnel</td>
                                    </tr>
                                    @endforelse
                                    <tr class="table-total">
                                        <td colspan="2" class="fw-bold">TOTAL PRODUITS EXCEPTIONNELS</td>
                                        <td class="text-right fw-bold text-success">{{ number_format($totalProduitsExceptionnels, 0, ',', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TOTAL GENERAL PRODUITS -->
                    <div class="resultat-total-general resultat-total-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">TOTAL GÉNÉRAL DES PRODUITS</span>
                            <span class="fw-bold fs-4 text-success">{{ number_format($totalGeneralProduits, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>

                    <!-- Perte (si présente) -->
                    @if($resultatNet < 0)
                    <div class="resultat-perte">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><i class="fas fa-minus-circle me-2 text-danger"></i>Perte de l'exercice</span>
                            <span class="fw-bold text-danger">{{ number_format(abs($resultatNet), 0, ',', ' ') }} FC</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fade-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Filtres */
    .filters-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 20px; }
    .filters-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 12px 20px; display: flex; align-items: center; gap: 12px; }
    .filters-icon { width: 35px; height: 35px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; }
    .filters-title { font-size: 14px; font-weight: 600; margin: 0; color: white; }
    .filters-subtitle { font-size: 11px; margin: 2px 0 0; color: rgba(255,255,255,0.8); }
    .filters-body { padding: 20px; }
    .form-label { font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block; }
    .form-control-custom { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; }
    .form-control-custom:focus { border-color: #4f46e5; outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
    .btn-filter { background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none; padding: 10px 16px; border-radius: 10px; color: white; font-weight: 600; font-size: 13px; cursor: pointer; }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79,70,229,0.3); }
    .btn-pdf-custom { background: linear-gradient(135deg, #dc2626, #b91c1c); border: none; padding: 10px 16px; border-radius: 10px; color: white; font-weight: 600; font-size: 13px; cursor: pointer; }
    .btn-pdf-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220,38,38,0.3); }

    /* Carte résultat net */
    .resultat-card { background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 20px; overflow: hidden; margin-bottom: 25px; }
    .resultat-card-body { padding: 25px; text-align: center; }
    .resultat-label { color: #94a3b8; font-size: 13px; font-weight: 600; letter-spacing: 1px; margin-bottom: 10px; }
    .resultat-montant { font-size: 48px; font-weight: 800; margin: 10px 0; }
    .resultat-type { font-size: 14px; font-weight: 600; margin-bottom: 8px; }
    .resultat-periode { color: #64748b; font-size: 12px; margin: 0; }

    /* Data card */
    .data-card { background: white; border-radius: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden; height: 100%; }
    .data-header { padding: 15px 20px; }
    .bg-danger { background: linear-gradient(135deg, #dc2626, #b91c1c); }
    .bg-success { background: linear-gradient(135deg, #059669, #047857); }
    .data-header-title { color: white; font-size: 16px; font-weight: 700; margin: 0; }
    .data-body { padding: 0; }

    /* Sections */
    .resultat-section { border-bottom: 1px solid #e2e8f0; }
    .section-title { padding: 12px 20px; font-weight: 700; font-size: 13px; color: #1e293b; }
    .section-title-danger { background: #fef2f2; border-left: 4px solid #dc2626; }
    .section-title-success { background: #ecfdf5; border-left: 4px solid #059669; }

    /* Tableaux */
    .table-responsive { overflow-x: auto; }
    .resultat-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .resultat-table th { background: #f8fafc; padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: left; }
    .resultat-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .resultat-table tr:hover { background: #fefce8; }
    .table-total { background: #f8fafc; font-weight: 700; }
    .table-total td { padding: 10px 12px; border-top: 1px solid #e2e8f0; border-bottom: none; }

    /* Badges */
    .compte-badge { background: #eef2ff; color: #4f46e5; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; font-family: monospace; display: inline-block; }

    /* Totaux généraux */
    .resultat-total-general { padding: 15px 20px; margin: 12px; border-radius: 12px; }
    .resultat-total-danger { background: #fef2f2; border: 1px solid #fecaca; }
    .resultat-total-success { background: #ecfdf5; border: 1px solid #a7f3d0; }

    /* Bénéfice/Perte */
    .resultat-benefice { background: #d1fae5; padding: 15px 20px; margin: 12px; border-radius: 12px; border: 1px solid #a7f3d0; }
    .resultat-perte { background: #fee2e2; padding: 15px 20px; margin: 12px; border-radius: 12px; border: 1px solid #fecaca; }

    /* Utilitaires */
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-success { color: #059669; }
    .text-danger { color: #dc2626; }
    .text-muted { color: #94a3b8; }
    .fw-bold { font-weight: 700; }
    .fs-4 { font-size: 1.5rem; }
    .fs-5 { font-size: 1.1rem; }

    /* Responsive */
    @media (max-width: 768px) {
        .resultat-montant { font-size: 32px; }
        .resultat-table th, .resultat-table td { padding: 8px 10px; font-size: 11px; }
        .section-title { font-size: 12px; padding: 10px 15px; }
        .data-header-title { font-size: 14px; }
    }

    @media print {
        .filters-card, .btn-pdf-custom { display: none; }
        .data-card { box-shadow: none; border: 1px solid #ccc; page-break-inside: avoid; }
        .resultat-card { background: #f1f5f9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush

@push('scripts')
<script>
    function filterResultat() {
        let dateDebut = document.getElementById('date_debut').value;
        let dateFin = document.getElementById('date_fin').value;
        let url = '{{ url("/directeur/resultat") }}';
        let params = [];
        if(dateDebut) params.push('date_debut=' + dateDebut);
        if(dateFin) params.push('date_fin=' + dateFin);
        if(params.length) url += '?' + params.join('&');
        window.location.href = url;
    }
    
    function exporterPDF() {
        let dateDebut = document.getElementById('date_debut').value;
        let dateFin = document.getElementById('date_fin').value;
        let url = '{{ url("/directeur/export/resultat/pdf") }}';
        let params = [];
        if(dateDebut) params.push('date_debut=' + dateDebut);
        if(dateFin) params.push('date_fin=' + dateFin);
        if(params.length) url += '?' + params.join('&');
        window.open(url, '_blank');
    }
</script>
@endpush
@endsection