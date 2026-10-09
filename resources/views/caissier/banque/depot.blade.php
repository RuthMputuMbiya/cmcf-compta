@extends('layouts.caissier')

@section('title', 'Dépôt bancaire')
@section('header-title', '🏦 Dépôt bancaire')
@section('header-subtitle', 'Enregistrer un dépôt sur le compte bancaire')

@section('caissier-content')
<div class="fade-up">

    <!-- Carte solde -->
    <div class="solde-card mb-4">
        <div class="solde-card-header">
            <div class="solde-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <h3>Solde bancaire actuel</h3>
                <p>Montant disponible sur le compte</p>
            </div>
        </div>
        <div class="solde-card-body">
            <div class="solde-value text-primary">
                {{ number_format($solde ?? 0, 0, ',', ' ') }} FC
            </div>
        </div>
    </div>

    <!-- Formulaire de dépôt -->
    <div class="data-card">
        <div class="card-header-custom success">
            <div class="card-header-icon"><i class="fas fa-plus-circle"></i></div>
            <div>
                <h3 class="card-header-title">Nouveau dépôt bancaire</h3>
                <p class="card-header-subtitle">Enregistrer un versement sur le compte</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/caissier/banque/depot') }}" id="depotForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>Date du dépôt <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control-custom" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-money-bill-wave text-primary me-1"></i>Montant (FC) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group-custom">
                            <span class="input-group-text">FC</span>
                            <input type="number" name="montant" id="montant" class="form-control-custom" placeholder="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-tag text-primary me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="libelle" class="form-control-custom" placeholder="Ex: Dépôt espèces, Virement client, Encaissement..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-hashtag text-primary me-1"></i>Numéro de référence
                        </label>
                        <input type="text" name="reference" class="form-control-custom" placeholder="N° bordereau, N° chèque...">
                        <small class="text-muted">Optionnel</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-exchange-alt text-primary me-1"></i>Mode de versement
                        </label>
                        <select name="mode" class="form-control-custom">
                            <option value="especes">💰 Espèces</option>
                            <option value="cheque">📄 Chèque</option>
                            <option value="virement">💻 Virement bancaire</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sticky-note text-primary me-1"></i>Observations
                        </label>
                        <textarea name="observation" class="form-control-custom" rows="3" placeholder="Informations complémentaires..."></textarea>
                    </div>
                </div>

                <!-- Récapitulatif -->
                <div class="recap-box mt-4" id="recapBox" style="display: none;">
                    <div class="recap-header success">
                        <i class="fas fa-receipt"></i> Récapitulatif du dépôt
                    </div>
                    <div class="recap-body">
                        <div class="row">
                            <div class="col-md-4"><strong>Date :</strong> <span id="recapDate"></span></div>
                            <div class="col-md-4"><strong>Montant :</strong> <span id="recapMontant" class="text-success fw-bold"></span></div>
                            <div class="col-md-4"><strong>Mode :</strong> <span id="recapMode"></span></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ url('/caissier/banque') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Enregistrer le dépôt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Informations importantes -->
    <div class="info-card mt-4">
        <div class="info-card-header">
            <i class="fas fa-info-circle"></i> Informations importantes
        </div>
        <div class="info-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-item">
                        <i class="fas fa-check-circle text-success"></i>
                        <h6>Justificatif requis</h6>
                        <p>Conservez le reçu ou bordereau comme justificatif</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <i class="fas fa-clock text-warning"></i>
                        <h6>Délai de validation</h6>
                        <p>Les dépôts sont validés sous 24h</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <h6>Sécurité</h6>
                        <p>Toutes les opérations sont tracées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
        padding: 10px 15px;
        font-weight: 600;
        background: #e2e8f0;
        color: #1e293b;
    }
    .recap-header.success {
        background: #d1fae5;
        color: #065f46;
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
    .info-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .info-card-header {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 12px 20px;
        color: white;
        font-weight: 600;
    }
    .info-card-body {
        padding: 20px;
    }
    .info-item {
        text-align: center;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        height: 100%;
    }
    .info-item i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }
    .info-item h6 {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .info-item p {
        font-size: 11px;
        color: #64748b;
        margin: 0;
    }
    .text-muted {
        color: #94a3b8;
        font-size: 11px;
        margin-top: 5px;
        display: block;
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
        .info-item {
            margin-bottom: 15px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const montantInput = document.getElementById('montant');
    const recapMontant = document.getElementById('recapMontant');
    const recapDate = document.getElementById('recapDate');
    const recapMode = document.getElementById('recapMode');
    const modeSelect = document.querySelector('select[name="mode"]');
    const dateInput = document.querySelector('input[name="date"]');
    const recapBox = document.getElementById('recapBox');
    const submitBtn = document.getElementById('submitBtn');

    function updateRecap() {
        let montant = parseFloat(montantInput.value) || 0;
        if(montant > 0) {
            recapBox.style.display = 'block';
            recapMontant.innerHTML = montant.toLocaleString('fr-FR') + ' FC';
        } else {
            recapBox.style.display = 'none';
        }
    }

    montantInput.addEventListener('input', updateRecap);
    
    dateInput.addEventListener('change', function() {
        if(this.value) {
            let date = new Date(this.value);
            recapDate.innerHTML = date.toLocaleDateString('fr-FR');
        }
    });

    modeSelect.addEventListener('change', function() {
        recapMode.innerHTML = this.options[this.selectedIndex].text;
    });

    document.getElementById('depotForm')?.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Enregistrement en cours...';
        submitBtn.disabled = true;
    });
</script>
@endpush
@endsection