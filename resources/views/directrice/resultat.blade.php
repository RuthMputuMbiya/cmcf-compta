@extends('layouts.directrice')

@section('title', 'Compte de résultat')
@section('header-title', '📉 Compte de résultat')
@section('header-subtitle', 'Performance financière')

@section('directrice-content')
<div class="fade-up">

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-gradient-to-r from-purple-50 to-purple-100">
        <div class="card-body p-4 text-center">
            <h4 class="text-muted mb-2">Résultat net</h4>
            <h1 class="{{ ($resultat->resultat_net ?? 0) >= 0 ? 'text-success' : 'text-danger' }} display-4 fw-bold">
                {{ number_format(abs($resultat->resultat_net ?? 0), 0, ',', ' ') }} FC
            </h1>
            <p><i class="fas {{ ($resultat->resultat_net ?? 0) >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger' }} me-1"></i>{{ ($resultat->resultat_net ?? 0) >= 0 ? 'Bénéfice' : 'Perte' }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-success text-white"><h3 class="mb-0 text-white"><i class="fas fa-plus-circle me-2"></i>PRODUITS</h3></div>
                <div class="p-4">
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span>Chiffre d'affaires</span><span class="fw-bold">{{ number_format($resultat->chiffre_affaires ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span>Autres produits</span><span class="fw-bold">{{ number_format($resultat->autres_produits ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between pt-3 fw-bold fs-5 text-success"><span>TOTAL PRODUITS</span><span>{{ number_format($resultat->total_produits ?? 0, 0, ',', ' ') }} FC</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-danger text-white"><h3 class="mb-0 text-white"><i class="fas fa-minus-circle me-2"></i>CHARGES</h3></div>
                <div class="p-4">
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span>Achats</span><span class="fw-bold">{{ number_format($resultat->achats ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span>Charges personnel</span><span class="fw-bold">{{ number_format($resultat->charges_personnel ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><span>Charges externes</span><span class="fw-bold">{{ number_format($resultat->charges_externes ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between pt-3 fw-bold fs-5 text-danger"><span>TOTAL CHARGES</span><span>{{ number_format($resultat->total_charges ?? 0, 0, ',', ' ') }} FC</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection