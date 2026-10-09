@extends('layouts.comptable')

@section('title', 'Confirmer la suppression')
@section('header-title', '🗑️ Suppression d\'un compte')
@section('header-subtitle', 'Confirmation de suppression')

@section('comptable-content')
<div class="fade-up">
    <div class="confirm-card">
        @if($usedInEcritures)
            <div class="alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <h4>⚠️ Suppression impossible</h4>
                    <p>Ce compte est utilisé dans des écritures comptables. Vous ne pouvez pas le supprimer.</p>
                </div>
            </div>
            <div class="confirm-actions">
                <a href="{{ url('/comptable/plan-comptable') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
        @else
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <h4>⚠️ Attention ! Action irréversible</h4>
                    <p>Vous êtes sur le point de supprimer définitivement ce compte comptable.</p>
                </div>
            </div>
            
            <div class="compte-info">
                <h4>Informations du compte</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Numéro</label>
                        <span>{{ $compte->numero }}</span>
                    </div>
                    <div class="info-item">
                        <label>Nom</label>
                        <span>{{ $compte->nom }}</span>
                    </div>
                    <div class="info-item">
                        <label>Classe</label>
                        <span class="badge classe">Classe {{ $compte->classe }}</span>
                    </div>
                    <div class="info-item">
                        <label>Type</label>
                        <span>
                            @if($compte->type == 'Actif')
                                <span class="badge actif">Actif</span>
                            @elseif($compte->type == 'Passif')
                                <span class="badge passif">Passif</span>
                            @elseif($compte->type == 'Charge')
                                <span class="badge charge">Charge</span>
                            @else
                                <span class="badge produit">Produit</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Solde initial</label>
                        <span>{{ number_format($compte->solde_initial, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="info-item">
                        <label>Solde actuel</label>
                        <span>{{ number_format($compte->solde_actuel, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ url('/comptable/plan-comptable/' . $compte->id) }}" 
                  onsubmit="return confirm('Confirmation finale : Voulez-vous vraiment supprimer définitivement ce compte ?')">
                @csrf
                @method('DELETE')
                
                <div class="confirm-actions">
                    <a href="{{ url('/comptable/plan-comptable') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Oui, supprimer définitivement
                    </button>
                </div>
            </form>
        @endif
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
    
    .confirm-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        max-width: 700px;
        margin: 0 auto;
        overflow: hidden;
    }
    
    .alert-warning, .alert-danger {
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .alert-warning {
        background: #fef3c7;
        border-bottom: 1px solid #fde68a;
    }
    
    .alert-warning i {
        font-size: 40px;
        color: #d97706;
    }
    
    .alert-warning h4 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: #92400e;
    }
    
    .alert-warning p {
        font-size: 13px;
        margin: 0;
        color: #78350f;
    }
    
    .alert-danger {
        background: #fee2e2;
        border-bottom: 1px solid #fecaca;
    }
    
    .alert-danger i {
        font-size: 40px;
        color: #dc2626;
    }
    
    .alert-danger h4 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: #991b1b;
    }
    
    .alert-danger p {
        font-size: 13px;
        margin: 0;
        color: #7f1d1d;
    }
    
    .compte-info {
        padding: 25px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .compte-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .compte-info h4:before {
        content: '';
        width: 4px;
        height: 18px;
        background: #4f46e5;
        border-radius: 2px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .info-item label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    
    .info-item span {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }
    
    .badge {
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge.classe {
        background: #dbeafe;
        color: #2563eb;
    }
    .badge.actif {
        background: #d1fae5;
        color: #059669;
    }
    .badge.passif {
        background: #fef3c7;
        color: #d97706;
    }
    .badge.charge {
        background: #fee2e2;
        color: #dc2626;
    }
    .badge.produit {
        background: #e0e7ff;
        color: #4f46e5;
    }
    
    .confirm-actions {
        padding: 20px 25px;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        background: #f8fafc;
    }
    
    .btn-secondary {
        background: #f1f5f9;
        border: none;
        padding: 10px 20px;
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
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.4);
    }
    
    @media (max-width: 640px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        .confirm-actions {
            flex-direction: column;
        }
        .confirm-actions a, .confirm-actions button {
            justify-content: center;
        }
    }
</style>
@endpush
@endsection