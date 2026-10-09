@extends('layouts.comptable')

@section('title', 'Détail client')
@section('header-title', '👁️ Détail du client')
@section('header-subtitle', 'Informations et historique des écritures')

@section('comptable-content')
<div class="fade-up">
    <div class="row g-4">
        <!-- Carte Informations client -->
        <div class="col-md-5">
            <div class="data-card">
                <div class="data-header">
                    <div>
                        <h3><i class="fas fa-user-circle me-2"></i>Informations client</h3>
                        <p class="text-muted mb-0">Détails personnels et contact</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="info-card">
                        <div class="info-icon client-bg">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <label><i class="fas fa-user me-1"></i> Nom complet</label>
                                <h4>{{ $client->nom }}</h4>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-envelope me-1"></i> Email</label>
                                <p>{{ $client->email ?? 'Non renseigné' }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-phone me-1"></i> Téléphone</label>
                                <p>{{ $client->telephone ?? 'Non renseigné' }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-map-marker-alt me-1"></i> Adresse</label>
                                <p>{{ $client->adresse ?? 'Non renseignée' }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-calendar-alt me-1"></i> Date d'ajout</label>
                                <p>{{ $client->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-chart-line me-1"></i> Total transactions</label>
                                <p class="text-primary">{{ number_format($total_achats ?? 0, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-actions">
                    <a href="{{ url('/comptable/clients/' . $client->id . '/edit') }}" class="btn-warning-custom">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <a href="{{ url('/comptable/clients/' . $client->id . '/delete') }}" class="btn-danger-custom" onclick="return confirm('⚠️ Supprimer le client \"{{ $client->nom }}\" ? Cette action est irréversible.')">
                        <i class="fas fa-trash-alt me-2"></i>Supprimer
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Carte Écritures comptables associées -->
        <div class="col-md-7">
            <div class="data-card">
                <div class="data-header">
                    <div>
                        <h3><i class="fas fa-book me-2"></i>Écritures comptables</h3>
                        <p class="text-muted mb-0">Historique des écritures du client</p>
                    </div>
                    <div>
                        <a href="{{ url('/comptable/ecritures/create?client_id=' . $client->id) }}" class="btn-primary-custom">
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
                            @forelse($client->ecritures as $ecriture)
                            <tr>
                                <td><strong class="text-primary">{{ $ecriture->numero_piece }}</strong></td>
                                <td>{{ $ecriture->date->format('d/m/Y') }}</td>
                                <td>{{ Str::limit($ecriture->libelle, 40) }}</td>
                                <td><span class="badge-compte">{{ $ecriture->compte_debit }}</span></td>
                                <td class="text-success">{{ number_format($ecriture->montant_debit, 0, ',', ' ') }} FL</span>
                                <td><span class="badge-compte">{{ $ecriture->compte_credit }}</span></td>
                                <td class="text-danger">{{ number_format($ecriture->montant_credit, 0, ',', ' ') }} FL</span>
                                <td>
                                    @if($ecriture->statut == 'valide')
                                        <span class="badge-valid"><i class="fas fa-check-circle me-1"></i>Validée</span>
                                    @else
                                        <span class="badge-draft"><i class="fas fa-pencil-alt me-1"></i>Brouillon</span>
                                    @endif
                                </span
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
                                </span
                            </tr>
                            @empty
                            <tr class="text-center">
                                <td colspan="9" class="py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <h5>Aucune écriture trouvée</h5>
                                        <p class="text-muted">Ce client n'a aucune écriture comptable</p>
                                        <a href="{{ url('/comptable/ecritures/create?client_id=' . $client->id) }}" class="btn-primary-custom mt-2">
                                            <i class="fas fa-plus-circle me-2"></i>Créer une écriture
                                        </a>
                                    </div>
                                </span
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
        margin-bottom: 25px;
        height: 100%;
    }
    
    .data-header {
        padding: 18px 22px;
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
    
    /* Info Card */
    .info-card {
        background: #f8fafc;
        border-radius: 20px;
        padding: 5px;
        display: flex;
        gap: 20px;
    }
    
    .info-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .client-bg {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
    }
    
    .info-icon i {
        font-size: 36px;
        color: white;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-item {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .info-item label {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 5px;
    }
    
    .info-item h4 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }
    
    .info-item p {
        font-size: 14px;
        margin: 0;
        color: #475569;
    }
    
    .card-actions {
        padding: 18px 22px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
        background: #fafbfc;
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
        padding: 10px 22px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        flex: 1;
        justify-content: center;
    }
    
    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245,158,11,0.4);
        color: white;
    }
    
    .btn-danger-custom {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        padding: 10px 22px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        flex: 1;
        justify-content: center;
        cursor: pointer;
    }
    
    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239,68,68,0.4);
        color: white;
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
    
    .badge-compte {
        background: #e0e7ff;
        color: #4f46e5;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        font-family: monospace;
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
    
    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 30px;
    }
    
    .empty-state i {
        color: #cbd5e1;
    }
    
    .empty-state h5 {
        font-size: 16px;
        color: #64748b;
        margin: 15px 0 5px;
    }
    
    .empty-state p {
        font-size: 13px;
        color: #94a3b8;
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
        .info-card {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .info-icon {
            margin-bottom: 15px;
        }
        .card-actions {
            flex-direction: column;
        }
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
            width: 40%;
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