@extends('layouts.comptable')

@section('title', 'Détail du compte comptable')
@section('header-title', '👁️ Détail du compte comptable')
@section('header-subtitle', 'Informations complètes du compte')

@section('comptable-content')
<div class="fade-up">
    <div class="detail-grid">
        <!-- Carte informations générales -->
        <div class="detail-card">
            <div class="detail-header">
                <i class="fas fa-info-circle"></i>
                <h3>Informations générales</h3>
            </div>
            <div class="detail-body">
                <div class="info-row">
                    <label>Numéro de compte</label>
                    <span class="value">{{ $compte->numero }}</span>
                </div>
                <div class="info-row">
                    <label>Nom du compte</label>
                    <span class="value">{{ $compte->nom }}</span>
                </div>
                <div class="info-row">
                    <label>Classe</label>
                    <span class="badge classe">Classe {{ $compte->classe }}</span>
                </div>
                <div class="info-row">
                    <label>Type OHADA</label>
                    <span>
                        @if($compte->type == 'Actif')
                            <span class="badge actif"><i class="fas fa-chart-line me-1"></i>Actif</span>
                        @elseif($compte->type == 'Passif')
                            <span class="badge passif"><i class="fas fa-chart-pie me-1"></i>Passif</span>
                        @elseif($compte->type == 'Charge')
                            <span class="badge charge"><i class="fas fa-arrow-down me-1"></i>Charge</span>
                        @else
                            <span class="badge produit"><i class="fas fa-arrow-up me-1"></i>Produit</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Carte informations financières -->
        <div class="detail-card">
            <div class="detail-header">
                <i class="fas fa-chart-line"></i>
                <h3>Informations financières</h3>
            </div>
            <div class="detail-body">
                <div class="info-row">
                    <label>Solde initial</label>
                    <span class="value">{{ number_format($compte->solde_initial, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="info-row">
                    <label>Solde actuel</label>
                    <span class="value">{{ number_format($compte->solde_actuel, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="info-row">
                    <label>Date de création</label>
                    <span class="value">{{ $compte->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <label>Dernière modification</label>
                    <span class="value">{{ $compte->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="action-bar">
        <a href="{{ url('/comptable/plan-comptable') }}" class="btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
        <div class="action-group">
            <a href="{{ url('/comptable/plan-comptable/' . $compte->id . '/edit') }}" class="btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="{{ url('/comptable/plan-comptable/' . $compte->id . '/delete') }}" class="btn-danger">
                <i class="fas fa-trash-alt me-2"></i>Supprimer
            </a>
        </div>
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
    
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 25px;
    }
    
    .detail-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .detail-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .detail-header i {
        font-size: 20px;
        color: #4f46e5;
    }
    
    .detail-header h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: #1e293b;
    }
    
    .detail-body {
        padding: 20px 22px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-row label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-row .value {
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
    }
    
    .badge {
        padding: 4px 12px;
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
    
    .action-bar {
        background: white;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 18px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .action-group {
        display: flex;
        gap: 12px;
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
    
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.4);
        color: white;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.4);
        color: white;
    }
    
    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
        .action-bar {
            flex-direction: column;
        }
        .action-group {
            width: 100%;
        }
        .action-group a {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
        .btn-secondary {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
    }
</style>
@endpush
@endsection