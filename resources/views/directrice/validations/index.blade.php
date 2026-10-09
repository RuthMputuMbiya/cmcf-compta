@extends('layouts.directrice')

@section('title', 'Validations')
@section('header-title', '✅ Opérations à valider')
@section('header-subtitle', 'Validation des écritures comptables')

@section('directrice-content')
<div class="fade-up">

    <div class="stats-grid mb-4">
        <div class="stat-card stat-warning"><div class="stat-icon"><i class="fas fa-clock"></i></div><div class="stat-label">En attente</div><div class="stat-value">{{ $operations->total() }}</div></div>
        <div class="stat-card stat-primary"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div class="stat-label">Montant total</div><div class="stat-value">{{ number_format($total_montant, 0, ',', ' ') }} FC</div></div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>N° Pièce</th><th>Date</th><th>Libellé</th><th>Compte débit</th><th>Compte crédit</th><th>Montant</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($operations as $op)
                    <tr>
                        <td><strong class="text-primary">{{ $op->numero_piece }}</strong></td>
                        <td>{{ $op->date->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($op->libelle, 40) }}</td>
                        <td>{{ $op->compte_debit }}</td>
                        <td>{{ $op->compte_credit }}</td>
                        <td class="text-success">{{ number_format($op->montant_debit, 0, ',', ' ') }} FC</span>
                        <td>
                            <div class="d-flex gap-2">
                                <button onclick="valider({{ $op->id }})" class="btn-validate"><i class="fas fa-check-circle"></i> Valider</button>
                                <button onclick="rejeter({{ $op->id }})" class="btn-reject"><i class="fas fa-times-circle"></i> Rejeter</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $operations->links() }}</div>
    </div>
</div>

<!-- Modal rejet -->
<div id="rejectModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header"><h5 class="modal-title"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Motif du rejet</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body"><textarea name="motif" class="form-control-custom" rows="3" placeholder="Veuillez indiquer le motif du rejet..." required></textarea></div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn-danger-custom">Confirmer le rejet</button></div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function valider(id) { if(confirm('Valider cette opération ?')) window.location.href = `/directrice/valider/${id}`; }
    function rejeter(id) { 
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        document.getElementById('rejectForm').action = `/directrice/rejeter/${id}`;
        modal.show();
    }
</script>
@endpush
@endsection