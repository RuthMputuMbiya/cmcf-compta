@extends('layouts.comptable')

@section('title', 'Nouvelle écriture')
@section('header-title', '➕ Nouvelle écriture comptable')
@section('header-subtitle', 'Saisie d\'une nouvelle écriture comptable')

@section('comptable-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-pen-alt"></i>
            </div>
            <div>
                <h3 class="card-header-title">Nouvelle écriture</h3>
                <p class="card-header-subtitle">Remplissez les informations de l'écriture comptable</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/comptable/ecritures') }}" id="ecritureForm">
                @csrf
                
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
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control-custom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>Libellé <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="libelle" class="form-control-custom" required placeholder="Ex: Achat de marchandises, Vente de produits...">
                        </div>
                    </div>
                </div>

                <!-- Client et Fournisseur (Optionnels) -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-users"></i>
                        <span>Tiers (Optionnel)</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-1"></i>Client
                            </label>
                            <select name="client_id" class="form-control-custom">
                                <option value="">-- Sélectionner un client (optionnel) --</option>
                                @foreach($clients ?? [] as $client)
                                <option value="{{ $client->id }}">{{ $client->nom }} - {{ $client->telephone ?? 'Tél non renseigné' }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Optionnel - Laissez vide si non applicable</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-truck text-primary me-1"></i>Fournisseur
                            </label>
                            <select name="fournisseur_id" class="form-control-custom">
                                <option value="">-- Sélectionner un fournisseur (optionnel) --</option>
                                @foreach($fournisseurs ?? [] as $fournisseur)
                                <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }} - {{ $fournisseur->telephone ?? 'Tél non renseigné' }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Optionnel - Laissez vide si non applicable</small>
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
                                    @foreach($comptes as $c)
                                    <option value="{{ $c->numero }}">{{ $c->numero }} - {{ $c->nom }} ({{ $c->type }})</option>
                                    @endforeach
                                </select>
                                <div id="intitule_debit" class="intitule-compte">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Sélectionnez un compte pour voir l'intitulé complet</span>
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-semibold">Montant (FC) <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-group-text">FC</span>
                                    <input type="number" name="montant_debit" id="montant_debit" class="form-control-custom" step="0.01" placeholder="0" required>
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
                                    @foreach($comptes as $c)
                                    <option value="{{ $c->numero }}">{{ $c->numero }} - {{ $c->nom }} ({{ $c->type }})</option>
                                    @endforeach
                                </select>
                                <div id="intitule_credit" class="intitule-compte">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Sélectionnez un compte pour voir l'intitulé complet</span>
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-semibold">Montant (FC) <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-group-text">FC</span>
                                    <input type="number" name="montant_credit" id="montant_credit" class="form-control-custom" step="0.01" placeholder="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aperçu des écritures -->
                <div class="preview-section">
                    <div class="section-title">
                        <i class="fas fa-eye"></i>
                        <span>Aperçu de l'écriture</span>
                    </div>
                    <div class="preview-card">
                        <div class="preview-header">
                            <span class="preview-label">Date</span>
                            <span class="preview-label">N° Pièce</span>
                            <span class="preview-label">Libellé</span>
                        </div>
                        <div class="preview-body">
                            <div class="preview-row debit-row">
                                <div class="preview-icon"><i class="fas fa-arrow-down"></i></div>
                                <div class="preview-compte">
                                    <span class="compte-numero" id="preview_debit_numero">---</span>
                                    <span class="compte-nom" id="preview_debit_nom">Compte débiteur</span>
                                </div>
                                <div class="preview-montant" id="preview_debit_montant">0 FC</div>
                            </div>
                            <div class="preview-row credit-row">
                                <div class="preview-icon"><i class="fas fa-arrow-up"></i></div>
                                <div class="preview-compte">
                                    <span class="compte-numero" id="preview_credit_numero">---</span>
                                    <span class="compte-nom" id="preview_credit_nom">Compte créditeur</span>
                                </div>
                                <div class="preview-montant" id="preview_credit_montant">0 FC</div>
                            </div>
                        </div>
                        <div class="preview-footer">
                            <span class="preview-equilibre" id="preview_equilibre">En attente des montants</span>
                        </div>
                    </div>
                </div>

                <!-- Alerte équilibre -->
                <div id="equilibreAlert" class="alert d-none mt-3"></div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ url('/comptable/ecritures') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Enregistrer l'écriture
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
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
        color: #3b82f6;
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
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
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
    
    /* Intitulé du compte */
    .intitule-compte {
        margin-top: 8px;
        padding: 8px 12px;
        background: #f1f5f9;
        border-radius: 10px;
        font-size: 12px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    
    .intitule-compte i {
        color: #3b82f6;
        font-size: 12px;
    }
    
    .intitule-compte.active {
        background: #dbeafe;
        color: #1e40af;
        border-left: 3px solid #3b82f6;
    }
    
    /* Aperçu */
    .preview-section {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
    }
    
    .preview-card {
        background: white;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .preview-header {
        display: grid;
        grid-template-columns: 100px 1fr 150px;
        background: #f8fafc;
        padding: 12px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
    }
    
    .preview-body {
        padding: 5px 0;
    }
    
    .preview-row {
        display: grid;
        grid-template-columns: 40px 1fr 150px;
        align-items: center;
        padding: 12px 20px;
        transition: all 0.2s;
    }
    
    .preview-row.debit-row {
        border-bottom: 1px solid #f1f5f9;
    }
    
    .preview-row.debit-row:hover {
        background: #ecfdf5;
    }
    
    .preview-row.credit-row:hover {
        background: #fef2f2;
    }
    
    .preview-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .debit-row .preview-icon {
        background: #d1fae5;
        color: #10b981;
    }
    
    .credit-row .preview-icon {
        background: #fee2e2;
        color: #ef4444;
    }
    
    .preview-compte {
        display: flex;
        flex-direction: column;
    }
    
    .compte-numero {
        font-weight: 700;
        font-size: 14px;
        color: #1e293b;
    }
    
    .compte-nom {
        font-size: 11px;
        color: #64748b;
    }
    
    .preview-montant {
        font-weight: 700;
        font-size: 16px;
        text-align: right;
    }
    
    .debit-row .preview-montant {
        color: #10b981;
    }
    
    .credit-row .preview-montant {
        color: #ef4444;
    }
    
    .preview-footer {
        background: #f8fafc;
        padding: 12px 20px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .preview-equilibre {
        font-size: 12px;
        font-weight: 500;
    }
    
    .preview-equilibre.success {
        color: #10b981;
    }
    
    .preview-equilibre.error {
        color: #ef4444;
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
    
    .text-muted {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 5px;
        display: block;
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
        background: linear-gradient(135deg, #3b82f6, #2563eb);
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
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
    }
    
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
        color: #3b82f6;
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
        .preview-header {
            display: none;
        }
        .preview-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        .preview-montant {
            text-align: left;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Données des comptes
    const comptesData = @json($comptes->map(function($c) {
        return [
            'numero' => $c->numero,
            'nom' => $c->nom,
            'type' => $c->type
        ];
    }));
    
    const debitSelect = document.getElementById('compte_debit');
    const creditSelect = document.getElementById('compte_credit');
    const debitInput = document.getElementById('montant_debit');
    const creditInput = document.getElementById('montant_credit');
    const alertDiv = document.getElementById('equilibreAlert');
    const submitBtn = document.getElementById('submitBtn');
    
    // Éléments d'intitulé
    const intituleDebit = document.getElementById('intitule_debit');
    const intituleCredit = document.getElementById('intitule_credit');
    
    // Éléments d'aperçu
    const previewDebitNumero = document.getElementById('preview_debit_numero');
    const previewDebitNom = document.getElementById('preview_debit_nom');
    const previewCreditNumero = document.getElementById('preview_credit_numero');
    const previewCreditNom = document.getElementById('preview_credit_nom');
    const previewDebitMontant = document.getElementById('preview_debit_montant');
    const previewCreditMontant = document.getElementById('preview_credit_montant');
    const previewEquilibre = document.getElementById('preview_equilibre');
    
    // Fonction pour mettre à jour l'intitulé du compte débit
    function updateIntituleDebit() {
        const selectedValue = debitSelect.value;
        
        if (selectedValue) {
            const compte = comptesData.find(c => c.numero === selectedValue);
            if (compte) {
                intituleDebit.innerHTML = `<i class="fas fa-check-circle"></i> <strong>${compte.numero}</strong> - ${compte.nom} <span style="color:#64748b">(${compte.type})</span>`;
                intituleDebit.classList.add('active');
                previewDebitNumero.textContent = compte.numero;
                previewDebitNom.textContent = compte.nom;
            }
        } else {
            intituleDebit.innerHTML = `<i class="fas fa-info-circle"></i> Sélectionnez un compte pour voir l'intitulé complet`;
            intituleDebit.classList.remove('active');
            previewDebitNumero.textContent = '---';
            previewDebitNom.textContent = 'Compte débiteur';
        }
        updateApercuMontants();
    }
    
    // Fonction pour mettre à jour l'intitulé du compte crédit
    function updateIntituleCredit() {
        const selectedValue = creditSelect.value;
        
        if (selectedValue) {
            const compte = comptesData.find(c => c.numero === selectedValue);
            if (compte) {
                intituleCredit.innerHTML = `<i class="fas fa-check-circle"></i> <strong>${compte.numero}</strong> - ${compte.nom} <span style="color:#64748b">(${compte.type})</span>`;
                intituleCredit.classList.add('active');
                previewCreditNumero.textContent = compte.numero;
                previewCreditNom.textContent = compte.nom;
            }
        } else {
            intituleCredit.innerHTML = `<i class="fas fa-info-circle"></i> Sélectionnez un compte pour voir l'intitulé complet`;
            intituleCredit.classList.remove('active');
            previewCreditNumero.textContent = '---';
            previewCreditNom.textContent = 'Compte créditeur';
        }
        updateApercuMontants();
    }
    
    // Fonction pour mettre à jour les montants dans l'aperçu
    function updateApercuMontants() {
        let d = parseFloat(debitInput?.value) || 0;
        let c = parseFloat(creditInput?.value) || 0;
        
        previewDebitMontant.textContent = d.toLocaleString() + ' FC';
        previewCreditMontant.textContent = c.toLocaleString() + ' FC';
        
        if (d > 0 || c > 0) {
            if (d === c) {
                previewEquilibre.innerHTML = '<i class="fas fa-check-circle"></i> Équilibre parfait : Débit = Crédit';
                previewEquilibre.className = 'preview-equilibre success';
            } else {
                previewEquilibre.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Déséquilibre : Le débit doit être égal au crédit';
                previewEquilibre.className = 'preview-equilibre error';
            }
        } else {
            previewEquilibre.innerHTML = '<i class="fas fa-info-circle"></i> Saisissez les montants pour vérifier l\'équilibre';
            previewEquilibre.className = 'preview-equilibre';
        }
    }
    
    // Fonction de vérification de l'équilibre
    function checkEquilibre() {
        let d = parseFloat(debitInput?.value) || 0;
        let c = parseFloat(creditInput?.value) || 0;
        updateApercuMontants();
        
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
    
    // Écouteurs d'événements
    if(debitSelect) {
        debitSelect.addEventListener('change', updateIntituleDebit);
        updateIntituleDebit();
    }
    
    if(creditSelect) {
        creditSelect.addEventListener('change', updateIntituleCredit);
        updateIntituleCredit();
    }
    
    if(debitInput && creditInput) {
        debitInput.addEventListener('input', checkEquilibre);
        creditInput.addEventListener('input', checkEquilibre);
        updateApercuMontants();
    }
    
    // Validation et effet de chargement
    document.getElementById('ecritureForm')?.addEventListener('submit', function(e) {
        let d = parseFloat(debitInput?.value) || 0;
        let c = parseFloat(creditInput?.value) || 0;
        
        if(!debitSelect.value) {
            e.preventDefault();
            alert('❌ Veuillez sélectionner un compte débit !');
            debitSelect.focus();
        } else if(!creditSelect.value) {
            e.preventDefault();
            alert('❌ Veuillez sélectionner un compte crédit !');
            creditSelect.focus();
        } else if(d !== c) {
            e.preventDefault();
            alert('❌ Le montant débit doit être égal au montant crédit !');
        } else if(d === 0 && c === 0) {
            e.preventDefault();
            alert('❌ Veuillez saisir un montant !');
        } else {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Enregistrement en cours...';
            submitBtn.disabled = true;
        }
    });
</script>
@endpush
@endsection