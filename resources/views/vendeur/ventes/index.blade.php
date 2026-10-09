@extends('layouts.vendeur')

@section('title', 'Rapport des ventes')
@section('header-title', '📈 Rapport des ventes')
@section('header-subtitle', 'Historique des ventes réalisées')

@section('vendeur-content')
<div class="fade-up">

    <div class="data-card mb-4">
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Date début</label><input type="date" id="date_debut" class="form-control-custom"></div>
                <div class="col-md-3"><label class="form-label">Date fin</label><input type="date" id="date_fin" class="form-control-custom"></div>
                <div class="col-md-2"><button onclick="filtrer()" class="btn-primary-custom w-100"><i class="fas fa-search me-2"></i>Filtrer</button></div>
                <div class="col-md-2"><button onclick="exporterPDF()" class="btn-success-custom w-100"><i class="fas fa-file-pdf me-2"></i>PDF</button></div>
                <div class="col-md-2"><button onclick="exporterExcel()" class="btn-info-custom w-100"><i class="fas fa-file-excel me-2"></i>Excel</button></div>
            </div>
        </div>
    </div>

    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div class="stat-label">Total ventes</div><div class="stat-value">{{ number_format($total_ventes, 0, ',', ' ') }} FC</div></div>
        <div class="stat-card stat-success"><div class="stat-icon"><i class="fas fa-calendar-day"></i></div><div class="stat-label">Ventes aujourd'hui</div><div class="stat-value">{{ number_format($stats['ventes_jour'] ?? 0, 0, ',', ' ') }} FC</div></div>
        <div class="stat-card stat-info"><div class="stat-icon"><i class="fas fa-calendar-week"></i></div><div class="stat-label">Ventes semaine</div><div class="stat-value">{{ number_format($stats['ventes_semaine'] ?? 0, 0, ',', ' ') }} FC</div></div>
        <div class="stat-card stat-warning"><div class="stat-icon"><i class="fas fa-calendar-month"></i></div><div class="stat-label">Ventes mois</div><div class="stat-value">{{ number_format($stats['ventes_mois'] ?? 0, 0, ',', ' ') }} FC</div></div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>N° Facture</th><th>Date</th><th>Client</th><th>Montant HT</th><th>TVA</th><th>Montant TTC</th><th>Mode paiement</th></tr></thead>
                <tbody>
                    @foreach($ventes as $vente)
                    <tr>
                        <td>{{ $vente->numero }}</span>
                        <td>{{ $vente->date->format('d/m/Y') }}</span>
                        <td>{{ $vente->client->nom }}</span>
                        <td>{{ number_format($vente->montant_ht, 0, ',', ' ') }} FC</span>
                        <td>{{ number_format($vente->tva, 0, ',', ' ') }} FC</span>
                        <td class="fw-bold">{{ number_format($vente->montant_ttc, 0, ',', ' ') }} FC</span>
                        <td>{{ $vente->mode_paiement ?? '-' }}</span>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $ventes->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
    function filtrer() { let url = '{{ route("vendeur.ventes") }}?'; if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&'; if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value; window.location.href = url; }
    function exporterPDF() { let url = '{{ route("vendeur.export.ventes") }}?format=pdf'; if(document.getElementById('date_debut').value) url += '&date_debut=' + document.getElementById('date_debut').value; if(document.getElementById('date_fin').value) url += '&date_fin=' + document.getElementById('date_fin').value; window.open(url, '_blank'); }
    function exporterExcel() { let url = '{{ route("vendeur.export.ventes") }}?format=excel'; if(document.getElementById('date_debut').value) url += '&date_debut=' + document.getElementById('date_debut').value; if(document.getElementById('date_fin').value) url += '&date_fin=' + document.getElementById('date_fin').value; window.location.href = url; }
</script>
@endpush

@push('styles')
<style>
    .btn-info-custom { background: linear-gradient(135deg, #06b6d4, #0891b2); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; }
</style>
@endpush
@endsection