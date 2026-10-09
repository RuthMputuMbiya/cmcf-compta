@extends('layouts.comptable')

@section('title', 'Modifier le fournisseur')
@section('header-title', '✏️ Modifier le fournisseur')
@section('header-subtitle', 'Mise à jour des informations du fournisseur')

@section('comptable-content')
<div class="fade-up">
    <div class="form-card">
        <div class="form-header">
            <div class="form-header-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div>
                <h3 class="form-header-title">Modifier le fournisseur</h3>
                <p class="form-header-subtitle">Mettez à jour les informations du fournisseur</p>
            </div>
        </div>

        <form method="POST" action="{{ url('/comptable/fournisseurs/' . $fournisseur->id) }}" id="fournisseurForm">
            @csrf
            @method('PUT')
            
            <div class="form-body">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-1 text-primary"></i>Nom complet <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="nom" 
                           class="form-control @error('nom') is-invalid @enderror" 
                           value="{{ old('nom', $fournisseur->nom) }}" 
                           placeholder="Ex: SARL CMCF" 
                           required>
                    @error('nom')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope me-1 text-primary"></i>Email
                    </label>
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $fournisseur->email) }}" 
                           placeholder="exemple@email.com">
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone me-1 text-primary"></i>Téléphone <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="telephone" 
                           class="form-control @error('telephone') is-invalid @enderror" 
                           value="{{ old('telephone', $fournisseur->telephone) }}" 
                           placeholder="+243 XXX XXX XXX" 
                           required>
                    @error('telephone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt me-1 text-primary"></i>Adresse
                    </label>
                    <textarea name="adresse" 
                              class="form-control @error('adresse') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Adresse complète du fournisseur">{{ old('adresse', $fournisseur->adresse) }}</textarea>
                    @error('adresse')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Informations supplémentaires -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-info-circle"></i>
                        <span>Informations système</span>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <span class="info-label">Date d'ajout :</span>
                            <span class="info-value">{{ $fournisseur->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dernière modification :</span>
                            <span class="info-value">{{ $fournisseur->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ url('/comptable/fournisseurs') }}" class="btn-cancel">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-save me-2"></i>Mettre à jour
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
    
    /* Form Card */
    .form-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        max-width: 800px;
        margin: 0 auto;
        overflow: hidden;
    }
    
    /* Form Header */
    .form-header {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        padding: 25px 30px;
        display: flex;
        align-items: center;
        gap: 18px;
    }
    
    .form-header-icon {
        width: 55px;
        height: 55px;
        background: rgba(255,255,255,0.2);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }
    
    .form-header-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    
    .form-header-subtitle {
        font-size: 13px;
        margin: 5px 0 0;
        color: rgba(255,255,255,0.9);
    }
    
    /* Form Body */
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
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s;
        background: white;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
    }
    
    .form-control.is-invalid {
        border-color: #ef4444;
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }
    
    .error-text {
        color: #ef4444;
        font-size: 11px;
        margin-top: 5px;
        display: block;
    }
    
    /* Info Card */
    .info-card {
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        margin-top: 25px;
        overflow: hidden;
    }
    
    .info-card-header {
        background: #f1f5f9;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .info-card-header i {
        color: #f59e0b;
        font-size: 14px;
    }
    
    .info-card-body {
        padding: 15px 20px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-size: 12px;
        color: #64748b;
    }
    
    .info-value {
        font-size: 12px;
        font-weight: 500;
        color: #1e293b;
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
    
    /* Buttons */
    .btn-submit {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.4);
    }
    
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-cancel {
        background: #f1f5f9;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        color: #1e293b;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-cancel:hover {
        background: #e2e8f0;
    }
    
    /* Loading state */
    .btn-submit.loading {
        opacity: 0.7;
        cursor: wait;
    }
    
    .btn-submit.loading i {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 640px) {
        .form-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }
        .form-body {
            padding: 20px;
        }
        .form-actions {
            flex-direction: column;
            padding: 15px 20px;
        }
        .btn-submit, .btn-cancel {
            justify-content: center;
            width: 100%;
        }
        .info-row {
            flex-direction: column;
            gap: 4px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const submitBtn = document.getElementById('submitBtn');
    
    document.getElementById('fournisseurForm')?.addEventListener('submit', function(e) {
        const nom = document.querySelector('input[name="nom"]')?.value.trim();
        const telephone = document.querySelector('input[name="telephone"]')?.value.trim();
        
        if (!nom) {
            e.preventDefault();
            alert('❌ Veuillez saisir le nom du fournisseur !');
            document.querySelector('input[name="nom"]')?.focus();
            return false;
        }
        
        if (!telephone) {
            e.preventDefault();
            alert('❌ Veuillez saisir le numéro de téléphone !');
            document.querySelector('input[name="telephone"]')?.focus();
            return false;
        }
        
        // Afficher l'effet de chargement
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Enregistrement en cours...';
        submitBtn.disabled = true;
    });
</script>
@endpush
@endsection