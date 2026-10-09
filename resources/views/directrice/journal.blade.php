@extends('layouts.directrice')

@section('title', 'Journal comptable')
@section('header-title', '📋 Journal comptable')
@section('header-subtitle', 'Historique des opérations validées')

@section('directrice-content')
<div class="fade-up">

    <div class="data-card mb-4">
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Date début</label><input type="date" id="date_debut" class="form-control-custom"></div>
                <div class="col-md-3"><label class="form-label">Date fin</label><input type="date" id="date_fin" class="form-control-custom"></div>
                <div class="col-md-3 d-flex align-items-end"><button onclick="filtrer()" class="btn-primary-custom w-100"><i class="fas fa-search me-2"></i>Filtrer</button></div>
                <div class="col-md-3 d-flex align-items-end"><button onclick="exporterPDF()" class="btn-success-custom w-100"><i class="fas fa-file-pdf me-2"></i>Exporter</button></div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Date</th><th>Compte</th><th>Libellé</th><th>Débit (FC)</th><th>Crédit (FC)</th><th>Référence</th></tr></thead>
                <tbody id="journalBody">
                    @foreach($journal as $ligne)
                    <tr>
                        <td>{{ $ligne->date->format('d/m/Y') }}</td>
                        <td><strong class="text-primary">{{ $ligne->compte }}</strong></td>
                        <td>{{ $ligne->libelle }}</td>
                        <td class="text-success">{{ number_format($ligne->debit, 0, ',', ' ') }}</td>
                        <td class="text-danger">{{ number_format($ligne->credit, 0, ',', ' ') }}</td>
                        <td><span class="badge-info">{{ $ligne->reference }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $journal->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
    function filtrer() {
        let url = '{{ route("directrice.journal") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value;
        window.location.href = url;
    }
    function exporterPDF() { window.open('{{ route("directrice.export.journal") }}', '_blank'); }
</script>
@endpush
@endsection