@extends('layouts.comptable')

@section('title', 'Détail du fournisseur')
@section('header-title', '🏢 Détail du fournisseur')
@section('header-subtitle', 'Informations complètes du fournisseur')

@section('comptable-content')
<div class="fade-up">
    <!-- Carte Informations générales -->
    <div class="row g-4">
        <div class="col-md-12">
            <div class="data-card">
                <div class="data-header">
                    <div>
                        <h3><i class="fas fa-info-circle me-2"></i>Informations générales</h3>
                        <p class="text-muted mb-0">Détails du fournisseur</p>
                    </div>
                    <div>
                        <a href="{{ url('/comptable/fournisseurs/' . $fournisseur->id . '/edit') }}" class="btn-warning-custom me-2">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <a href="{{ url('/comptable/fournisseurs/' . $fournisseur->id . '/delete') }}" class="btn-danger-custom">
                            <i class="fas fa-trash-alt me-1"></i>Supprimer
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-user me-1"></i> Nom complet</label>
                                <h4>{{ $fournisseur->nom }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-envelope me-1"></i> Email</label>
                                <h4>{{ $fournisseur->email ?? 'Non renseigné' }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-phone me-1"></i> Téléphone</label>
                                <h4>{{ $fournisseur->telephone }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-map-marker-alt me-1"></i> Adresse</label>
                                <h4>{{ $fournisseur->adresse ?? 'Non renseignée' }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-calendar-alt me-1"></i> Date d'ajout</label>
                                <h4>{{ $fournisseur->created_at->format('d/m/Y H:i') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label><i class="fas fa-chart-line me-1"></i> Total achats</label>
                                <h4 class="text-danger">{{ number_format($total_achats ?? 0, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Écritures liées -->
    <div class="data-card mt-4">
        <div class="data-header">
            <div>
                <h3><i class="fas fa-file-invoice me-2"></i>Écritures liées</h3>
                <p class="text-muted mb-0">Liste des écritures associées à ce fournisseur</p>
            </div>
            <div>
                <a href="{{ url('/comptable/ecritures/create') }}" class="btn-primary-custom">
                    <i class="fas fa-plus-circle me-1"></i>Nouvelle écriture
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Pièce</th>
                        <th>Date</th>
                        <th>Libellé</th>
                        <th>Compte débit</th>
                        <th>Montant débit</th>
                        <th>Compte crédit</th>
                        <th>Montant crédit</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fournisseur->ecritures as $ecriture)
                    <tr>
                        <td><strong class="text-primary">{{ $ecriture->numero_piece }}</strong></td>
                        <td>{{ $ecriture->date->format('d/m/Y') }}</span>
                        <td>{{ Str::limit($ecriture->libelle, 40) }}</span>
                        <td class="text-primary">{{ $ecriture->compte_debit }}</span>
                        <td class="text-success">{{ number_format($ecriture->montant_debit, 0, ',', ' ') }}</span>
                        <td class="text-primary">{{ $ecriture->compte_credit }}</span>
                        <td class="text-danger">{{ number_format($ecriture->montant_credit, 0, ',', ' ') }}</span>
                        <td>
                            @if($ecriture->statut == 'valide')
                                <span class="badge-valid"><i class="fas fa-check-circle me-1"></i>Validé</span>
                            @else
                                <span class="badge-draft"><i class="fas fa-pencil-alt me-1"></i>Brouillon</span>
                            @endif
                        </span>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ url('/comptable/ecritures/' . $ecriture->id) }}" class="action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($ecriture->statut != 'valide')
                                    <a href="{{ url('/comptable/ecritures/' . $ecriture->id . '/edit') }}" class="action-btn edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="validateEcriture({{ $ecriture->id }})" class="action-btn validate" title="Valider">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @endif
                                <button onclick="deleteEcriture({{ $ecriture->id }}, '{{ $ecriture->numero_piece }}')" class="action-btn delete" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </span>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <td colspan="9" class="py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <h5>Aucune écriture trouvée</h5>
                            <p class="text-muted">Aucune écriture n'est associée à ce fournisseur</p>
                            <a href="{{ url('/comptable/ecritures/create') }}" class="btn-primary-custom mt-2">
                                <i class="fas fa-plus-circle me-2"></i>Créer une écriture
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
    
    /* Data Card */
    .data-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    
    .data-header {
        padding: 18px 25px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        background: linear-gradient(135deg, #f8fafc, #ffffff);
    }
    
    .data-header h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }
    
    /* Info Box */
    .info-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }
    
    .info-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
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
    
    /* Badges */
    .badge-valid {
        background: #d1fae5;
        color: #059669;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-draft {
        background: #fef3c7;
        color: #d97706;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Boutons */
    .btn-primary-custom {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99,102,241,0.4);
        color: white;
    }
    
    .btn-warning-custom {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.4);
        color: white;
    }
    
    .btn-danger-custom {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.4);
        color: white;
    }
    
    /* Tableau */
    .table-responsive {
        overflow-x: auto;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table thead th {
        background: #f8fafc;
        padding: 14px 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .data-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }
    
    .action-btn.view {
        background: #e0e7ff;
        color: #4f46e5;
    }
    
    .action-btn.view:hover {
        background: #4f46e5;
        color: white;
        transform: translateY(-2px);
    }
    
    .action-btn.edit {
        background: #fef3c7;
        color: #d97706;
    }
    
    .action-btn.edit:hover {
        background: #d97706;
        color: white;
        transform: translateY(-2px);
    }
    
    .action-btn.validate {
        background: #d1fae5;
        color: #059669;
    }
    
    .action-btn.validate:hover {
        background: #059669;
        color: white;
        transform: translateY(-2px);
    }
    
    .action-btn.delete {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .action-btn.delete:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Text colors */
    .text-primary {
        color: #4f46e5 !important;
    }
    .text-success {
        color: #059669 !important;
    }
    .text-danger {
        color: #dc2626 !important;
    }
    .text-muted {
        color: #64748b !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .data-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .data-table thead {
            display: none;
        }
        .data-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        .data-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .data-table tbody td:last-child {
            border-bottom: none;
        }
        .data-table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 11px;
            color: #64748b;
            width: 35%;
        }
        .action-buttons {
            justify-content: flex-end;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function validateEcriture(id) {
        if(confirm('Valider cette écriture ?')) {
            window.location.href = '/comptable/ecritures/' + id + '/valider';
        }
    }
    
    function deleteEcriture(id, numero) {
        if(confirm(`Supprimer l'écriture ${numero} ?`)) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/comptable/ecritures/' + id;
            
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            let methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Ajouter les labels pour responsive
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        let cells = row.querySelectorAll('td');
        if(cells.length >= 9) {
            cells[0].setAttribute('data-label', 'N° Pièce');
            cells[1].setAttribute('data-label', 'Date');
            cells[2].setAttribute('data-label', 'Libellé');
            cells[3].setAttribute('data-label', 'Compte débit');
            cells[4].setAttribute('data-label', 'Montant débit');
            cells[5].setAttribute('data-label', 'Compte crédit');
            cells[6].setAttribute('data-label', 'Montant crédit');
            cells[7].setAttribute('data-label', 'Statut');
            cells[8].setAttribute('data-label', 'Actions');
        }
    });
</script>
@endpush
@endsection