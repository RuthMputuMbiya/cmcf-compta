@extends('layouts.comptable')

@section('title', 'Nouveau fournisseur')
@section('header-title', '➕ Nouveau fournisseur')
@section('header-subtitle', 'Ajouter un fournisseur')

@section('comptable-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div>
                <h3 class="card-header-title">Informations du fournisseur</h3>
                <p class="card-header-subtitle">Remplissez tous les champs obligatoires</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/comptable/fournisseurs') }}">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-building text-primary me-1"></i>Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom" class="form-control-custom" placeholder="Nom du fournisseur" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-envelope text-primary me-1"></i>Email
                        </label>
                        <input type="email" name="email" class="form-control-custom" placeholder="fournisseur@email.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-phone text-primary me-1"></i>Téléphone
                        </label>
                        <input type="text" name="telephone" class="form-control-custom" placeholder="+243 XXX XXX XXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-map-marker-alt text-primary me-1"></i>Adresse
                        </label>
                        <input type="text" name="adresse" class="form-control-custom" placeholder="Adresse complète">
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ url('/comptable/fournisseurs') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save me-2"></i>Enregistrer le fournisseur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection