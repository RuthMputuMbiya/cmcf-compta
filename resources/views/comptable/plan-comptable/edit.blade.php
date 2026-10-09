@extends('layouts.comptable')

@section('title', 'Modifier le compte comptable')
@section('header-title', '✏️ Modifier le compte comptable')
@section('header-subtitle', 'Modification des informations du compte')

@section('comptable-content')
<div class="fade-up">
    <div class="form-card">
        <form method="POST" action="{{ url('/comptable/plan-comptable/' . $compte->id) }}">
            @csrf
            @method('PUT')
            
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Compte: {{ $compte->numero }}</strong><br>
                    <small>Le numéro de compte ne peut pas être modifié</small>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Numéro de compte</label>
                    <input type="text" class="form-control" value="{{ $compte->numero }}" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nom du compte <span class="required">*</span></label>
                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" 
                           value="{{ old('nom', $compte->nom) }}" required>
                    @error('nom')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Classe <span class="required">*</span></label>
                    <select name="classe" class="form-control @error('classe') is-invalid @enderror" required>
                        <option value="1" {{ old('classe', $compte->classe) == '1' ? 'selected' : '' }}>Classe 1 - Capitaux propres</option>
                        <option value="2" {{ old('classe', $compte->classe) == '2' ? 'selected' : '' }}>Classe 2 - Actif immobilisé</option>
                        <option value="3" {{ old('classe', $compte->classe) == '3' ? 'selected' : '' }}>Classe 3 - Actif circulant</option>
                        <option value="4" {{ old('classe', $compte->classe) == '4' ? 'selected' : '' }}>Classe 4 - Tiers</option>
                        <option value="5" {{ old('classe', $compte->classe) == '5' ? 'selected' : '' }}>Classe 5 - Trésorerie</option>
                        <option value="6" {{ old('classe', $compte->classe) == '6' ? 'selected' : '' }}>Classe 6 - Charges</option>
                        <option value="7" {{ old('classe', $compte->classe) == '7' ? 'selected' : '' }}>Classe 7 - Produits</option>
                        <option value="8" {{ old('classe', $compte->classe) == '8' ? 'selected' : '' }}>Classe 8 - Comptes spéciaux</option>
                        <option value="9" {{ old('classe', $compte->classe) == '9' ? 'selected' : '' }}>Classe 9 - Engagements</option>
                    </select>
                    @error('classe')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Type OHADA <span class="required">*</span></label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="Actif" {{ old('type', $compte->type) == 'Actif' ? 'selected' : '' }}>Actif</option>
                        <option value="Passif" {{ old('type', $compte->type) == 'Passif' ? 'selected' : '' }}>Passif</option>
                        <option value="Charge" {{ old('type', $compte->type) == 'Charge' ? 'selected' : '' }}>Charge</option>
                        <option value="Produit" {{ old('type', $compte->type) == 'Produit' ? 'selected' : '' }}>Produit</option>
                    </select>
                    @error('type')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Solde initial</label>
                    <input type="text" class="form-control" value="{{ number_format($compte->solde_initial, 0, ',', ' ') }} FCFA" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Solde actuel</label>
                    <input type="text" class="form-control" value="{{ number_format($compte->solde_actuel, 0, ',', ' ') }} FCFA" disabled>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="{{ url('/comptable/plan-comptable') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
                <button type="submit" class="btn-primary">
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
    
    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .info-alert {
        background: #e0e7ff;
        border-radius: 12px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
    }
    
    .info-alert i {
        font-size: 20px;
        color: #4f46e5;
    }
    
    .info-alert strong {
        color: #1e293b;
        font-size: 14px;
    }
    
    .info-alert small {
        color: #64748b;
        font-size: 12px;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .form-group {
        margin-bottom: 5px;
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
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    
    .form-control:disabled {
        background: #f1f5f9;
        color: #64748b;
    }
    
    .form-control.is-invalid {
        border-color: #ef4444;
    }
    
    .error-text {
        color: #ef4444;
        font-size: 11px;
        margin-top: 5px;
        display: block;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
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
    
    .btn-secondary {
        background: #f1f5f9;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        color: #1e293b;
        font-weight: 500;
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
        .form-grid {
            grid-template-columns: 1fr;
        }
        .form-card {
            padding: 20px;
        }
    }
</style>
@endpush
@endsection