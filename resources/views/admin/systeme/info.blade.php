@extends('layouts.admin')

@section('title', 'Informations système')
@section('header-title', 'ℹ️ Informations détaillées')
@section('header-subtitle', 'Configuration technique du système')

@section('admin-content')
<div class="fade-up">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fab fa-php me-2"></i>PHP Configuration</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3"><label>Version</label><h4>{{ $phpInfo['version'] }}</h4></div>
                    <div class="info-box mb-3"><label>Extensions chargées</label><p class="small">{{ implode(', ', array_slice($phpInfo['extensions'], 0, 10)) }}...</p></div>
                    <div class="info-box"><label>Fonctions désactivées</label><p class="small">{{ $phpInfo['disabled_functions'] ?: 'Aucune' }}</p></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-database me-2"></i>Base de données</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3"><label>Driver</label><h4>{{ $dbInfo['driver'] }}</h4></div>
                    <div class="info-box mb-3"><label>Hôte</label><h4>{{ $dbInfo['host'] }}</h4></div>
                    <div class="info-box mb-3"><label>Base de données</label><h4>{{ $dbInfo['database'] }}</h4></div>
                    <div class="info-box"><label>Charset</label><h4>{{ $dbInfo['charset'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-globe me-2"></i>Environnement</h3></div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-4"><div class="info-box"><label>APP_ENV</label><h4>{{ $envInfo['APP_ENV'] }}</h4></div></div>
                        <div class="col-md-4"><div class="info-box"><label>APP_DEBUG</label><h4><span class="badge {{ $envInfo['APP_DEBUG'] == 'true' ? 'badge-warning' : 'badge-success' }}">{{ $envInfo['APP_DEBUG'] }}</span></h4></div></div>
                        <div class="col-md-4"><div class="info-box"><label>APP_URL</label><h4>{{ $envInfo['APP_URL'] }}</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection