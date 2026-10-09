@extends('layouts.comptable')

@section('title', 'Modifier écriture')
@section('header-title', '✏️ Modifier l\'écriture')
@section('header-subtitle', 'Modification de l\'écriture comptable')

@section('comptable-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h3 class="card-header-title">Modification de l'écriture</h3>
                <p class="card-header-subtitle">Modifiez les informations de l'écriture N° {{ $ecriture->numero_piece }}</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/comptable/ecritures/' . $ecriture->id) }}" id="ecritureForm">
                @csrf 
                @method('PUT')
                
                <!-- Informations générales -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        <span>Informations générales</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" value="{{ old('date', $ecriture->date->format('Y-m-d')) }}" class="form-control-custom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>Libellé <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="libelle" value="{{ old('libelle', $ecriture->libelle) }}" class="form-control-custom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-1"></i>Client
                            </label>
                            <select name="client_id" class="form-control-custom">
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $ecriture->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-truck text-primary me-1"></i>Fournisseur
                            </label>
                            <select name="fournisseur_id" class="form-control-custom">
                                <option value="">Sélectionner un fournisseur</option>
                                @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $ecriture->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
                                    {{ $fournisseur->nom }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Comptes comptables -->
                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <div class="form-section-debit">
                            <div class="section-title-debit">
                                <i class="fas fa-arrow-down"></i>
                                <span>COMPTE DÉBIT</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Compte comptable <span class="text-danger">*</span></label>
                                <select name="compte_debit" id="compte_debit" class="form-control-custom" required>
                                    <option value="">Sélectionner un compte</option>
                                    @foreach($comptes as $compte)
                                    <option value="{{ $compte->numero }}" {{ old('compte_debit', $ecriture->compte_debit) == $compte->numero ? 'selected' : '' }}>
                                        {{ $compte->numero }} - {{ $compte->nom }} ({{ $compte->type }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label fw-semibold">Montant (FC) <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-group-text">FC</span>
                                    <input type="number" name="montant_debit" id="montant_debit" value="{{ old('montant_debit', $ecriture->montant_debit) }}" class="form-control-custom" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-section-credit">
                            <div class="section-title-credit">
                                <i class="fas fa-arrow-up"></i>
                                <span>COMPTE CRÉDIT</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Compte comptable <span class="text-danger">*</span></label>
                                <select name="compte_credit" id="compte_credit" class="form-control-custom" required>
                                    <option value="">Sélectionner un compte</option>
                                    @foreach($comptes as $compte)
                                    <option value="{{ $compte->numero }}" {{ old('compte_credit', $ecriture->compte_credit) == $compte->numero ? 'selected' : '' }}>
                                        {{ $compte->numero }} - {{ $compte->nom }} ({{ $compte->type }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label fw-semibold">Montant (FC) <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-group-text">FC</span>
                                    <input type="number" name="montant_credit" id="montant_credit" value="{{ old('montant_credit', $ecriture->montant_credit) }}" class="form-control-custom" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerte équilibre -->
                <div id="equilibreAlert" class="alert d-none mt-3"></div>

                <!-- Informations supplémentaires -->
                <div class="form-section mt-3">
                    <div class="section-title">
                        <i class="fas fa-history"></i>
                        <span>Informations de création</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-readonly">
                                <label><i class="fas fa-user-check me-1"></i> Créé par</label>
                                <p>{{ $ecriture->saisiPar->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-readonly">
                                <label><i class="fas fa-calendar-plus me-1"></i> Date de création</label>
                                <p>{{ $ecriture->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @if($ecriture->valide_par)
                        <div class="col-md-6">
                            <div class="info-readonly">
                                <label><i class="fas fa-check-circle me-1"></i> Validé par</label>
                                <p>{{ $ecriture->validePar->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-readonly">
                                <label><i class="fas fa-calendar-check me-1"></i> Date de validation</label>
                                <p>{{ $ecriture->valide_le->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ url('/comptable/ecritures') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-icon {
        width: 45px;
        height: 45px;
        background: rgba(255,255,255,0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    
    .card-header-title {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    
    .card-header-subtitle {
        font-size: 11px;
        margin: 4px 0 0;
        color: rgba(255,255,255,0.8);
    }
    
    .form-section {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
    }
    
    .form-section-debit {
        background: #ecfdf5;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #a7f3d0;
    }
    
    .form-section-credit {
        background: #fef2f2;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #fecaca;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .section-title i {
        color: #f59e0b;
        font-size: 18px;
    }
    
    .section-title-debit {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #059669;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #a7f3d0;
    }
    
    .section-title-debit i {
        color: #10b981;
        font-size: 18px;
    }
    
    .section-title-credit {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #dc2626;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #fecaca;
    }
    
    .section-title-credit i {
        color: #ef4444;
        font-size: 18px;
    }
    
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control-custom {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }
    
    .form-control-custom:focus {
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
    }
    
    .input-group-custom {
        display: flex;
        align-items: center;
    }
    
    .input-group-text {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 12px 16px;
        border-radius: 12px 0 0 12px;
        font-weight: 600;
        color: #64748b;
    }
    
    .input-group-custom .form-control-custom {
        border-radius: 0 12px 12px 0;
        border-left: none;
    }
    
    .info-readonly {
        background: white;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .info-readonly label {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        display: block;
        margin-bottom: 5px;
    }
    
    .info-readonly p {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }
    
    .alert-success {
        background: #d1fae5;
        border: none;
        color: #065f46;
        border-radius: 12px;
        padding: 15px;
    }
    
    .alert-danger {
        background: #fee2e2;
        border: none;
        color: #991b1b;
        border-radius: 12px;
        padding: 15px;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    }
    
    /* Loading state */
    .btn-submit.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .btn-submit.loading i {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .btn-cancel {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 12px 28px;
        border-radius: 12px;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-cancel:hover {
        background: #f8fafc;
        color: #1e293b;
    }
    
    .text-danger {
        color: #ef4444;
    }
    
    .text-primary {
        color: #f59e0b;
    }
    
    .text-success {
        color: #10b981;
    }
    
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
        .btn-submit, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
        .card-header-custom {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const debit = document.getElementById('montant_debit');
    const credit = document.getElementById('montant_credit');
    const alertDiv = document.getElementById('equilibreAlert');
    const submitBtn = document.getElementById('submitBtn');
    
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
        checkEquilibre();
    }
    
    // Effet de chargement lors de la soumission
    document.getElementById('ecritureForm')?.addEventListener('submit', function(e) {
        let d = parseFloat(debit?.value) || 0;
        let c = parseFloat(credit?.value) || 0;
        
        if(d !== c) {
            e.preventDefault();
            alert('❌ Le montant débit doit être égal au montant crédit !');
        } else {
            // Afficher l'effet de chargement
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Mise à jour en cours...';
            submitBtn.disabled = true;
        }
    });
</script>
@endpush
@endsection