@extends('layouts.comptable')

@section('title', 'Nouveau compte comptable')
@section('header-title', '➕ Nouveau compte comptable')
@section('header-subtitle', 'Ajouter un compte au plan comptable SYSCOHADA')

@section('comptable-content')
<div class="fade-up">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-plus-circle"></i>
            <div>
                <h3>Créer un nouveau compte</h3>
                <p>Remplissez les informations ci-dessous pour ajouter un compte au plan comptable</p>
            </div>
        </div>

        <form method="POST" action="{{ url('/comptable/plan-comptable') }}" id="createForm">
            @csrf
            
            <div class="form-body">
                <!-- Numéro de compte -->
                <div class="form-group">
                    <label class="form-label">
                        Numéro de compte <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="numero" 
                           class="form-control @error('numero') is-invalid @enderror" 
                           value="{{ old('numero') }}" 
                           placeholder="Ex: 411"
                           maxlength="20"
                           required>
                    @error('numero')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                    <small class="form-hint">Format: 3 à 4 chiffres maximum (ex: 411 pour Clients)</small>
                </div>

                <!-- Nom du compte -->
                <div class="form-group">
                    <label class="form-label">
                        Nom du compte <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="nom" 
                           class="form-control @error('nom') is-invalid @enderror" 
                           value="{{ old('nom') }}" 
                           placeholder="Ex: Clients"
                           maxlength="255"
                           required>
                    @error('nom')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                    <small class="form-hint">Nom complet du compte comptable</small>
                </div>

                <!-- Classe -->
                <div class="form-group">
                    <label class="form-label">
                        Classe <span class="required">*</span>
                    </label>
                    <select name="classe" class="form-control @error('classe') is-invalid @enderror" required>
                        <option value="">Sélectionner une classe</option>
                        <option value="1" {{ old('classe') == '1' ? 'selected' : '' }}>Classe 1 - Capitaux propres</option>
                        <option value="2" {{ old('classe') == '2' ? 'selected' : '' }}>Classe 2 - Actif immobilisé</option>
                        <option value="3" {{ old('classe') == '3' ? 'selected' : '' }}>Classe 3 - Actif circulant (Stocks)</option>
                        <option value="4" {{ old('classe') == '4' ? 'selected' : '' }}>Classe 4 - Tiers</option>
                        <option value="5" {{ old('classe') == '5' ? 'selected' : '' }}>Classe 5 - Trésorerie</option>
                        <option value="6" {{ old('classe') == '6' ? 'selected' : '' }}>Classe 6 - Charges</option>
                        <option value="7" {{ old('classe') == '7' ? 'selected' : '' }}>Classe 7 - Produits</option>
                        <option value="8" {{ old('classe') == '8' ? 'selected' : '' }}>Classe 8 - Comptes spéciaux</option>
                        <option value="9" {{ old('classe') == '9' ? 'selected' : '' }}>Classe 9 - Engagements hors bilan</option>
                    </select>
                    @error('classe')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                    <small class="form-hint">La classe détermine la nature du compte</small>
                </div>

                <!-- Type OHADA -->
                <div class="form-group">
                    <label class="form-label">
                        Type OHADA <span class="required">*</span>
                    </label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="">Sélectionner un type</option>
                        <option value="Actif" {{ old('type') == 'Actif' ? 'selected' : '' }}>Actif</option>
                        <option value="Passif" {{ old('type') == 'Passif' ? 'selected' : '' }}>Passif</option>
                        <option value="Charge" {{ old('type') == 'Charge' ? 'selected' : '' }}>Charge</option>
                        <option value="Produit" {{ old('type') == 'Produit' ? 'selected' : '' }}>Produit</option>
                    </select>
                    @error('type')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                    <small class="form-hint">Le type détermine le sens du mouvement comptable</small>
                </div>

                <!-- Solde initial -->
                <div class="form-group">
                    <label class="form-label">Solde initial</label>
                    <div class="input-group">
                        <input type="number" 
                               name="solde_initial" 
                               class="form-control @error('solde_initial') is-invalid @enderror" 
                               value="{{ old('solde_initial', 0) }}" 
                               step="1"
                               min="0">
                        <span class="input-suffix">FC</span>
                    </div>
                    @error('solde_initial')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                    <small class="form-hint">Laisser 0 si pas de solde initial (sauf pour les comptes de trésorerie)</small>
                </div>

                <!-- Aperçu rapide -->
                <div class="preview-box">
                    <h4><i class="fas fa-eye"></i> Aperçu</h4>
                    <div class="preview-content">
                        <div class="preview-row">
                            <span class="preview-label">Compte:</span>
                            <span class="preview-value" id="previewNumero">---</span>
                        </div>
                        <div class="preview-row">
                            <span class="preview-label">Intitulé:</span>
                            <span class="preview-value" id="previewNom">---</span>
                        </div>
                        <div class="preview-row">
                            <span class="preview-label">Classe:</span>
                            <span class="preview-value" id="previewClasse">---</span>
                        </div>
                        <div class="preview-row">
                            <span class="preview-label">Type:</span>
                            <span class="preview-badge" id="previewType">---</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ url('/comptable/plan-comptable') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-save me-2"></i>Enregistrer le compte
                </button>
            </div>
        </form>
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
    
    .form-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        max-width: 700px;
        margin: 0 auto;
        overflow: hidden;
    }
    
    .form-header {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        padding: 25px 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .form-header i {
        font-size: 40px;
        color: white;
        background: rgba(255,255,255,0.2);
        padding: 12px;
        border-radius: 16px;
    }
    
    .form-header h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    
    .form-header p {
        font-size: 12px;
        margin: 5px 0 0;
        color: rgba(255,255,255,0.8);
    }
    
    .form-body {
        padding: 30px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .required {
        color: #ef4444;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    
    .form-control.is-invalid {
        border-color: #ef4444;
    }
    
    .input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .input-group .form-control {
        flex: 1;
    }
    
    .input-suffix {
        background: #f1f5f9;
        padding: 12px 15px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
    }
    
    .error-text {
        color: #ef4444;
        font-size: 11px;
        margin-top: 5px;
        display: block;
    }
    
    .form-hint {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 5px;
        display: block;
    }
    
    /* Preview Box */
    .preview-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        margin-top: 25px;
        border: 1px solid #e2e8f0;
    }
    
    .preview-box h4 {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .preview-box h4 i {
        color: #6366f1;
        font-size: 14px;
    }
    
    .preview-content {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .preview-row {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .preview-row:last-child {
        border-bottom: none;
    }
    
    .preview-label {
        width: 80px;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    
    .preview-value {
        flex: 1;
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
    }
    
    .preview-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .preview-badge.actif {
        background: #d1fae5;
        color: #059669;
    }
    
    .preview-badge.passif {
        background: #fef3c7;
        color: #d97706;
    }
    
    .preview-badge.charge {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .preview-badge.produit {
        background: #e0e7ff;
        color: #4f46e5;
    }
    
    .preview-badge.classe1, .preview-badge.classe2, .preview-badge.classe3,
    .preview-badge.classe4, .preview-badge.classe5, .preview-badge.classe6,
    .preview-badge.classe7, .preview-badge.classe8, .preview-badge.classe9 {
        background: #dbeafe;
        color: #2563eb;
    }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        padding: 20px 30px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99,102,241,0.4);
    }
    
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-secondary {
        background: #f1f5f9;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        color: #1e293b;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-secondary:hover {
        background: #e2e8f0;
    }
    
    @media (max-width: 640px) {
        .form-body {
            padding: 20px;
        }
        .form-actions {
            padding: 15px 20px;
            flex-direction: column;
        }
        .btn-primary, .btn-secondary {
            justify-content: center;
            width: 100%;
        }
        .input-group {
            flex-direction: column;
            align-items: stretch;
        }
        .input-suffix {
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Aperçu en temps réel
    const numeroInput = document.querySelector('input[name="numero"]');
    const nomInput = document.querySelector('input[name="nom"]');
    const classeSelect = document.querySelector('select[name="classe"]');
    const typeSelect = document.querySelector('select[name="type"]');
    
    const previewNumero = document.getElementById('previewNumero');
    const previewNom = document.getElementById('previewNom');
    const previewClasse = document.getElementById('previewClasse');
    const previewType = document.getElementById('previewType');
    
    function updatePreview() {
        // Numéro
        previewNumero.textContent = numeroInput.value || '---';
        
        // Nom
        previewNom.textContent = nomInput.value || '---';
        
        // Classe
        const classeValue = classeSelect.value;
        if (classeValue) {
            const classeText = classeSelect.options[classeSelect.selectedIndex]?.text || '';
            previewClasse.textContent = classeText;
            previewClasse.className = 'preview-value';
        } else {
            previewClasse.textContent = '---';
        }
        
        // Type
        const typeValue = typeSelect.value;
        if (typeValue) {
            previewType.textContent = typeValue;
            previewType.className = `preview-badge ${typeValue.toLowerCase()}`;
        } else {
            previewType.textContent = '---';
            previewType.className = 'preview-badge';
        }
    }
    
    numeroInput?.addEventListener('input', updatePreview);
    nomInput?.addEventListener('input', updatePreview);
    classeSelect?.addEventListener('change', updatePreview);
    typeSelect?.addEventListener('change', updatePreview);
    
    // Initial preview
    updatePreview();
    
    // Désactivation du bouton pendant la soumission
    document.getElementById('createForm')?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
        submitBtn.disabled = true;
    });
    
    // Validation avant soumission
    document.getElementById('createForm')?.addEventListener('submit', function(e) {
        const numero = numeroInput?.value.trim();
        const nom = nomInput?.value.trim();
        const classe = classeSelect?.value;
        const type = typeSelect?.value;
        
        if (!numero) {
            e.preventDefault();
            alert('Veuillez saisir le numéro de compte');
            numeroInput?.focus();
            return false;
        }
        
        if (!nom) {
            e.preventDefault();
            alert('Veuillez saisir le nom du compte');
            nomInput?.focus();
            return false;
        }
        
        if (!classe) {
            e.preventDefault();
            alert('Veuillez sélectionner une classe');
            classeSelect?.focus();
            return false;
        }
        
        if (!type) {
            e.preventDefault();
            alert('Veuillez sélectionner un type OHADA');
            typeSelect?.focus();
            return false;
        }
    });
</script>
@endpush
@endsection