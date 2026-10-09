@extends('layouts.admin')

@section('title', 'Système')
@section('header-title', '⚙️ Informations système')
@section('header-subtitle', 'Configuration et maintenance')

@section('admin-content')
<div class="fade-up">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-server me-2"></i>Informations serveur</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3"><label>Version application</label><h4>{{ $version_app ?? '1.0.0' }}</h4></div>
                    <div class="info-box mb-3"><label>PHP Version</label><h4>{{ $php_version ?? phpversion() }}</h4></div>
                    <div class="info-box mb-3"><label>Laravel Version</label><h4>{{ $laravel_version ?? app()->version() }}</h4></div>
                    <div class="info-box mb-3"><label>Base de données</label><h4>{{ $db_name ?? 'MySQL' }}</h4></div>
                    <div class="info-box"><label>Taille base de données</label><h4>{{ $db_size ?? '0 MB' }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-database me-2"></i>Cache système</h3></div>
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3"><span>Configuration</span><span class="badge {{ $cache['config'] ? 'badge-success' : 'badge-danger' }}">{{ $cache['config'] ? 'Cache activé' : 'Non activé' }}</span></div>
                    <div class="d-flex justify-content-between align-items-center mb-3"><span>Routes</span><span class="badge {{ $cache['routes'] ? 'badge-success' : 'badge-danger' }}">{{ $cache['routes'] ? 'Cache activé' : 'Non activé' }}</span></div>
                    <div class="d-flex justify-content-between align-items-center mb-3"><span>Vues</span><span class="badge {{ $cache['views'] ? 'badge-success' : 'badge-danger' }}">{{ $cache['views'] ? 'Cache activé' : 'Non activé' }}</span></div>
                    <div class="mt-4 d-flex gap-3">
                        <form method="POST" action="{{ route('admin.systeme.clear-cache') }}">@csrf<button type="submit" class="btn-warning-custom"><i class="fas fa-trash-alt me-2"></i>Vider le cache</button></form>
                        <form method="POST" action="{{ route('admin.systeme.optimize') }}">@csrf<button type="submit" class="btn-primary-custom"><i class="fas fa-rocket me-2"></i>Optimiser</button></form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Statistiques système</h3></div>
        <div class="p-4">
            <div class="row g-4">
                <div class="col-md-3"><div class="info-box text-center"><label>Total utilisateurs</label><h2 class="text-primary">{{ $stats['total_users'] ?? 0 }}</h2></div></div>
                <div class="col-md-3"><div class="info-box text-center"><label>Total écritures</label><h2 class="text-success">{{ $stats['total_ecritures'] ?? 0 }}</h2></div></div>
                <div class="col-md-3"><div class="info-box text-center"><label>Total logs</label><h2 class="text-warning">{{ $stats['total_logs'] ?? 0 }}</h2></div></div>
                <div class="col-md-3"><div class="info-box text-center"><label>Dernier backup</label><h6>{{ $stats['last_backup'] ?? 'Jamais' }}</h6></div></div>
            </div>
        </div>
    </div>

    <div class="quick-actions mt-4">
        <div class="quick-header"><i class="fas fa-bolt me-2"></i> Actions système</div>
        <div class="quick-grid">
            <form method="POST" action="{{ route('admin.systeme.backup') }}" class="d-inline">@csrf<button type="submit" class="quick-btn bg-transparent border-0"><i class="fas fa-database"></i> Créer un backup</button></form>
            <a href="{{ route('admin.systeme.backups') }}" class="quick-btn"><i class="fas fa-history"></i> Voir backups</a>
            <a href="{{ route('admin.systeme.info') }}" class="quick-btn"><i class="fas fa-info-circle"></i> Infos détaillées</a>
            <a href="{{ route('admin.systeme.logs') }}" class="quick-btn"><i class="fas fa-file-alt"></i> Logs Laravel</a>
        </div>
    </div>
</div>
@push('styles')<style>.info-box{background:#f8fafc;padding:15px;border-radius:16px;border:1px solid #e2e8f0}.info-box label{font-size:11px;text-transform:uppercase;color:#64748b;letter-spacing:.5px;display:block;margin-bottom:5px}</style>@endpush
@endsection