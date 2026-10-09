@extends('layouts.caissier')

@section('title', 'Encaissement')
@section('header-title', '💰 Encaissement')
@section('header-subtitle', 'Enregistrer une entrée en caisse')

@section('caissier-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom success">
            <div class="card-header-icon"><i class="fas fa-plus-circle"></i></div>
            <div>
                <h3 class="card-header-title">Nouvel encaissement</h3>
                <p class="card-header-subtitle">Enregistrer une entrée d'argent en caisse</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/caissier/caisse/entree') }}">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control-custom" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-money-bill-wave text-primary me-1"></i>Montant (FC) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group-custom">
                            <span class="input-group-text">FC</span>
                            <input type="number" name="montant" class="form-control-custom" placeholder="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-tag text-primary me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="libelle" class="form-control-custom" placeholder="Ex: Vente au comptant, Remboursement, Paiement client..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-hashtag text-primary me-1"></i>Référence
                        </label>
                        <input type="text" name="reference" class="form-control-custom" placeholder="N° facture, reçu, bordereau...">
                        <small class="text-muted">Optionnel</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sticky-note text-primary me-1"></i>Observations
                        </label>
                        <textarea name="observation" class="form-control-custom" rows="2" placeholder="Informations complémentaires..."></textarea>
                    </div>
                </div>
                
                <!-- Récapitulatif -->
                <div class="recap-box mt-4" id="recapBox" style="display: none;">
                    <div class="recap-header">
                        <i class="fas fa-receipt"></i> Récapitulatif de l'opération
                    </div>
                    <div class="recap-body">
                        <div class="row">
                            <div class="col-md-4"><strong>Date :</strong> <span id="recapDate"></span></div>
                            <div class="col-md-4"><strong>Montant :</strong> <span id="recapMontant" class="text-success fw-bold"></span></div>
                            <div class="col-md-4"><strong>Type :</strong> <span class="badge-success">ENTRÉE</span></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ url('/caissier/caisse') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Enregistrer l'encaissement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header-custom.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .card-header-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .card-header-title {
        font-size: 15px;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    .card-header-subtitle {
        font-size: 11px;
        margin: 3px 0 0;
        color: rgba(255,255,255,0.8);
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }
    .form-control-custom {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }
    .form-control-custom:focus {
        border-color: #10b981;
        outline: none;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    .input-group-custom {
        display: flex;
        align-items: center;
    }
    .input-group-text {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 12px 16px;
        border-radius: 12px 0 0 12px;
        font-weight: 600;
        color: #64748b;
    }
    .input-group-custom .form-control-custom {
        border-radius: 0 12px 12px 0;
        border-left: none;
    }
    .recap-box {
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .recap-header {
        background: #e2e8f0;
        padding: 10px 15px;
        font-weight: 600;
        color: #1e293b;
    }
    .recap-body {
        padding: 15px;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }
    .btn-submit {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    .btn-cancel {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 12px 28px;
        border-radius: 12px;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-cancel:hover {
        background: #f8fafc;
        color: #1e293b;
    }
    .text-muted {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 5px;
        display: block;
    }
    .text-danger {
        color: #ef4444;
    }
    .text-primary {
        color: #10b981;
    }
    .badge-success {
        background: #d1fae5;
        color: #059669;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
        .btn-submit, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
        .card-header-custom.success {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const dateInput = document.querySelector('input[name="date"]');
    const montantInput = document.querySelector('input[name="montant"]');
    const recapBox = document.getElementById('recapBox');
    const recapDate = document.getElementById('recapDate');
    const recapMontant = document.getElementById('recapMontant');
    const submitBtn = document.getElementById('submitBtn');
    
    function updateRecap() {
        if(montantInput.value && montantInput.value > 0) {
            recapBox.style.display = 'block';
            recapDate.innerHTML = dateInput.value ? new Date(dateInput.value).toLocaleDateString('fr-FR') : '-';
            recapMontant.innerHTML = parseInt(montantInput.value).toLocaleString('fr-FR') + ' FC';
        } else {
            recapBox.style.display = 'none';
        }
    }
    
    montantInput.addEventListener('input', updateRecap);
    dateInput.addEventListener('change', updateRecap);
    
    document.querySelector('form')?.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Enregistrement en cours...';
        submitBtn.disabled = true;
    });
</script>
@endpush
@endsection