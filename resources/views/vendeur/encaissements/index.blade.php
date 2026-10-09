@extends('layouts.vendeur')

@section('title', 'Encaissements')
@section('header-title', '💵 Encaissements')
@section('header-subtitle', 'Enregistrer un paiement client')

@section('vendeur-content')
<div class="fade-up">

    <div class="row g-4">
        <div class="col-md-5">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-hand-holding-usd me-2"></i>Nouvel encaissement</h3></div>
                <div class="p-4">
                    <form method="POST" action="{{ route('vendeur.encaissements.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Facture <span class="text-danger">*</span></label>
                            <select name="facture_id" id="facture_id" class="form-control-custom" required>
                                <option value="">Sélectionner une facture</option>
                                @foreach($facturesImpayees as $facture)
                                <option value="{{ $facture->id }}" data-montant="{{ $facture->montant_ttc }}">
                                    {{ $facture->numero }} - {{ $facture->client->nom }} ({{ number_format($facture->montant_ttc, 0, ',', ' ') }} FC)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Montant à encaisser (FC) <span class="text-danger">*</span></label>
                            <input type="number" name="montant" id="montant" class="form-control-custom" step="0.01" required>
                            <small id="montant_restant" class="text-muted"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control-custom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mode de paiement</label>
                            <select name="mode_paiement" class="form-control-custom">
                                <option value="espece">Espèces</option>
                                <option value="banque">Virement bancaire</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Référence</label>
                            <input type="text" name="reference" class="form-control-custom" placeholder="N° chèque, bordereau...">
                        </div>
                        <button type="submit" class="btn-primary-custom w-100"><i class="fas fa-save me-2"></i>Enregistrer l'encaissement</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-clock me-2"></i>Factures impayées</h3></div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>N° Facture</th><th>Client</th><th>Date</th><th>Montant</th></tr></thead>
                        <tbody>
                            @forelse($facturesImpayees as $facture)
                            <tr>
                                <td>{{ $facture->numero }}</span>
                                <td>{{ $facture->client->nom }}</span>
                                <td>{{ $facture->date->format('d/m/Y') }}</span>
                                <td class="text-danger fw-bold">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FC</span>
                            </tr>
                            @empty<td colspan="4" class="text-center py-3">Aucune facture impayée</span>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const factureSelect = document.getElementById('facture_id');
    const montantInput = document.getElementById('montant');
    const montantRestant = document.getElementById('montant_restant');

    factureSelect.addEventListener('change', function() {
        let option = this.options[this.selectedIndex];
        let montantTotal = option.dataset.montant || 0;
        montantRestant.innerHTML = `Montant restant : ${parseInt(montantTotal).toLocaleString('fr-FR')} FC`;
        montantInput.max = montantTotal;
    });
</script>
@endpush
@endsection