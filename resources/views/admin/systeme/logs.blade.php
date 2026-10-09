@extends('layouts.admin')

@section('title', 'Logs Laravel')
@section('header-title', '📄 Logs Laravel')
@section('header-subtitle', 'Fichiers de log de l\'application')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="p-4">
            <div class="mb-3">
                <button onclick="window.location.reload()" class="btn-primary-custom"><i class="fas fa-sync-alt me-2"></i>Rafraîchir</button>
            </div>
            <div class="bg-dark text-white p-4 rounded-3" style="max-height: 600px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                @forelse($logs as $log)
                <div class="mb-1 {{ str_contains($log, 'ERROR') ? 'text-danger' : (str_contains($log, 'WARNING') ? 'text-warning' : 'text-white') }}">
                    {{ $log }}
                </div>
                @empty
                <div class="text-muted">Aucun log disponible</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection