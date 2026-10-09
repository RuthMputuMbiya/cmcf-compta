@extends('layouts.vendeur')

@section('title', 'Modifier facture')
@section('header-title', '✏️ Modifier la facture')
@section('header-subtitle', 'Modification de la facture N° {{ $facture->numero }}')

@section('vendeur-content')
<div class="fade-up">
    <div class="data-card">
        <div class="p-4">
            <form method="POST" action="{{ route('vendeur.factures.update', $facture->id) }}" id="factureForm">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">N° Facture</label>
                        <input type="text" value="{{ $facture->numero }}" class="form-control-custom" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" value="{{ $facture->date->format('Y-m-d') }}" class="form-control-custom" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-control-custom" required>
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ $facture->client_id == $client->id ? 'selected' : '' }}>
                                {{ $client->nom }} - {{ $client->telephone }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Montant HT (FC) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_ht" id="montant_ht" value="{{ $facture->montant_ht }}" class="form-control-custom" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">TVA (%)</label>
                        <input type="number" name="tva" id="tva" value="{{ $facture->tva }}" class="form-control-custom" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Montant TTC (FC)</label>
                        <input type="text" id="montant_ttc" value="{{ number_format($facture->montant_ttc, 0, ',', ' ') }}" class="form-control-custom" readonly style="background: #f1f5f9;">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description / Observations</label>
                        <textarea name="description" class="form-control-custom" rows="3">{{ $facture->description }}</textarea>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded-3">
                    <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Récapitulatif</h5>
                    <div class="row">
                        <div class="col-md-4"><strong>Montant HT :</strong> <span id="recap_ht">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FC</span></div>
                        <div class="col-md-4"><strong>TVA :</strong> <span id="recap_tva">{{ number_format($facture->montant_ht * $facture->tva / 100, 0, ',', ' ') }} FC</span></div>
                        <div class="col-md-4"><strong class="text-primary">Total TTC :</strong> <strong id="recap_ttc" class="text-primary">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FC</strong></div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-3">
                    <a href="{{ route('vendeur.factures') }}" class="btn btn-light px-4 rounded-3">Annuler</a>
                    <button type="submit" class="btn-primary-custom"><i class="fas fa-save me-2"></i>Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const htInput = document.getElementById('montant_ht');
    const tvaInput = document.getElementById('tva');
    const ttcField = document.getElementById('montant_ttc');
    const recapHt = document.getElementById('recap_ht');
    const recapTva = document.getElementById('recap_tva');
    const recapTtc = document.getElementById('recap_ttc');

    function calculerTTC() {
        let ht = parseFloat(htInput.value) || 0;
        let tva = parseFloat(tvaInput.value) || 0;
        let ttc = ht + (ht * tva / 100);
        ttcField.value = ttc.toLocaleString('fr-FR') + ' FC';
        recapHt.innerHTML = ht.toLocaleString('fr-FR') + ' FC';
        recapTva.innerHTML = (ht * tva / 100).toLocaleString('fr-FR') + ' FC';
        recapTtc.innerHTML = ttc.toLocaleString('fr-FR') + ' FC';
    }

    htInput.addEventListener('input', calculerTTC);
    tvaInput.addEventListener('input', calculerTTC);
</script>
@endpush
@endsection