@extends('layouts.vendeur')

@section('title', 'Historique des encaissements')
@section('header-title', '📜 Historique des encaissements')
@section('header-subtitle', 'Suivi des paiements clients')

@section('vendeur-content')
<div class="fade-up">

    <!-- Filtres -->
    <div class="data-card mb-4">
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date début</label>
                    <input type="date" id="date_debut" class="form-control-custom">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date fin</label>
                    <input type="date" id="date_fin" class="form-control-custom">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Mode de paiement</label>
                    <select id="mode_filter" class="form-control-custom">
                        <option value="">Tous</option>
                        <option value="espece">Espèces</option>
                        <option value="banque">Virement bancaire</option>
                        <option value="cheque">Chèque</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button onclick="filtrer()" class="btn-primary-custom flex-1"><i class="fas fa-search me-2"></i>Filtrer</button>
                    <button onclick="exporterPDF()" class="btn-success-custom"><i class="fas fa-file-pdf"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Total encaissé</div>
            <div class="stat-value">{{ number_format($total_encaisse, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-label">Nombre d'opérations</div>
            <div class="stat-value">{{ $encaissements->total() }}</div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-label">Moyenne par opération</div>
            <div class="stat-value">{{ number_format($moyenne_par_operation, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
            <div class="stat-label">Mode principal</div>
            <div class="stat-value">{{ $mode_principal ?? 'Espèces' }}</div>
        </div>
    </div>

    <!-- Tableau des encaissements -->
    <div class="data-card">
        <div class="data-header">
            <h3><i class="fas fa-list-alt me-2"></i>Liste des encaissements</h3>
            <button onclick="window.print()" class="btn-secondary-custom"><i class="fas fa-print me-2"></i>Imprimer</button>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Facture N°</th>
                        <th>Client</th>
                        <th>Montant (FC)</th>
                        <th>Mode</th>
                        <th>Référence</th>
                        <th>Enregistré par</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($encaissements as $enc)
                    <tr>
                        <td>{{ $enc->date->format('d/m/Y') }}</span>
                        <td><strong class="text-primary">{{ $enc->reference ?? '-' }}</strong></span>
                        <td>{{ $enc->libelle ?? '-' }}</span>
                        <td class="text-success fw-bold">{{ number_format($enc->montant, 0, ',', ' ') }}</span>
                        <td>
                            @if($enc->mode_paiement == 'espece')<span class="badge-success">Espèces</span>
                            @elseif($enc->mode_paiement == 'banque')<span class="badge-info">Virement</span>
                            @else<span class="badge-warning">Chèque</span>@endif
                        </span>
                        <td>{{ $enc->reference_paiement ?? '-' }}</span>
                        <td>{{ $enc->enregistrePar->name ?? '-' }}</span>
                    </tr>
                    @empty
                    <tr class="text-center"><td colspan="7" class="py-5"><i class="fas fa-hand-holding-usd fa-3x text-muted mb-3 d-block"></i>Aucun encaissement trouvé</span></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr><td colspan="3" class="text-end">TOTAL :</span><td class="text-success">{{ number_format($total_encaisse, 0, ',', ' ') }} FC</span><td colspan="3"></span></tr>
                </tfoot>
            </table>
        </div>
        <div class="p-3 border-top">{{ $encaissements->links() }}</div>
    </div>

    <!-- Graphique évolution -->
    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Évolution des encaissements</h3></div>
        <div class="p-4"><canvas id="evolutionChart" height="250"></canvas></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function filtrer() {
        let url = '{{ route("vendeur.encaissements.historique") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('mode_filter').value) url += 'mode=' + document.getElementById('mode_filter').value;
        window.location.href = url;
    }
    function exporterPDF() {
        let url = '{{ route("vendeur.export.encaissements") }}?format=pdf';
        if(document.getElementById('date_debut').value) url += '&date_debut=' + document.getElementById('date_debut').value;
        if(document.getElementById('date_fin').value) url += '&date_fin=' + document.getElementById('date_fin').value;
        window.open(url, '_blank');
    }
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($evolution_mois ?? []) !!},
            datasets: [{ label: 'Encaissements (FC)', data: {!! json_encode($evolution_montants ?? []) !!}, borderColor: '#10b981', tension: 0.4, fill: true, backgroundColor: 'rgba(16,185,129,0.05)' }]
        },
        options: { responsive: true, maintainAspectRatio: true, scales: { y: { ticks: { callback: (v) => new Intl.NumberFormat('fr-FR').format(v) + ' FC' } } } }
    });
</script>
@endpush

@push('styles')
<style>
    .flex-1 { flex: 1; }
    .btn-secondary-custom { background: linear-gradient(135deg, #64748b, #475569); border: none; padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; }
    .btn-secondary-custom:hover { transform: translateY(-2px); }
</style>
@endpush
@endsection