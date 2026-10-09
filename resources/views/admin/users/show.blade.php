@extends('layouts.admin')

@section('title', 'Détail utilisateur')
@section('header-title', '👁️ Détail de l\'utilisateur')
@section('header-subtitle', 'Informations complètes du compte')

@section('admin-content')
<div class="fade-up">
    <div class="row g-4">
        <!-- Colonne gauche - Informations personnelles -->
        <div class="col-md-5">
            <div class="data-card">
                <div class="card-header-custom">
                    <div class="card-header-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="card-header-title">Informations personnelles</h3>
                        <p class="card-header-subtitle">Détails du compte utilisateur</p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="info-box mb-3">
                        <label><i class="fas fa-id-card me-1 text-primary"></i> Matricule</label>
                        <h4 class="text-primary">{{ $user->matricule }}</h4>
                    </div>
                    <div class="info-box mb-3">
                        <label><i class="fas fa-user me-1 text-primary"></i> Nom complet</label>
                        <h4>{{ $user->name }} {{ $user->prenom }}</h4>
                    </div>
                    <div class="info-box mb-3">
                        <label><i class="fas fa-envelope me-1 text-primary"></i> Email</label>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="info-box mb-3">
                        <label><i class="fas fa-phone me-1 text-primary"></i> Téléphone</label>
                        <p>{{ $user->telephone ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="info-box">
                        <label><i class="fas fa-map-marker-alt me-1 text-primary"></i> Adresse</label>
                        <p>{{ $user->adresse ?? 'Non renseignée' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne droite - Informations compte -->
        <div class="col-md-7">
            <div class="data-card">
                <div class="card-header-custom purple">
                    <div class="card-header-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div>
                        <h3 class="card-header-title">Informations compte</h3>
                        <p class="card-header-subtitle">Statut et permissions</p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="info-box mb-3">
                        <label><i class="fas fa-tag me-1 text-primary"></i> Rôle</label>
                        <h4><span class="badge-info">{{ $user->role->nom ?? 'N/A' }}</span> ({{ $user->role->code ?? 'N/A' }})</h4>
                    </div>
                    <div class="info-box mb-3">
                        <label><i class="fas fa-circle me-1 text-primary"></i> Statut</label>
                        <h4>
                            @if($user->actif)
                                <span class="badge-success"><i class="fas fa-check-circle me-1"></i>Actif</span>
                            @else
                                <span class="badge-danger"><i class="fas fa-ban me-1"></i>Inactif</span>
                            @endif
                        </h4>
                    </div>
                    <div class="info-box mb-3">
                        <label><i class="fas fa-calendar-plus me-1 text-primary"></i> Date d'inscription</label>
                        <h4>{{ $user->created_at->format('d/m/Y à H:i') }}</h4>
                    </div>
                    <div class="info-box">
                        <label><i class="fas fa-calendar-check me-1 text-primary"></i> Dernière connexion</label>
                        <h4>{{ $user->derniere_connexion?->format('d/m/Y à H:i') ?? 'Jamais connecté' }}</h4>
                    </div>
                </div>
                <div class="p-4 border-top d-flex gap-3">
                    <a href="{{ url('/admin/users/' . $user->id . '/edit') }}" class="btn-warning-custom">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    @if($user->role && $user->role->code != 'AD')
                    <button onclick="toggleStatus({{ $user->id }}, '{{ $user->actif ? 'désactiver' : 'activer' }}')" class="btn-info-custom">
                        <i class="fas fa-toggle-{{ $user->actif ? 'on' : 'off' }} me-2"></i>
                        {{ $user->actif ? 'Désactiver' : 'Activer' }}
                    </button>
                    @endif
                    <a href="{{ url('/admin/users') }}" class="btn btn-light px-4">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente -->
    <div class="data-card mt-4">
        <div class="card-header-custom info">
            <div class="card-header-icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h3 class="card-header-title">Activité récente</h3>
                <p class="card-header-subtitle">Historique des actions de l'utilisateur</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-bolt me-1"></i> Action</th>
                        <th><i class="fas fa-database me-1"></i> Table</th>
                        <th><i class="fas fa-info-circle me-1"></i> Détails</th>
                        <th><i class="fas fa-network-wired me-1"></i> IP</th>
                        <th><i class="fas fa-calendar me-1"></i> Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activite as $log)
                    <tr>
                        <td><span class="badge-info">{{ $log->action }}</span></td>
                        <td>{{ $log->table ?? '-' }}</span>
                        <td>{{ Str::limit($log->details, 50) }}</span>
                        <td><span class="badge-light">{{ $log->ip }}</span></span>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Aucune activité</h5>
                                <p class="text-muted">Cet utilisateur n'a pas encore d'activité</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Card Header Styles */
    .card-header-custom {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-custom.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }
    
    .card-header-custom.info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
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
    
    /* Info Box */
    .info-box {
        background: #f8fafc;
        padding: 15px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    
    .info-box:hover {
        border-color: #cbd5e1;
        background: #ffffff;
    }
    
    .info-box label {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 8px;
    }
    
    .info-box h4 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }
    
    .info-box p {
        margin: 0;
        font-size: 14px;
        color: #475569;
    }
    
    /* Badges */
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge-info {
        background: #dbeafe;
        color: #2563eb;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-light {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-family: monospace;
    }
    
    /* Buttons */
    .btn-info-custom {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        border: none;
        padding: 10px 22px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .btn-info-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(6,182,212,0.3);
    }
    
    .btn-warning-custom {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        padding: 10px 22px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    }
    
    .btn-light {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px 22px;
        border-radius: 12px;
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-light:hover {
        background: #f8fafc;
        color: #1e293b;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
    }
    
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
    }
    
    .empty-state h5 {
        font-size: 16px;
        color: #64748b;
        margin: 15px 0 5px;
    }
    
    .empty-state p {
        font-size: 13px;
        color: #94a3b8;
    }
    
    /* Tableau */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table thead th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .data-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .row.g-4 {
            flex-direction: column;
        }
        .d-flex.gap-3 {
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn-info-custom, .btn-warning-custom, .btn-light {
            width: 100%;
            justify-content: center;
        }
        .data-table thead {
            display: none;
        }
        .data-table tbody tr {
            display: block;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        .data-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        .data-table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 11px;
            color: #64748b;
        }
    }
    
    /* Animations */
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
</style>
@endpush

@push('scripts')
<script>
    function toggleStatus(id, action) {
        if(confirm(`${action} cet utilisateur ?`)) {
            window.location.href = `/admin/users/${id}/toggle`;
        }
    }
</script>
@endpush
@endsection