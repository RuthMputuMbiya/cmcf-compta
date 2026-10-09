@extends('layouts.comptable')

@section('title', 'Import plan comptable')
@section('header-title', '📥 Import du plan comptable')
@section('header-subtitle', 'Importation des comptes depuis fichier Excel')

@section('comptable-content')
<div class="fade-up">
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-file-excel"></i>
            <div>
                <h3>Importer un fichier Excel</h3>
                <p>Choisissez un fichier Excel/CSV à importer</p>
            </div>
        </div>
        
        <div class="form-body">
            @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
            
            <form method="POST" action="{{ url('/comptable/plan-comptable/import') }}" enctype="multipart/form-data" id="importForm">
                @csrf
                
                <div class="file-upload">
                    <label class="upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Cliquez pour sélectionner un fichier</span>
                        <input type="file" name="fichier" accept=".xlsx,.xls,.csv" required>
                    </label>
                    <div id="fileInfo" class="file-info" style="display: none;">
                        <i class="fas fa-file"></i>
                        <span id="fileName"></span>
                        <span id="fileSize"></span>
                    </div>
                </div>
                
                <div class="info-box">
                    <strong><i class="fas fa-info-circle me-2"></i>Format du fichier attendu :</strong>
                    <ul>
                        <li><strong>numero</strong> - Numéro de compte (ex: 411)</li>
                        <li><strong>nom</strong> - Nom du compte (ex: Clients)</li>
                        <li><strong>classe</strong> - Classe (1 à 9)</li>
                        <li><strong>type</strong> - Type (Actif, Passif, Charge, Produit)</li>
                        <li><strong>solde_initial</strong> - Solde initial (optionnel)</li>
                    </ul>
                </div>
                
                <div class="warning-box">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Important :</strong>
                    <ul>
                        <li>Le fichier doit être au format CSV ou Excel (.xlsx, .xls)</li>
                        <li>La première ligne doit contenir les en-têtes de colonnes</li>
                        <li>Les numéros de compte doivent être uniques</li>
                    </ul>
                </div>
                
                <div class="form-actions">
                    <a href="{{ url('/comptable/plan-comptable/template') }}" class="btn-warning">
                        <i class="fas fa-download me-2"></i>Télécharger le template
                    </a>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-upload me-2"></i>Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fade-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        max-width: 800px;
        margin: 0 auto;
        overflow: hidden;
    }
    
    .form-header {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .form-header i {
        font-size: 40px;
        color: white;
    }
    
    .form-header h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: white;
    }
    
    .form-header p {
        font-size: 12px;
        margin: 5px 0 0;
        color: rgba(255,255,255,0.8);
    }
    
    .form-body {
        padding: 25px;
    }
    
    .alert-success, .alert-error {
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .file-upload {
        margin-bottom: 25px;
    }
    
    .upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }
    
    .upload-label:hover {
        border-color: #6366f1;
        background: #f8fafc;
    }
    
    .upload-label i {
        font-size: 48px;
        color: #94a3b8;
        margin-bottom: 10px;
    }
    
    .upload-label span {
        color: #64748b;
        font-size: 13px;
    }
    
    .upload-label input {
        display: none;
    }
    
    .file-info {
        margin-top: 15px;
        padding: 12px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }
    
    .info-box, .warning-box {
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    .info-box {
        background: #e0e7ff;
        color: #1e40af;
    }
    
    .warning-box {
        background: #fef3c7;
        color: #92400e;
    }
    
    .info-box ul, .warning-box ul {
        margin: 10px 0 0 20px;
        font-size: 12px;
    }
    
    .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 20px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99,102,241,0.4);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.4);
        color: white;
    }
    
    @media (max-width: 640px) {
        .form-actions {
            flex-direction: column;
        }
        .btn-primary, .btn-warning {
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('fichier')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        
        if (file) {
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Le fichier est trop volumineux. Taille maximale: 5 MB');
                this.value = '';
                fileInfo.style.display = 'none';
                return;
            }
            
            fileName.innerHTML = file.name;
            let sizeInKB = (file.size / 1024).toFixed(2);
            if (sizeInKB > 1024) {
                fileSize.innerHTML = (sizeInKB / 1024).toFixed(2) + ' MB';
            } else {
                fileSize.innerHTML = sizeInKB + ' KB';
            }
            fileInfo.style.display = 'flex';
        }
    });
    
    document.getElementById('importForm')?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importation...';
        submitBtn.disabled = true;
    });
</script>
@endpush
@endsection