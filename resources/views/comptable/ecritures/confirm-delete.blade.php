@extends('layouts.comptable')

@section('title', 'Confirmer la suppression')
@section('header-title', '🗑️ Suppression d\'une écriture')
@section('header-subtitle', 'Confirmation de suppression')

@section('comptable-content')
<div class="fade-up">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="data-card">
                @if($ecriture->statut === 'valide')
                <div class="alert alert-warning m-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                        <div>
                            <h5 class="mb-1">⚠️ Attention ! Écriture validée</h5>
                            <p class="mb-0">Cette écriture a déjà été validée. La suppression est irréversible.</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="alert alert-danger m-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-exclamation-circle fa-3x"></i>
                        <div>
                            <h5 class="mb-1">⚠️ Action irréversible</h5>
                            <p class="mb-0">Vous êtes sur le point de supprimer définitivement cette écriture comptable.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="info-box mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">N° Pièce</label>
                                    <h4 class="text-primary mb-0">{{ $ecriture->numero_piece }}</h4>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Date</label>
                                    <h4 class="mb-0">{{ $ecriture->date->format('d/m/Y') }}</h4>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="text-muted small">Libellé</label>
                                    <p class="mb-0">{{ $ecriture->libelle }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Compte débit</label>
                                    <p class="mb-0"><strong>{{ $ecriture->compte_debit }}</strong> - {{ $ecriture->compteDebiteur->nom ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Montant débit</label>
                                    <p class="mb-0 text-success">{{ number_format($ecriture->montant_debit, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Compte crédit</label>
                                    <p class="mb-0"><strong>{{ $ecriture->compte_credit }}</strong> - {{ $ecriture->compteCrediteur->nom ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Montant crédit</label>
                                    <p class="mb-0 text-danger">{{ number_format($ecriture->montant_credit, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Statut</label>
                                    <p class="mb-0">
                                        @if($ecriture->statut === 'valide')
                                            <span class="badge-valid">Validée</span>
                                        @else
                                            <span class="badge-draft">Brouillon</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Saisi par</label>
                                    <p class="mb-0">{{ $ecriture->saisiPar->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ url('/comptable/ecritures/' . $ecriture->id) }}" method="POST" 
                          onsubmit="return confirm('Confirmation finale : Voulez-vous vraiment supprimer définitivement cette écriture ?')">
                        @csrf
                        @method('DELETE')
                        
                        <div class="d-flex justify-content-between gap-3">
                            <a href="{{ url('/comptable/ecritures') }}" class="btn btn-secondary btn-lg flex-grow-1">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg flex-grow-1">
                                <i class="fas fa-trash-alt me-2"></i>Oui, supprimer définitivement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fade-up { animation: fadeInUp 0.5s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .data-card { background: white; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
    .info-box { background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; }
    .badge-valid { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-draft { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
    .btn-secondary { background: #f1f5f9; border: none; padding: 12px 24px; border-radius: 12px; color: #1e293b; font-weight: 600; }
    .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 12px 24px; border-radius: 12px; color: white; font-weight: 600; }
    @media (max-width: 768px) {
        .info-box .row { flex-direction: column; }
        .d-flex.gap-3 { flex-direction: column; }
        .btn-secondary, .btn-danger { width: 100%; text-align: center; justify-content: center; }
    }
</style>
@endpush
@endsection