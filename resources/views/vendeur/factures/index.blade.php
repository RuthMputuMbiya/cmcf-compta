@extends('layouts.vendeur')

@section('title', 'Gestion des factures')
@section('header-title', '📄 Factures')
@section('header-subtitle', 'Gestion des factures clients')

@section('vendeur-content')
<div class="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <a href="{{ route('vendeur.factures.create') }}" class="btn-primary-custom"><i class="fas fa-plus-circle me-2"></i>Nouvelle facture</a>
        <div class="d-flex gap-2">
            <input type="text" id="searchFacture" class="form-control-custom" placeholder="🔍 N° facture, client..." style="width: 250px;">
            <select id="statutFilter" class="form-control-custom" style="width: 130px;">
                <option value="">Tous</option>
                <option value="payee">Payée</option>
                <option value="impayee">Impayée</option>
            </select>
        </div>
    </div>

    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary"><div class="stat-icon"><i class="fas fa-file-invoice"></i></div><div class="stat-label">Total factures</div><div class="stat-value">{{ $factures->total() }}</div></div>
        <div class="stat-card stat-success"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-label">Payées</div><div class="stat-value">{{ $factures->where('statut', 'payee')->count() }}</div></div>
        <div class="stat-card stat-danger"><div class="stat-icon"><i class="fas fa-clock"></i></div><div class="stat-label">Impayées</div><div class="stat-value">{{ $factures->where('statut', 'impayee')->count() }}</div></div>
        <div class="stat-card stat-info"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div class="stat-label">Montant total</div><div class="stat-value">{{ number_format($total_ttc, 0, ',', ' ') }} FC</div></div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table" id="facturesTable">
                <thead>
                    <tr><th>N° Facture</th><th>Date</th><th>Client</th><th>Montant HT</th><th>TVA</th><th>Montant TTC</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($factures as $facture)
                    <tr data-statut="{{ $facture->statut }}" data-numero="{{ $facture->numero }}" data-client="{{ strtolower($facture->client->nom) }}">
                        <td><strong class="text-primary">{{ $facture->numero }}</strong></td>
                        <td>{{ $facture->date->format('d/m/Y') }}</td>
                        <td>{{ $facture->client->nom }}</td>
                        <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }} FC</span>
                        <td>{{ number_format($facture->tva, 0, ',', ' ') }} FC</span>
                        <td class="fw-bold">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FC</span>
                        <td>@if($facture->statut == 'payee')<span class="badge-success">Payée</span>@else<span class="badge-danger">Impayée</span>@endif</span>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('vendeur.factures.show', $facture->id) }}" class="text-primary" title="Voir/Imprimer"><i class="fas fa-print"></i></a>
                                @if($facture->statut != 'payee')
                                <a href="{{ route('vendeur.factures.edit', $facture->id) }}" class="text-warning" title="Modifier"><i class="fas fa-edit"></i></a>
                                <button onclick="supprimer({{ $facture->id }}, '{{ $facture->numero }}')" class="text-danger bg-transparent border-0" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                                @endif
                            </div>
                        </span>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $factures->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('searchFacture')?.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        document.querySelectorAll('#facturesTable tbody tr').forEach(row => {
            let match = row.dataset.numero?.includes(term) || row.dataset.client?.includes(term);
            row.style.display = match ? '' : 'none';
        });
    });
    document.getElementById('statutFilter')?.addEventListener('change', function() {
        let statut = this.value;
        document.querySelectorAll('#facturesTable tbody tr').forEach(row => {
            row.style.display = !statut || row.dataset.statut === statut ? '' : 'none';
        });
    });
    function supprimer(id, numero) { if(confirm(`Supprimer la facture ${numero} ?`)) window.location.href = `/vendeur/factures/${id}/delete`; }
</script>
@endpush
@endsection