@extends('layouts.admin')

@section('title', 'Modifier utilisateur')
@section('header-title', '✏️ Modifier utilisateur')
@section('header-subtitle', 'Modification des informations du compte')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h3 class="card-header-title">Modification du compte</h3>
                <p class="card-header-subtitle">Modifiez les informations de l'utilisateur {{ $user->name }}</p>
            </div>
        </div>
        
        <div class="p-4">
            <form method="POST" action="{{ url('/admin/users/' . $user->id) }}" id="editUserForm">
                @csrf 
                @method('PUT')
                
                <!-- Section 1: Informations personnelles -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user-circle"></i>
                        <span>Informations personnelles</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-id-card text-primary me-1"></i>Matricule
                            </label>
                            <input type="text" value="{{ $user->matricule }}" class="form-control-custom" disabled>
                            <small class="text-muted">Le matricule ne peut pas être modifié</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-1"></i>Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-1"></i>Prénom
                            </label>
                            <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" class="form-control-custom">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Coordonnées -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-address-card"></i>
                        <span>Coordonnées</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-envelope text-primary me-1"></i>Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control-custom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-phone text-primary me-1"></i>Téléphone
                            </label>
                            <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="form-control-custom">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt text-primary me-1"></i>Adresse
                            </label>
                            <input type="text" name="adresse" value="{{ old('adresse', $user->adresse) }}" class="form-control-custom">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Sécurité -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-lock"></i>
                        <span>Sécurité</span>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>Rôle <span class="text-danger">*</span>
                            </label>
                            <select name="role_id" class="form-control-custom" required>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                    {{ $role->nom }} ({{ $role->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-key text-primary me-1"></i>Nouveau mot de passe
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="password" class="form-control-custom" placeholder="Laisser vide pour conserver">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-check-circle text-primary me-1"></i>Confirmer
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control-custom" placeholder="Confirmer le nouveau mot de passe">
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check-custom">
                                <input type="checkbox" name="actif" id="actif" value="1" {{ $user->actif ? 'checked' : '' }} class="form-check-input">
                                <label for="actif" class="form-check-label">
                                    <i class="fas fa-circle-check"></i>
                                    Compte actif
                                </label>
                                <small class="text-muted d-block mt-1">Décochez pour désactiver le compte utilisateur</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password strength meter -->
                    <div class="password-strength mt-3" id="strengthContainer" style="display: none;">
                        <div class="strength-meter">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small id="strengthText" class="text-muted"></small>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ url('/admin/users') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Card Header */
    .card-header-custom {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    
    .card-header-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: white;
    }
    
    .card-header-subtitle {
        font-size: 12px;
        margin: 5px 0 0;
        color: rgba(255,255,255,0.8);
    }
    
    /* Form Sections */
    .form-section {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .section-title i {
        color: #8b5cf6;
        font-size: 18px;
    }
    
    /* Form Labels */
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
        border-color: #8b5cf6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
    }
    
    .form-control-custom:disabled {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
    }
    
    /* Password Wrapper */
    .password-wrapper {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0;
    }
    
    .password-toggle:hover {
        color: #8b5cf6;
    }
    
    /* Checkbox Custom */
    .form-check-custom {
        background: white;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .form-check-input {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        cursor: pointer;
        accent-color: #8b5cf6;
    }
    
    .form-check-label {
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
    }
    
    .form-check-label i {
        color: #10b981;
        margin-right: 5px;
    }
    
    /* Password Strength */
    .password-strength {
        margin-top: 10px;
    }
    
    .strength-meter {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        overflow: hidden;
    }
    
    .strength-fill {
        height: 100%;
        width: 0;
        transition: width 0.3s ease;
        border-radius: 2px;
    }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
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
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
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
        color: #8b5cf6;
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
    
    /* Loading button */
    .btn-submit.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .btn-submit.loading i {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
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

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Password strength meter
    const passwordInput = document.getElementById('password');
    const strengthContainer = document.getElementById('strengthContainer');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length > 0) {
                strengthContainer.style.display = 'block';
                let strength = 0;
                let message = '';
                let color = '';
                
                if (password.length >= 6) strength += 25;
                if (password.match(/[a-z]+/)) strength += 25;
                if (password.match(/[A-Z]+/)) strength += 25;
                if (password.match(/[0-9]+/)) strength += 25;
                if (password.match(/[$@#&!]+/)) strength += 25;
                
                if (strength <= 25) {
                    message = 'Très faible';
                    color = '#ef4444';
                } else if (strength <= 50) {
                    message = 'Faible';
                    color = '#f59e0b';
                } else if (strength <= 75) {
                    message = 'Moyen';
                    color = '#3b82f6';
                } else {
                    message = 'Fort';
                    color = '#10b981';
                }
                
                strengthFill.style.width = Math.min(strength, 100) + '%';
                strengthFill.style.backgroundColor = color;
                strengthText.textContent = message;
                strengthText.style.color = color;
            } else {
                strengthContainer.style.display = 'none';
                strengthFill.style.width = '0';
                strengthText.textContent = '';
            }
        });
    }
    
    // Form submission with loading effect
    const form = document.getElementById('editUserForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Ajouter l'effet de chargement
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Mise à jour en cours...';
            submitBtn.disabled = true;
        });
    }
</script>
@endpush
@endsection