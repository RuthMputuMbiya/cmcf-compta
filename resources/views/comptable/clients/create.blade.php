@extends('layouts.comptable')

@section('title', 'Nouveau client')
@section('header-title', '➕ Nouveau client')
@section('header-subtitle', 'Ajouter un client à la base de données')

@section('comptable-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h3 class="card-header-title">Informations du client</h3>
                <p class="card-header-subtitle">Remplissez tous les champs obligatoires</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/comptable/clients') }}">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-user text-primary me-1"></i>Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom" class="form-control-custom" placeholder="Nom du client" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-envelope text-primary me-1"></i>Email
                        </label>
                        <input type="email" name="email" class="form-control-custom" placeholder="client@email.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-phone text-primary me-1"></i>Téléphone
                        </label>
                        <input type="text" name="telephone" class="form-control-custom" placeholder="+243 XXX XXX XXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-map-marker-alt text-primary me-1"></i>Adresse
                        </label>
                        <input type="text" name="adresse" class="form-control-custom" placeholder="Adresse complète">
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ url('/comptable/clients') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save me-2"></i>Enregistrer le client
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