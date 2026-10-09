@extends('layouts.admin')

@section('title', 'Détail du log')
@section('header-title', '👁️ Détail du log système')
@section('header-subtitle', 'Informations complètes de l\'événement')

@section('admin-content')
<div class="fade-up">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-info-circle me-2"></i>Informations générales</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3"><label>ID</label><h4 class="text-primary">#{{ $log->id }}</h4></div>
                    <div class="info-box mb-3"><label>Action</label><h4><span class="badge-info">{{ $log->action }}</span></h4></div>
                    <div class="info-box mb-3"><label>Table concernée</label><h4>{{ $log->table ?? 'Non spécifiée' }}</h4></div>
                    <div class="info-box mb-3"><label>Utilisateur</label><h4>{{ $log->user?->name ?? 'Système' }} ({{ $log->user?->email ?? 'N/A' }})</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-network-wired me-2"></i>Informations techniques</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3"><label>Adresse IP</label><h4>{{ $log->ip }}</h4></div>
                    <div class="info-box mb-3"><label>User Agent</label><p class="small">{{ $log->user_agent ?? 'Non disponible' }}</p></div>
                    <div class="info-box mb-3"><label>Date de création</label><h4>{{ $log->created_at->format('d/m/Y à H:i:s') }}</h4></div>
                    <div class="info-box"><label>Dernière mise à jour</label><h4>{{ $log->updated_at->format('d/m/Y à H:i:s') }}</h4></div>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-align-left me-2"></i>Détails complets</h3></div>
        <div class="p-4">
            <div class="bg-light p-4 rounded-3">
                <pre class="mb-0" style="white-space: pre-wrap; font-family: monospace;">{{ $log->details ?? 'Aucun détail disponible' }}</pre>
            </div>
        </div>
        <div class="p-4 border-top d-flex gap-3">
            <a href="{{ route('admin.logs.index') }}" class="btn btn-light px-4"><i class="fas fa-arrow-left me-2"></i>Retour</a>
            <button onclick="window.print()" class="btn-primary-custom"><i class="fas fa-print me-2"></i>Imprimer</button>
        </div>
    </div>
</div>
@endsection