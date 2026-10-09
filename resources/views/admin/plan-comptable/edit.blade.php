@extends('layouts.admin')

@section('title', 'Modifier compte')
@section('header-title', '✏️ Modifier le compte comptable')
@section('header-subtitle', 'Modification des informations du compte')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h3 class="card-header-title">Modification du compte</h3>
                <p class="card-header-subtitle">Modifiez les informations du compte {{ $compte->numero }} - {{ $compte->nom }}</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/admin/plan-comptable/' . $compte->id) }}">
                @csrf 
                @method('PUT')
                
                <!-- Section 1: Informations du compte -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        <span>Informations générales</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-hashtag text-primary me-1"></i>Numéro de compte
                            </label>
                            <input type="text" value="{{ $compte->numero }}" class="form-control-custom" disabled>
                            <small class="text-muted">Le numéro de compte ne peut pas être modifié</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>Nom du compte <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom" value="{{ old('nom', $compte->nom) }}" class="form-control-custom" required>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Classification -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-layer-group"></i>
                        <span>Classification OHADA</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-diagram-project text-primary me-1"></i>Classe <span class="text-danger">*</span>
                            </label>
                            <select name="classe" class="form-control-custom" required>
                                <option value="">Sélectionner une classe</option>
                                @for($i=1; $i<=9; $i++)
                                <option value="{{ $i }}" {{ $compte->classe == $i ? 'selected' : '' }}>
                                    Classe {{ $i }} - 
                                    @switch($i)
                                        @case(1) Capitaux propres @break
                                        @case(2) Actif immobilisé @break
                                        @case(3) Actif circulant @break
                                        @case(4) Passif circulant @break
                                        @case(5) Trésorerie @break
                                        @case(6) Charges @break
                                        @case(7) Produits @break
                                        @case(8) Comptes spéciaux @break
                                        @case(9) Engagements @break
                                    @endswitch
                                </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-chart-pie text-primary me-1"></i>Type <span class="text-danger">*</span>
                            </label>
                            <select name="type" class="form-control-custom" required>
                                <option value="Actif" {{ $compte->type == 'Actif' ? 'selected' : '' }}>📊 Actif - Biens et droits de l'entreprise</option>
                                <option value="Passif" {{ $compte->type == 'Passif' ? 'selected' : '' }}>📈 Passif - Dettes et obligations</option>
                                <option value="Charge" {{ $compte->type == 'Charge' ? 'selected' : '' }}>📉 Charge - Dépenses de l'entreprise</option>
                                <option value="Produit" {{ $compte->type == 'Produit' ? 'selected' : '' }}>💹 Produit - Revenus de l'entreprise</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Solde -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-coins"></i>
                        <span>Informations financières</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-chart-line text-primary me-1"></i>Solde initial (FC)
                            </label>
                            <div class="input-group-custom">
                                <span class="input-group-text">FC</span>
                                <input type="number" name="solde_initial" value="{{ $compte->solde_initial }}" class="form-control-custom" step="0.01">
                            </div>
                            <small class="text-muted">Modification du solde d'ouverture</small>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Solde actuel</label>
                                <h4 class="text-primary">{{ number_format($compte->solde_actuel, 0, ',', ' ') }} FC</h4>
                                <small>Mis à jour automatiquement</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ url('/admin/plan-comptable') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit">
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
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    
    .card-header-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    
    .card-header-subtitle {
        font-size: 12px;
        margin: 5px 0 0;
        color: rgba(255,255,255,0.8);
    }
    
    .form-section {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
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
        color: #8b5cf6;
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
        border-color: #8b5cf6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }
    
    .form-control-custom:disabled {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
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
    
    .info-box {
        background: #f8fafc;
        padding: 15px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .info-box label {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        display: block;
        margin-bottom: 5px;
    }
    
    .info-box h4 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }
    
    .info-box small {
        font-size: 11px;
        color: #94a3b8;
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
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
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
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
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
    
    .text-muted {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 5px;
        display: block;
    }
    
    .text-danger {
        color: #ef4444;
    }
    
    .text-primary {
        color: #8b5cf6;
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
@endsection