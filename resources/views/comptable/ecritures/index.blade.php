@extends('layouts.comptable')

@section('title', 'Écritures comptables')
@section('header-title', '📝 Écritures comptables')
@section('header-subtitle', 'Gestion des écritures')

@section('comptable-content')
<div class="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <a href="{{ route('comptable.ecritures.create') }}" class="btn-primary-custom"><i class="fas fa-plus-circle me-2"></i>Nouvelle écriture</a>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" class="form-control-custom" placeholder="🔍 Rechercher..." style="width:250px">
            <select id="statutFilter" class="form-control-custom" style="width:130px">
                <option value="">Tous</option>
                <option value="brouillon">Brouillon</option>
                <option value="valide">Validé</option>
            </select>
        </div>
    </div>

    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary"><div class="stat-icon"><i class="fas fa-book"></i></div><div class="stat-label">Total</div><div class="stat-value">{{ $ecritures->total() }}</div></div>
        <div class="stat-card stat-warning"><div class="stat-icon"><i class="fas fa-pencil-alt"></i></div><div class="stat-label">Brouillons</div><div class="stat-value">{{ $ecritures->where('statut', 'brouillon')->count() }}</div></div>
        <div class="stat-card stat-success"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-label">Validées</div><div class="stat-value">{{ $ecritures->where('statut', 'valide')->count() }}</div></div>
        <div class="stat-card stat-info"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div class="stat-label">Montant total</div><div class="stat-value">{{ number_format($total_debit, 0, ',', ' ') }} FC</div></div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table" id="ecrituresTable">
                <thead>
                    <tr>
                        <th>N° Pièce</th>
                        <th>Date</th>
                        <th>Libellé</th>
                        <th>Débit</th>
                        <th>Crédit</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ecritures as $e)
                    <tr data-statut="{{ $e->statut }}">
                        <td><strong class="text-primary">{{ $e->numero_piece }}</strong></td>
                        <td>{{ $e->date->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($e->libelle, 50) }}</td>
                        <td class="text-success">{{ number_format($e->montant_debit, 0, ',', ' ') }} FC</span>
                        <td class="text-danger">{{ number_format($e->montant_credit, 0, ',', ' ') }} FC</span>
                        <td>
                            @if($e->statut=='valide')
                                <span class="badge-success">Validé</span>
                            @else
                                <span class="badge-warning">Brouillon</span>
                            @endif
                        </span>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('comptable.ecritures.show', $e->id) }}" class="text-primary" title="Voir"><i class="fas fa-eye"></i></a>
                                @if($e->statut != 'valide')
                                    <a href="{{ route('comptable.ecritures.edit', $e->id) }}" class="text-warning" title="Modifier"><i class="fas fa-edit"></i></a>
                                    <button onclick="validateEcriture({{ $e->id }})" class="text-success bg-transparent border-0" title="Valider"><i class="fas fa-check-double"></i></button>
                                @endif
                                <button onclick="deleteEcriture({{ $e->id }}, '{{ $e->numero_piece }}', '{{ $e->statut }}')" 
                                        class="text-danger bg-transparent border-0" 
                                        title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </span>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $ecritures->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
    function validateEcriture(id) {
        if(confirm('Valider cette écriture ?')) {
            window.location.href = `/comptable/ecritures/${id}/valider`;
        }
    }
    
    function deleteEcriture(id, numero, statut) {
        let message = `Supprimer l'écriture ${numero} ?`;
        if (statut === 'valide') {
            message = `⚠️ ATTENTION ! Cette écriture est VALIDÉE.\n\nSupprimer l'écriture ${numero} ?\n\nCette action est irréversible.`;
        }
        
        if(confirm(message)) {
            // Créer un formulaire et le soumettre
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = `/comptable/ecritures/${id}`;
            
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
    
    // Filtre recherche
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        let s = this.value.toLowerCase();
        document.querySelectorAll('#ecrituresTable tbody tr').forEach(r => {
            r.style.display = r.innerText.toLowerCase().includes(s) ? '' : 'none';
        });
    });
    
    // Filtre statut
    document.getElementById('statutFilter')?.addEventListener('change', function() {
        let v = this.value;
        document.querySelectorAll('#ecrituresTable tbody tr').forEach(r => {
            r.style.display = !v || r.dataset.statut === v ? '' : 'none';
        });
    });
</script>
@endpush
@endsection