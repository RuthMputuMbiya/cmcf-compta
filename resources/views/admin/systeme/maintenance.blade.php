@extends('layouts.admin')

@section('title', 'Mode maintenance')
@section('header-title', '🛠️ Mode maintenance')
@section('header-subtitle', 'Gestion du mode maintenance de l\'application')

@section('admin-content')
<div class="fade-up">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-info-circle me-2"></i>Statut actuel</h3></div>
                <div class="p-4 text-center">
                    @if($maintenanceMode)
                        <div class="stat-icon stat-warning mx-auto mb-3"><i class="fas fa-exclamation-triangle fa-3x"></i></div>
                        <h4 class="text-warning">Mode maintenance ACTIF</h4>
                        <p class="text-muted">L'application est actuellement en maintenance. Seuls les administrateurs peuvent accéder au site.</p>
                    @else
                        <div class="stat-icon stat-success mx-auto mb-3"><i class="fas fa-check-circle fa-3x"></i></div>
                        <h4 class="text-success">Mode maintenance INACTIF</h4>
                        <p class="text-muted">L'application est accessible à tous les utilisateurs.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-cog me-2"></i>Actions</h3></div>
                <div class="p-4">
                    @if($maintenanceMode)
                    <form method="POST" action="{{ route('admin.systeme.maintenance') }}">
                        @csrf
                        <button type="submit" class="btn-success-custom w-100 py-3"><i class="fas fa-play me-2"></i>Désactiver le mode maintenance</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.systeme.maintenance') }}">
                        @csrf
                        <input type="hidden" name="enable" value="1">
                        <div class="mb-3">
                            <label class="form-label">Token secret (optionnel)</label>
                            <input type="text" name="secret" class="form-control-custom" placeholder="Laisser vide pour aucun token">
                            <small class="text-muted">Permet d'accéder au site même en maintenance</small>
                        </div>
                        <button type="submit" class="btn-warning-custom w-100 py-3"><i class="fas fa-pause me-2"></i>Activer le mode maintenance</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-exclamation-triangle me-2"></i>⚠️ Attention</h3></div>
        <div class="p-4">
            <ul class="text-muted">
                <li>En mode maintenance, tous les utilisateurs (sauf les administrateurs) verront une page "Site en maintenance".</li>
                <li>Les administrateurs peuvent toujours accéder au site normalement.</li>
                <li>Si vous définissez un token secret, les utilisateurs peuvent accéder au site en ajoutant ?secret=VOTRE_TOKEN à l'URL.</li>
                <li>Pensez à prévenir vos utilisateurs avant d'activer le mode maintenance.</li>
            </ul>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-success-custom { background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 10px 24px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; }
    .btn-success-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
    .btn-warning-custom { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 10px 24px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; }
    .btn-warning-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); }
</style>
@endpush
@endsection