@extends('layouts.admin')

@section('title', 'Détail du compte')
@section('header-title', '👁️ Détail du compte comptable')
@section('header-subtitle', 'Informations complètes du compte')

@section('admin-content')
<div class="fade-up">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-info-circle me-2"></i>Informations générales</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3"><label>Numéro de compte</label><h4 class="text-primary">{{ $compte->numero }}</h4></div>
                    <div class="info-box mb-3"><label>Nom du compte</label><h4>{{ $compte->nom }}</h4></div>
                    <div class="info-box mb-3"><label>Classe</label><h4>Classe {{ $compte->classe }}</h4></div>
                    <div class="info-box mb-3"><label>Type OHADA</label><h4>@if($compte->type == 'Actif')<span class="badge-success">Actif</span>@elseif($compte->type == 'Passif')<span class="badge-warning">Passif</span>@elseif($compte->type == 'Charge')<span class="badge-danger">Charge</span>@else<span class="badge-info">Produit</span>@endif</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Informations financières</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3"><label>Solde initial</label><h4>{{ number_format($compte->solde_initial, 0, ',', ' ') }} FC</h4></div>
                    <div class="info-box mb-3"><label>Solde actuel</label><h4>{{ number_format($compte->solde_actuel, 0, ',', ' ') }} FC</h4></div>
                    <div class="info-box"><label>Date de création</label><h4>{{ $compte->created_at->format('d/m/Y H:i') }}</h4></div>
                </div>
                <div class="p-4 border-top d-flex gap-3">
                    <a href="{{ url('/admin/plan-comptable/' . $compte->id . '/edit') }}" class="btn-warning-custom"><i class="fas fa-edit me-2"></i>Modifier</a>
                    <button onclick="confirmDelete({{ $compte->id }}, '{{ $compte->numero }}')" class="btn-danger-custom"><i class="fas fa-trash-alt me-2"></i>Supprimer</button>
                    <a href="{{ url('/admin/plan-comptable') }}" class="btn btn-light px-4"><i class="fas fa-arrow-left me-2"></i>Retour</a>
                </div>
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
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .data-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .data-header h3 {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }
    .info-box {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .info-box label {
        font-size: 10px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 5px;
    }
    .info-box h4 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }
    .badge-success { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-warning { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-danger { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .btn-warning-custom { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .btn-warning-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); color: white; }
    .btn-danger-custom { background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; cursor: pointer; }
    .btn-danger-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239,68,68,0.3); color: white; }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete(id, numero) { 
        if(confirm(`Supprimer le compte ${numero} ?`)) {
            window.location.href = '/admin/plan-comptable/' + id + '/delete';
        } 
    }
</script>
@endpush
@endsection