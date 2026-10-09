@props(['showCompte' => true, 'showClient' => false, 'showFournisseur' => false])

<div class="filters-card mb-4">
    <div class="filters-header">
        <div class="filters-icon">
            <i class="fas fa-filter"></i>
        </div>
        <div>
            <h3 class="filters-title">Filtres de recherche</h3>
            <p class="filters-subtitle">Affinez votre recherche</p>
        </div>
    </div>
    <div class="filters-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i>Date début
                </label>
                <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i>Date fin
                </label>
                <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
            </div>
            @if($showCompte)
            <div class="col-md-3">
                <label class="form-label">
                    <i class="fas fa-book me-1 text-primary"></i>Compte comptable
                </label>
                <select id="compte" class="form-control-custom">
                    <option value="">Tous les comptes</option>
                    @foreach($comptes ?? [] as $compte)
                    <option value="{{ $compte->numero }}" {{ request('compte') == $compte->numero ? 'selected' : '' }}>
                        {{ $compte->numero }} - {{ $compte->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($showClient)
            <div class="col-md-3">
                <label class="form-label">
                    <i class="fas fa-user me-1 text-primary"></i>Client
                </label>
                <select id="client" class="form-control-custom">
                    <option value="">Tous les clients</option>
                    @foreach($clients ?? [] as $client)
                    <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                        {{ $client->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($showFournisseur)
            <div class="col-md-3">
                <label class="form-label">
                    <i class="fas fa-truck me-1 text-primary"></i>Fournisseur
                </label>
                <select id="fournisseur" class="form-control-custom">
                    <option value="">Tous les fournisseurs</option>
                    @foreach($fournisseurs ?? [] as $fournisseur)
                    <option value="{{ $fournisseur->id }}" {{ request('fournisseur') == $fournisseur->id ? 'selected' : '' }}>
                        {{ $fournisseur->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button onclick="applyFilters()" class="btn-filter w-100">
                    <i class="fas fa-search me-2"></i>Filtrer
                </button>
                <button onclick="resetFilters()" class="btn-reset">
                    <i class="fas fa-undo-alt"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .filters-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .filters-header {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .filters-icon {
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .filters-title {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    .filters-subtitle {
        font-size: 11px;
        margin: 2px 0 0;
        color: rgba(255,255,255,0.8);
    }
    .filters-body {
        padding: 20px;
    }
    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .form-control-custom {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        transition: all 0.2s;
        background: white;
    }
    .form-control-custom:focus {
        border-color: #8b5cf6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }
    .btn-filter {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
    }
    .btn-reset {
        background: #64748b;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-reset:hover {
        background: #475569;
        transform: translateY(-2px);
    }
    @media (max-width: 768px) {
        .d-flex.align-items-end {
            margin-top: 10px;
        }
        .btn-filter, .btn-reset {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        let url = window.location.pathname + '?';
        const date_debut = document.getElementById('date_debut')?.value;
        const date_fin = document.getElementById('date_fin')?.value;
        const compte = document.getElementById('compte')?.value;
        const client = document.getElementById('client')?.value;
        const fournisseur = document.getElementById('fournisseur')?.value;
        
        if(date_debut) url += 'date_debut=' + date_debut + '&';
        if(date_fin) url += 'date_fin=' + date_fin + '&';
        if(compte) url += 'compte=' + compte + '&';
        if(client) url += 'client=' + client + '&';
        if(fournisseur) url += 'fournisseur=' + fournisseur + '&';
        
        window.location.href = url.slice(0, -1);
    }
    
    function resetFilters() {
        window.location.href = window.location.pathname;
    }
</script>
@endpush