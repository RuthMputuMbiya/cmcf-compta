@extends('layouts.vendeur')

@section('title', 'Facture')
@section('header-title', '🖨️ Facture')
@section('header-subtitle', 'Détail de la facture')

@section('vendeur-content')
<div class="fade-up">
    <div class="facture-container">
        <!-- En-tête facture -->
        <div class="facture-header text-center mb-4">
            <h2 class="mb-1">CMCF CONSULTING-FORMATION SASU</h2>
            <p>Lubumbashi, RDC | Tél: +243 812 212 290</p>
            <h3 class="text-primary mt-3">FACTURE N° {{ $facture->numero }}</h3>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-box"><strong>Date d'émission :</strong> {{ $facture->date->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-6">
                <div class="info-box"><strong>Client :</strong> {{ $facture->client->nom }}<br>{{ $facture->client->adresse ?? '' }}<br>Tél: {{ $facture->client->telephone ?? '' }}</div>
            </div>
        </div>

        <table class="data-table mb-4">
            <thead><tr><th>Désignation</th><th>Montant HT (FC)</th><th>TVA (%)</th><th>Montant TTC (FC)</th></tr></thead>
            <tbody>
                <tr><td>{{ $facture->description ?? 'Vente de produits/services' }}</td><td>{{ number_format($facture->montant_ht, 0, ',', ' ') }}</td><td>{{ $facture->tva }}%</td><td class="fw-bold">{{ number_format($facture->montant_ttc, 0, ',', ' ') }}</td></tr>
            </tbody>
            <tfoot class="bg-light">
                <tr><td colspan="3" class="text-end fw-bold">TOTAL TTC :</td><td class="fw-bold text-primary fs-5">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FC</td></tr>
            </tfoot>
        </table>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="info-box"><strong>Statut :</strong> @if($facture->statut == 'payee')<span class="badge-success">Payée</span>@else<span class="badge-danger">Impayée</span>@endif</div>
            </div>
            <div class="col-md-6 text-end">
                <div class="signature-line">
                    <p>Fait à Lubumbashi, le {{ $facture->date->format('d/m/Y') }}</p>
                    <p>Signature du client</p>
                    <div class="mt-4">_________________</div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4">
            <a href="{{ route('vendeur.factures') }}" class="btn btn-light px-4 rounded-3"><i class="fas fa-arrow-left me-2"></i>Retour</a>
            <button onclick="window.print()" class="btn-primary-custom"><i class="fas fa-print me-2"></i>Imprimer</button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .facture-container { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .facture-header { border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; }
    .signature-line { border-top: 1px solid #cbd5e1; padding-top: 20px; margin-top: 20px; }
    @media print { .btn, .btn-primary-custom, .btn-light, .d-flex.justify-content-end { display: none; } .facture-container { padding: 0; } }
</style>
@endpush
@endsection