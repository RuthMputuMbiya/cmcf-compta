@extends('layouts.caissier')

@section('title', 'Décaissement')
@section('header-title', '💸 Décaissement')
@section('header-subtitle', 'Enregistrer une sortie de caisse')

@section('caissier-content')
<div class="fade-up">
    
    <!-- Alerte solde insuffisant -->
    @if(($solde ?? 0) < 0)
    <div class="alert alert-danger-custom mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Attention :</strong> Solde de caisse insuffisant ({{ number_format($solde ?? 0, 0, ',', ' ') }} FC)
    </div>
    @elseif(($solde ?? 0) < 100000)
    <div class="alert alert-warning-custom mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Attention :</strong> Solde de caisse faible ({{ number_format($solde ?? 0, 0, ',', ' ') }} FC)
    </div>
    @endif

    <!-- Carte solde -->
    <div class="solde-card mb-4">
        <div class="solde-card-header">
            <div class="solde-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <h3>Solde actuel disponible</h3>
                <p>Montant disponible en caisse</p>
            </div>
        </div>
        <div class="solde-card-body">
            <div class="solde-value {{ ($solde ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format(abs($solde ?? 0), 0, ',', ' ') }} FC
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="card-header-custom danger">
            <div class="card-header-icon"><i class="fas fa-minus-circle"></i></div>
            <div>
                <h3 class="card-header-title">Nouveau décaissement</h3>
                <p class="card-header-subtitle">Enregistrer une sortie d'argent en caisse</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/caissier/caisse/sortie') }}" id="sortieForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt text-danger me-1"></i>Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control-custom" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-money-bill-wave text-danger me-1"></i>Montant (FC) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group-custom">
                            <span class="input-group-text">FC</span>
                            <input type="number" name="montant" id="montant" class="form-control-custom" placeholder="0" step="0.01" required>
                        </div>
                        <small class="text-muted">Solde disponible : {{ number_format($solde ?? 0, 0, ',', ' ') }} FC</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-tag text-danger me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="libelle" class="form-control-custom" placeholder="Ex: Paiement fournisseur, Achat matériel, Frais divers..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-hashtag text-danger me-1"></i>Référence
                        </label>
                        <input type="text" name="reference" class="form-control-custom" placeholder="N° facture, bon de commande...">
                        <small class="text-muted">Optionnel</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sticky-note text-danger me-1"></i>Observations
                        </label>
                        <textarea name="observation" class="form-control-custom" rows="2" placeholder="Motif du décaissement..."></textarea>
                    </div>
                </div>
                
                <!-- Alerte dépassement -->
                <div id="alerteDepassement" class="alert alert-danger-custom mt-3" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Solde insuffisant !</strong> Le montant demandé dépasse le solde disponible.
                </div>
                
                <!-- Récapitulatif -->
                <div class="recap-box mt-4" id="recapBox" style="display: none;">
                    <div class="recap-header danger">
                        <i class="fas fa-receipt"></i> Récapitulatif de l'opération
                    </div>
                    <div class="recap-body">
                        <div class="row">
                            <div class="col-md-4"><strong>Date :</strong> <span id="recapDate"></span></div>
                            <div class="col-md-4"><strong>Montant :</strong> <span id="recapMontant" class="text-danger fw-bold"></span></div>
                            <div class="col-md-4"><strong>Type :</strong> <span class="badge-danger">SORTIE</span></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12"><strong>Nouveau solde :</strong> <span id="nouveauSolde" class="fw-bold"></span></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ url('/caissier/caisse') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Enregistrer le décaissement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .alert-danger-custom {
        background: #fee2e2;
        border-left: 4px solid #ef4444;
        color: #991b1b;
        border-radius: 12px;
        padding: 15px;
    }
    .alert-warning-custom {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        color: #92400e;
        border-radius: 12px;
        padding: 15px;
    }
    .solde-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .solde-card-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
    }
    .solde-card-header h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }
    .solde-card-header p {
        font-size: 11px;
        margin: 2px 0 0;
        opacity: 0.8;
    }
    .solde-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .solde-card-body {
        padding: 20px;
        text-align: center;
    }
    .solde-value {
        font-size: 32px;
        font-weight: 800;
    }
    .card-header-custom.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    .recap-header.danger {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .btn-submit {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    .btn-submit:hover {
        box-shadow: 0 4px 12px rgba(239,68,68,0.3);
    }
    .loading {
        opacity: 0.7;
        cursor: not-allowed;
    }
    .loading i {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script>
    const soldeActuel = {{ $solde ?? 0 }};
    const montantInput = document.getElementById('montant');
    const recapBox = document.getElementById('recapBox');
    const recapDate = document.getElementById('recapDate');
    const recapMontant = document.getElementById('recapMontant');
    const nouveauSoldeSpan = document.getElementById('nouveauSolde');
    const alerteDepassement = document.getElementById('alerteDepassement');
    const dateInput = document.querySelector('input[name="date"]');
    const submitBtn = document.getElementById('submitBtn');
    
    function updateRecap() {
        let montant = parseFloat(montantInput.value) || 0;
        let nouveauSolde = soldeActuel - montant;
        
        if(montant > 0) {
            recapBox.style.display = 'block';
            recapDate.innerHTML = dateInput.value ? new Date(dateInput.value).toLocaleDateString('fr-FR') : '-';
            recapMontant.innerHTML = montant.toLocaleString('fr-FR') + ' FC';
            nouveauSoldeSpan.innerHTML = nouveauSolde.toLocaleString('fr-FR') + ' FC';
            
            if(montant > soldeActuel) {
                alerteDepassement.style.display = 'block';
                nouveauSoldeSpan.classList.add('text-danger');
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
            } else {
                alerteDepassement.style.display = 'none';
                nouveauSoldeSpan.classList.remove('text-danger');
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
            
            if(montant > 0 && montant <= soldeActuel) {
                nouveauSoldeSpan.classList.add('text-success');
            } else {
                nouveauSoldeSpan.classList.remove('text-success');
            }
        } else {
            recapBox.style.display = 'none';
            alerteDepassement.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
    }
    
    montantInput.addEventListener('input', updateRecap);
    dateInput.addEventListener('change', updateRecap);
    
    document.getElementById('sortieForm')?.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Enregistrement en cours...';
        submitBtn.disabled = true;
    });
</script>
@endpush
@endsection