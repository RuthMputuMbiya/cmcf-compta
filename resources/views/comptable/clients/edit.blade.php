@extends('layouts.comptable')

@section('title', 'Modifier client')
@section('header-title', 'Modifier le client')
@section('header-subtitle', 'Modification des informations client')

@section('comptable-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h3 class="card-header-title">Modifier le client</h3>
                <p class="card-header-subtitle">ID: {{ $client->id }} - {{ $client->nom }}</p>
            </div>
        </div>
        
        <div class="form-container">
            <form method="POST" action="{{ route('comptable.clients.update', $client->id) }}">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Nom <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="nom" value="{{ old('nom', $client->nom) }}" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Téléphone
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" name="telephone" value="{{ old('telephone', $client->telephone) }}" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Adresse
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input type="text" name="adresse" value="{{ old('adresse', $client->adresse) }}" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
    <a href="{{ url('/comptable/clients') }}" class="btn-cancel">
        <i class="fas fa-arrow-left"></i> Annuler
    </a>
    <button type="submit" class="btn-submit">
        <i class="fas fa-save"></i> Mettre à jour
    </button>
</div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ============================================
       DESIGN PROFESSIONNEL - COINS CARRÉS
       COULEURS SOBRES ET ÉLÉGANTES
    ============================================ */
    
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #3b82f6;
        --gray: #64748b;
        --gray-light: #f8fafc;
        --gray-border: #e2e8f0;
        --dark: #1e293b;
        --white: #ffffff;
        --danger: #ef4444;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    /* Coins carrés */
    .data-card, .card-header-custom, .card-header-icon,
    .form-control, .input-wrapper, .btn-submit, .btn-cancel {
        border-radius: 0px !important;
    }
    
    .data-card {
        background: var(--white);
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-border);
        overflow: hidden;
    }
    
    /* En-tête */
    .card-header-custom {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-icon {
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--white);
    }
    
    .card-header-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--white);
    }
    
    .card-header-subtitle {
        font-size: 0.75rem;
        margin: 0.25rem 0 0;
        color: rgba(255,255,255,0.7);
    }
    
    /* Formulaire */
    .form-container {
        padding: 1.5rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }
    
    .form-group {
        margin-bottom: 0;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.813rem;
        color: var(--dark);
    }
    
    .form-label i {
        margin-right: 0.5rem;
        color: var(--primary);
        width: 1rem;
    }
    
    .required {
        color: var(--danger);
        margin-left: 0.25rem;
    }
    
    .input-wrapper {
        position: relative;
    }
    
    .input-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
        font-size: 0.875rem;
        pointer-events: none;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.25rem;
        border: 1px solid var(--gray-border);
        font-size: 0.875rem;
        transition: var(--transition);
        background: var(--white);
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    }
    
    /* Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--gray-border);
    }
    
    .btn-submit {
        background: var(--primary);
        border: none;
        padding: 0.75rem 1.75rem;
        color: var(--white);
        font-weight: 600;
        font-size: 0.875rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }
    
    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-cancel {
        background: var(--white);
        border: 1px solid var(--gray-border);
        padding: 0.75rem 1.75rem;
        color: var(--gray);
        font-weight: 600;
        font-size: 0.875rem;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-cancel:hover {
        background: var(--gray-light);
        color: var(--dark);
    }
    
    /* Animation */
    .fade-up {
        animation: fadeInUp 0.4s ease-out;
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
    
    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-submit, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
        
        .card-header-custom {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endpush
@endsection