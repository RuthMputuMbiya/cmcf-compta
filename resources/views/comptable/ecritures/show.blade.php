@extends('layouts.comptable')

@section('title', 'Détail de l\'écriture')
@section('header-title', '👁️ Détail de l\'écriture comptable')
@section('header-subtitle', 'Consultation de l\'écriture N° {{ $ecriture->numero_piece }}')

@section('comptable-content')
<div class="fade-up">
    <div class="row g-4">
        <!-- Colonne gauche - Informations générales -->
        <div class="col-md-7">
            <div class="data-card">
                <div class="card-header-custom">
                    <div class="card-header-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <h3 class="card-header-title">Informations générales</h3>
                        <p class="card-header-subtitle">Détails de l'écriture comptable</p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-hashtag text-primary me-1"></i> N° Pièce</label>
                                <h4 class="text-primary">{{ $ecriture->numero_piece }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-calendar-alt text-primary me-1"></i> Date</label>
                                <h4>{{ $ecriture->date->format('d/m/Y') }}</h4>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box">
                                <label><i class="fas fa-align-left text-primary me-1"></i> Libellé</label>
                                <p class="fs-5">{{ $ecriture->libelle }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-tag text-primary me-1"></i> Statut</label>
                                <div>
                                    @if($ecriture->statut == 'valide')
                                        <span class="badge-success"><i class="fas fa-check-circle me-1"></i> Validé</span>
                                    @elseif($ecriture->statut == 'rejete')
                                        <span class="badge-danger"><i class="fas fa-times-circle me-1"></i> Rejeté</span>
                                    @else
                                        <span class="badge-warning"><i class="fas fa-clock me-1"></i> Brouillon</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-user-check text-primary me-1"></i> Saisi par</label>
                                <p>{{ $ecriture->saisiPar->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($ecriture->valide_par)
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-check-double text-primary me-1"></i> Validé par</label>
                                <p>{{ $ecriture->validePar->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-calendar-check text-primary me-1"></i> Date de validation</label>
                                <p>{{ $ecriture->valide_le->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne droite - Montants -->
        <div class="col-md-5">
            <div class="data-card">
                <div class="card-header-custom purple">
                    <div class="card-header-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h3 class="card-header-title">Montants</h3>
                        <p class="card-header-subtitle">Détail des montants débit/crédit</p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="montant-card debit-card">
                        <div class="montant-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="montant-info">
                            <span class="montant-label">COMPTE DÉBIT</span>
                            <span class="montant-compte">{{ $ecriture->compte_debit }}</span>
                            <span class="montant-value">{{ number_format($ecriture->montant_debit, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>
                    
                    <div class="montant-card credit-card mt-3">
                        <div class="montant-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="montant-info">
                            <span class="montant-label">COMPTE CRÉDIT</span>
                            <span class="montant-compte">{{ $ecriture->compte_credit }}</span>
                            <span class="montant-value">{{ number_format($ecriture->montant_credit, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>
                    
                    <div class="equilibre-box mt-3">
                        <div class="equilibre-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="equilibre-info">
                            <span>Équilibre comptable</span>
                            <strong class="text-success">Débit = Crédit</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comptes détaillés -->
    <div class="data-card mt-4">
        <div class="card-header-custom info">
            <div class="card-header-icon">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <h3 class="card-header-title">Comptes comptables</h3>
                <p class="card-header-subtitle">Détail des comptes utilisés</p>
            </div>
        </div>
        <div class="p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="compte-detail debit-detail">
                        <div class="compte-detail-header">
                            <i class="fas fa-arrow-down text-success"></i>
                            <strong>COMPTE DÉBIT</strong>
                        </div>
                        <div class="compte-detail-body">
                            <div class="compte-info">
                                <label>Numéro de compte</label>
                                <span class="compte-numero">{{ $ecriture->compte_debit }}</span>
                            </div>
                            <div class="compte-info">
                                <label>Libellé du compte</label>
                                <span>{{ $ecriture->compte_debit_nom ?? 'Non défini' }}</span>
                            </div>
                            <div class="compte-info">
                                <label>Type de compte</label>
                                <span class="badge-info">Débit</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="compte-detail credit-detail">
                        <div class="compte-detail-header">
                            <i class="fas fa-arrow-up text-danger"></i>
                            <strong>COMPTE CRÉDIT</strong>
                        </div>
                        <div class="compte-detail-body">
                            <div class="compte-info">
                                <label>Numéro de compte</label>
                                <span class="compte-numero">{{ $ecriture->compte_credit }}</span>
                            </div>
                            <div class="compte-info">
                                <label>Libellé du compte</label>
                                <span>{{ $ecriture->compte_credit_nom ?? 'Non défini' }}</span>
                            </div>
                            <div class="compte-info">
                                <label>Type de compte</label>
                                <span class="badge-info">Crédit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="data-card mt-4">
        <div class="card-header-custom secondary">
            <div class="card-header-icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h3 class="card-header-title">Historique</h3>
                <p class="card-header-subtitle">Dates de création et modifications</p>
            </div>
        </div>
        <div class="p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="historique-box">
                        <i class="fas fa-plus-circle text-primary"></i>
                        <div>
                            <label>Création</label>
                            <p>{{ $ecriture->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="historique-box">
                        <i class="fas fa-edit text-warning"></i>
                        <div>
                            <label>Dernière modification</label>
                            <p>{{ $ecriture->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
                @if($ecriture->valide_le)
                <div class="col-md-3">
                    <div class="historique-box">
                        <i class="fas fa-check-circle text-success"></i>
                        <div>
                            <label>Validation</label>
                            <p>{{ $ecriture->valide_le->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-end gap-3 mt-4">
        <a href="{{ url('/comptable/ecritures') }}" class="btn-cancel">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
        @if($ecriture->statut != 'valide')
        <a href="{{ url('/comptable/ecritures/' . $ecriture->id . '/edit') }}" class="btn-warning-custom">
            <i class="fas fa-edit me-2"></i>Modifier
        </a>
        <button onclick="validerEcriture({{ $ecriture->id }})" class="btn-success-custom">
            <i class="fas fa-check-double me-2"></i>Valider
        </button>
        @endif
        <button onclick="window.print()" class="btn-secondary-custom">
            <i class="fas fa-print me-2"></i>Imprimer
        </button>
    </div>
</div>

@push('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-custom.purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }
    
    .card-header-custom.info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }
    
    .card-header-custom.secondary {
        background: linear-gradient(135deg, #64748b, #475569);
    }
    
    .card-header-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
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
    
    .info-box {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
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
    
    .info-box .fs-5 {
        font-size: 16px;
    }
    
    /* Montant Cards */
    .montant-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        border-radius: 16px;
        transition: all 0.3s;
    }
    
    .montant-card:hover {
        transform: translateY(-3px);
    }
    
    .debit-card {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #a7f3d0;
    }
    
    .credit-card {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1px solid #fecaca;
    }
    
    .montant-icon {
        width: 50px;
        height: 50px;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .debit-card .montant-icon {
        background: #10b981;
        color: white;
    }
    
    .credit-card .montant-icon {
        background: #ef4444;
        color: white;
    }
    
    .montant-info {
        flex: 1;
    }
    
    .montant-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        display: block;
    }
    
    .montant-compte {
        font-size: 20px;
        font-weight: 700;
        display: block;
        margin: 5px 0;
    }
    
    .debit-card .montant-compte {
        color: #059669;
    }
    
    .credit-card .montant-compte {
        color: #dc2626;
    }
    
    .montant-value {
        font-size: 18px;
        font-weight: 800;
        display: block;
    }
    
    .debit-card .montant-value {
        color: #10b981;
    }
    
    .credit-card .montant-value {
        color: #ef4444;
    }
    
    /* Équilibre box */
    .equilibre-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .equilibre-icon {
        width: 40px;
        height: 40px;
        background: #d1fae5;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #10b981;
        font-size: 18px;
    }
    
    .equilibre-info {
        flex: 1;
    }
    
    .equilibre-info span {
        font-size: 12px;
        color: #64748b;
        display: block;
    }
    
    .equilibre-info strong {
        font-size: 16px;
    }
    
    /* Compte détail */
    .compte-detail {
        border-radius: 16px;
        overflow: hidden;
    }
    
    .compte-detail-header {
        padding: 12px 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .debit-detail .compte-detail-header {
        background: #10b981;
        color: white;
    }
    
    .credit-detail .compte-detail-header {
        background: #ef4444;
        color: white;
    }
    
    .compte-detail-body {
        padding: 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-top: none;
    }
    
    .compte-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .compte-info:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .compte-info label {
        font-size: 12px;
        color: #64748b;
    }
    
    .compte-info span {
        font-weight: 600;
        color: #1e293b;
    }
    
    .compte-numero {
        font-family: monospace;
        font-size: 14px;
        background: #e2e8f0;
        padding: 2px 8px;
        border-radius: 6px;
    }
    
    /* Historique */
    .historique-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .historique-box i {
        font-size: 24px;
    }
    
    .historique-box label {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        display: block;
        margin-bottom: 3px;
    }
    
    .historique-box p {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
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
    
    .badge-warning {
        background: #fef3c7;
        color: #d97706;
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
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
    }
    
    /* Buttons */
    .btn-warning-custom {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        padding: 10px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 14px;
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
    
    .btn-success-custom {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        padding: 10px 24px;
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
    
    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #64748b, #475569);
        border: none;
        padding: 10px 24px;
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
    
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(100,116,139,0.3);
    }
    
    .btn-cancel {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px 24px;
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
    
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        .d-flex.justify-content-end {
            flex-wrap: wrap;
            justify-content: center !important;
        }
        .btn-cancel, .btn-warning-custom, .btn-success-custom, .btn-secondary-custom {
            width: 100%;
            justify-content: center;
        }
        .card-header-custom {
            flex-direction: column;
            text-align: center;
        }
        .compte-info {
            flex-direction: column;
            gap: 5px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function validerEcriture(id) {
        if(confirm('✓ Valider cette écriture ? Cette action est irréversible.')) {
            window.location.href = '/comptable/ecritures/' + id + '/valider';
        }
    }
</script>
@endpush
@endsection