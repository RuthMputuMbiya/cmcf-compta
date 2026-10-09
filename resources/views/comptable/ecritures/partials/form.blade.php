@props(['ecriture' => null, 'clients' => [], 'fournisseurs' => [], 'comptes' => []])

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
        <input type="date" name="date" value="{{ old('date', $ecriture?->date->format('Y-m-d') ?? date('Y-m-d')) }}" class="form-control-custom" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
        <input type="text" name="libelle" value="{{ old('libelle', $ecriture?->libelle) }}" class="form-control-custom" required placeholder="Ex: Achat de marchandises">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Client</label>
        <select name="client_id" class="form-control-custom">
            <option value="">Sélectionner un client</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ old('client_id', $ecriture?->client_id) == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Fournisseur</label>
        <select name="fournisseur_id" class="form-control-custom">
            <option value="">Sélectionner un fournisseur</option>
            @foreach($fournisseurs as $fournisseur)
            <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $ecriture?->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>{{ $fournisseur->nom }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mt-4 pt-3 border-top">
    <div class="col-md-6">
        <div class="bg-light p-3 rounded-3">
            <h5 class="text-success mb-3"><i class="fas fa-arrow-down me-2"></i>COMPTE DÉBIT</h5>
            <div class="mb-3">
                <label class="form-label">Compte <span class="text-danger">*</span></label>
                <select name="compte_debit" id="compte_debit" class="form-control-custom" required>
                    <option value="">Sélectionner</option>
                    @foreach($comptes as $compte)
                    <option value="{{ $compte->numero }}" {{ old('compte_debit', $ecriture?->compte_debit) == $compte->numero ? 'selected' : '' }}>
                        {{ $compte->numero }} - {{ $compte->nom }} ({{ $compte->type }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Montant (FC) <span class="text-danger">*</span></label>
                <input type="number" name="montant_debit" id="montant_debit" value="{{ old('montant_debit', $ecriture?->montant_debit) }}" class="form-control-custom" step="0.01" required>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="bg-light p-3 rounded-3">
            <h5 class="text-danger mb-3"><i class="fas fa-arrow-up me-2"></i>COMPTE CRÉDIT</h5>
            <div class="mb-3">
                <label class="form-label">Compte <span class="text-danger">*</span></label>
                <select name="compte_credit" id="compte_credit" class="form-control-custom" required>
                    <option value="">Sélectionner</option>
                    @foreach($comptes as $compte)
                    <option value="{{ $compte->numero }}" {{ old('compte_credit', $ecriture?->compte_credit) == $compte->numero ? 'selected' : '' }}>
                        {{ $compte->numero }} - {{ $compte->nom }} ({{ $compte->type }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Montant (FC) <span class="text-danger">*</span></label>
                <input type="number" name="montant_credit" id="montant_credit" value="{{ old('montant_credit', $ecriture?->montant_credit) }}" class="form-control-custom" step="0.01" required>
            </div>
        </div>
    </div>
</div>

<div id="equilibreAlert" class="alert d-none mt-3"></div>

@push('scripts')
<script>
    const debit = document.getElementById('montant_debit');
    const credit = document.getElementById('montant_credit');
    const alertDiv = document.getElementById('equilibreAlert');
    
    function checkEquilibre() {
        let d = parseFloat(debit?.value) || 0;
        let c = parseFloat(credit?.value) || 0;
        if(d > 0 || c > 0) {
            if(d === c) {
                alertDiv.className = 'alert alert-success mt-3';
                alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>✓ Équilibre parfait : Débit = Crédit';
                alertDiv.classList.remove('d-none');
            } else {
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>⚠ Déséquilibre : Le débit doit être égal au crédit';
                alertDiv.classList.remove('d-none');
            }
        } else {
            alertDiv.classList.add('d-none');
        }
    }
    
    if(debit && credit) {
        debit.addEventListener('input', checkEquilibre);
        credit.addEventListener('input', checkEquilibre);
        if(debit.value || credit.value) checkEquilibre();
    }
</script>
@endpush