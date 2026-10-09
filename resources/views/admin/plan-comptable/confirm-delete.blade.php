@extends('layouts.admin')

@section('title', 'Confirmer la suppression')
@section('header-title', '🗑️ Suppression d\'un compte')
@section('header-subtitle', 'Confirmation de suppression')

@section('admin-content')
<div class="fade-up">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="data-card">
                @if($usedInEcritures)
                    <div class="alert alert-warning m-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                            <div>
                                <h5 class="mb-1">⚠️ Suppression impossible</h5>
                                <p class="mb-0">Ce compte est utilisé dans des écritures comptables. Vous ne pouvez pas le supprimer.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 text-center">
                        <a href="{{ url('/admin/plan-comptable') }}" class="btn-primary-custom">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                @else
                    <div class="alert alert-danger m-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-exclamation-circle fa-3x"></i>
                            <div>
                                <h5 class="mb-1">⚠️ Attention ! Action irréversible</h5>
                                <p class="mb-0">Vous êtes sur le point de supprimer définitivement ce compte comptable.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="info-box mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Numéro du compte</label>
                                        <h4 class="text-primary mb-0">{{ $compte->numero }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Nom du compte</label>
                                        <h4 class="mb-0">{{ $compte->nom }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Classe</label>
                                        <p class="mb-0"><span class="badge-info">Classe {{ $compte->classe }}</span></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Type OHADA</label>
                                        <p class="mb-0">
                                            @if($compte->type == 'Actif')
                                                <span class="badge-success"><i class="fas fa-chart-line me-1"></i>Actif</span>
                                            @elseif($compte->type == 'Passif')
                                                <span class="badge-warning"><i class="fas fa-chart-pie me-1"></i>Passif</span>
                                            @elseif($compte->type == 'Charge')
                                                <span class="badge-danger"><i class="fas fa-arrow-down me-1"></i>Charge</span>
                                            @else
                                                <span class="badge-info"><i class="fas fa-arrow-up me-1"></i>Produit</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Solde initial</label>
                                        <p class="mb-0 fw-bold">{{ number_format($compte->solde_initial, 0, ',', ' ') }} FC</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Solde actuel</label>
                                        <p class="mb-0 fw-bold">{{ number_format($compte->solde_actuel, 0, ',', ' ') }} FC</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ url('/admin/plan-comptable/' . $compte->id) }}" method="POST" 
                              onsubmit="return confirm('Confirmation finale : Voulez-vous vraiment supprimer définitivement le compte {{ $compte->numero }} - {{ $compte->nom }} ?')">
                            @csrf
                            @method('DELETE')
                            
                            <div class="d-flex justify-content-between gap-3">
                                <a href="{{ url('/admin/plan-comptable') }}" class="btn btn-secondary btn-lg flex-grow-1" style="padding: 12px; border-radius: 12px;">
                                    <i class="fas fa-arrow-left me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-danger btn-lg flex-grow-1" style="padding: 12px; border-radius: 12px; background: linear-gradient(135deg, #ef4444, #dc2626); border: none;">
                                    <i class="fas fa-trash-alt me-2"></i>Oui, supprimer définitivement
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
    .badge-success { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-warning { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-danger { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .info-box { background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; }
    .btn-primary-custom { background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(139,92,246,0.3); color: white; }
    .data-card { background: white; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 25px; }
</style>
@endpush
@endsection