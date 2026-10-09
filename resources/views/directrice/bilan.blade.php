@extends('layouts.directrice')

@section('title', 'Bilan comptable')
@section('header-title', '📊 Bilan comptable')
@section('header-subtitle', 'Situation financière')

@section('directrice-content')
<div class="fade-up">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-primary text-white"><h3 class="mb-0 text-white"><i class="fas fa-chart-line me-2"></i>ACTIF</h3></div>
                <div class="p-4">
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3"><span>Actif immobilisé</span><span class="fw-bold">{{ number_format($bilan->actif_immobilise ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3"><span>Actif circulant</span><span class="fw-bold">{{ number_format($bilan->actif_circulant ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3"><span>Trésorerie</span><span class="fw-bold">{{ number_format($bilan->tresorerie ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between pt-3 fw-bold fs-5 text-primary"><span>TOTAL ACTIF</span><span>{{ number_format($bilan->total_actif ?? 0, 0, ',', ' ') }} FC</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header bg-success text-white"><h3 class="mb-0 text-white"><i class="fas fa-chart-pie me-2"></i>PASSIF</h3></div>
                <div class="p-4">
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3"><span>Capitaux propres</span><span class="fw-bold">{{ number_format($bilan->capitaux_propres ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3"><span>Dettes</span><span class="fw-bold">{{ number_format($bilan->dettes ?? 0, 0, ',', ' ') }} FC</span></div>
                    <div class="d-flex justify-content-between pt-3 fw-bold fs-5 text-success"><span>TOTAL PASSIF</span><span>{{ number_format($bilan->total_passif ?? 0, 0, ',', ' ') }} FC</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection