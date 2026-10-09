@extends('layouts.admin')

@section('title', 'Nouveau compte')
@section('header-title', '➕ Nouveau compte comptable')
@section('header-subtitle', 'Ajouter un compte au plan comptable SYSCOHADA')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h3 class="card-header-title">Nouveau compte comptable</h3>
                <p class="card-header-subtitle">Remplissez les informations du compte selon la nomenclature OHADA</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/admin/plan-comptable') }}">
                @csrf
                
                <!-- Section 1: Informations du compte -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        <span>Informations générales</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-hashtag text-primary me-1"></i>Numéro de compte <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="numero" class="form-control-custom" placeholder="Ex: 411, 511, 701" required>
                            <small class="text-muted">Numéro unique du compte selon le plan comptable</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>Nom du compte <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom" class="form-control-custom" placeholder="Ex: Clients, Banque, Ventes" required>
                            <small class="text-muted">Libellé complet du compte</small>
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
                                <option value="1">Classe 1 - Capitaux propres (101, 106, 11, 12)</option>
                                <option value="2">Classe 2 - Actif immobilisé (21, 22, 23, 28, 29)</option>
                                <option value="3">Classe 3 - Actif circulant (31, 34, 38, 39)</option>
                                <option value="4">Classe 4 - Passif circulant (40, 41, 42, 43)</option>
                                <option value="5">Classe 5 - Trésorerie (51, 52, 53)</option>
                                <option value="6">Classe 6 - Charges (60, 61, 62, 63, 64, 65, 66)</option>
                                <option value="7">Classe 7 - Produits (70, 71, 72, 73)</option>
                                <option value="8">Classe 8 - Comptes spéciaux (81, 82)</option>
                                <option value="9">Classe 9 - Engagements hors bilan (91, 92)</option>
                            </select>
                            <small class="text-muted">La classe détermine la nature du compte</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-chart-pie text-primary me-1"></i>Type <span class="text-danger">*</span>
                            </label>
                            <select name="type" class="form-control-custom" required>
                                <option value="">Sélectionner un type</option>
                                <option value="Actif">📊 Actif - Biens et droits de l'entreprise</option>
                                <option value="Passif">📈 Passif - Dettes et obligations</option>
                                <option value="Charge">📉 Charge - Dépenses de l'entreprise</option>
                                <option value="Produit">💹 Produit - Revenus de l'entreprise</option>
                            </select>
                            <small class="text-muted">Le type est automatiquement lié à la classe OHADA</small>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Solde initial -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-coins"></i>
                        <span>Solde initial</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-money-bill-wave text-primary me-1"></i>Solde initial (FC)
                            </label>
                            <div class="input-group-custom">
                                <span class="input-group-text">FC</span>
                                <input type="number" name="solde_initial" class="form-control-custom" value="0" step="0.01">
                            </div>
                            <small class="text-muted">Solde d'ouverture du compte (0 par défaut)</small>
                        </div>
                        <div class="col-md-6">
                            <div class="info-tip">
                                <i class="fas fa-lightbulb text-warning"></i>
                                <span>Le solde actuel sera automatiquement mis à jour avec les écritures</span>
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
                        <i class="fas fa-save me-2"></i>Créer le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Card Header */
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
    
    /* Form Sections */
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
    
    /* Form Labels */
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
    
    /* Input Group */
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
    
    /* Info Tip */
    .info-tip {
        background: #fef3c7;
        border-radius: 12px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #92400e;
        font-size: 13px;
    }
    
    .info-tip i {
        font-size: 20px;
    }
    
    /* Form Actions */
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
    
    .text-warning {
        color: #f59e0b;
    }
    
    /* Animations */
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
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
        .info-tip {
            margin-top: 15px;
        }
    }
</style>
@endpush
@endsection