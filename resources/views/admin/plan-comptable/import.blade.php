@extends('layouts.admin')

@section('title', 'Import plan comptable')
@section('header-title', '📥 Import du plan comptable')
@section('header-subtitle', 'Importation des comptes depuis fichier Excel')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="card-header-custom">
            <div class="card-header-icon">
                <i class="fas fa-file-excel"></i>
            </div>
            <div>
                <h3 class="card-header-title">Importer un fichier Excel</h3>
                <p class="card-header-subtitle">Choisissez un fichier Excel/CSV à importer</p>
            </div>
        </div>
        
        <div class="p-4">
            @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form method="POST" action="{{ url('/admin/plan-comptable/import') }}" enctype="multipart/form-data" id="importForm">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Fichier Excel (XLSX, XLS, CSV)</label>
                    <input type="file" name="fichier" id="fichier" class="form-control-custom" accept=".xlsx,.xls,.csv" required>
                    <small class="text-muted">Taille maximale: 5 MB</small>
                    <div id="fileInfo" class="mt-2" style="display: none;">
                        <span class="badge-info" id="fileName"></span>
                        <span class="badge-info" id="fileSize"></span>
                    </div>
                </div>
                
                <div class="alert alert-info mb-4">
                    <strong><i class="fas fa-info-circle me-2"></i>Format du fichier attendu :</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>numero</strong> - Numéro de compte (ex: 411)</li>
                        <li><strong>nom</strong> - Nom du compte (ex: Clients)</li>
                        <li><strong>classe</strong> - Classe (1 à 9)</li>
                        <li><strong>type</strong> - Type (Actif, Passif, Charge, Produit)</li>
                        <li><strong>solde_initial</strong> - Solde initial (optionnel)</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning mb-4">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Important :</strong>
                    <ul class="mb-0 mt-2">
                        <li>Le fichier doit être au format CSV ou Excel (.xlsx, .xls)</li>
                        <li>La première ligne doit contenir les en-têtes de colonnes</li>
                        <li>Les numéros de compte doivent être uniques</li>
                    </ul>
                </div>
                
                <div class="d-flex gap-3">
                    <a href="{{ url('/admin/plan-comptable/template') }}" class="btn-warning-custom" id="downloadTemplate">
                        <i class="fas fa-download me-2"></i>Télécharger le template
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-upload me-2"></i>Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
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
    .alert-info {
        background: #e0f2fe;
        border: none;
        color: #0369a1;
        border-radius: 16px;
        padding: 15px;
    }
    .alert-success {
        background: #d1fae5;
        border: none;
        color: #065f46;
        border-radius: 16px;
        padding: 15px;
    }
    .alert-danger {
        background: #fee2e2;
        border: none;
        color: #991b1b;
        border-radius: 16px;
        padding: 15px;
    }
    .alert-warning {
        background: #fef3c7;
        border: none;
        color: #92400e;
        border-radius: 16px;
        padding: 15px;
    }
    .btn-submit {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
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
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139,92,246,0.3);
    }
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
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
        color: white;
    }
    .badge-info {
        background: #dbeafe;
        color: #2563eb;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-right: 8px;
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
        .d-flex.gap-3 {
            flex-direction: column;
        }
        .btn-submit, .btn-warning-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Afficher les informations du fichier sélectionné
    document.getElementById('fichier')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        
        if (file) {
            // Vérifier la taille du fichier (5 MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Le fichier est trop volumineux. Taille maximale: 5 MB');
                this.value = '';
                fileInfo.style.display = 'none';
                return;
            }
            
            // Vérifier l'extension
            const allowedExtensions = ['.xlsx', '.xls', '.csv'];
            const fileNameLower = file.name.toLowerCase();
            const isValid = allowedExtensions.some(ext => fileNameLower.endsWith(ext));
            
            if (!isValid) {
                alert('Format de fichier non supporté. Utilisez .xlsx, .xls ou .csv');
                this.value = '';
                fileInfo.style.display = 'none';
                return;
            }
            
            // Afficher les infos
            fileName.innerHTML = '<i class="fas fa-file me-1"></i> ' + file.name;
            let sizeInKB = (file.size / 1024).toFixed(2);
            if (sizeInKB > 1024) {
                fileSize.innerHTML = '<i class="fas fa-database me-1"></i> ' + (sizeInKB / 1024).toFixed(2) + ' MB';
            } else {
                fileSize.innerHTML = '<i class="fas fa-database me-1"></i> ' + sizeInKB + ' KB';
            }
            fileInfo.style.display = 'block';
        } else {
            fileInfo.style.display = 'none';
        }
    });
    
    // Désactiver le bouton pendant l'import
    document.getElementById('importForm')?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importation en cours...';
        submitBtn.disabled = true;
        
        // Réactiver après 30 secondes (au cas où)
        setTimeout(function() {
            if (submitBtn.disabled) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }, 30000);
    });
    
    // Auto-fermeture des alertes après 5 secondes
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-success, .alert-danger');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 4000);
        });
    }, 1000);
</script>
@endpush
@endsection