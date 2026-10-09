@extends('layouts.directrice')

@section('title', 'Situation bancaire')
@section('header-title', '🏦 Situation bancaire')
@section('header-subtitle', 'Suivi des comptes bancaires')

@section('directrice-content')
<div class="fade-up">

    <!-- Cartes statistiques -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Solde actuel</div>
            <div class="stat-value">{{ number_format($solde ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend"><i class="fas fa-info-circle me-1"></i> Disponibilités</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-label">Total dépôts</div>
            <div class="stat-value">{{ number_format($depots ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Entrées</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-label">Total retraits</div>
            <div class="stat-value">{{ number_format($retraits ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend down"><i class="fas fa-arrow-down me-1"></i> Sorties</div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-calendar"></i></div>
            <div class="stat-label">Opérations</div>
            <div class="stat-value">{{ $operations->total() }}</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="data-card mb-4">
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Date début</label>
                    <input type="date" id="date_debut" class="form-control-custom" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Date fin</label>
                    <input type="date" id="date_fin" class="form-control-custom" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-filter me-1"></i>Type</label>
                    <select id="type_filter" class="form-control-custom">
                        <option value="">Tous les types</option>
                        <option value="depot">Dépôts</option>
                        <option value="retrait">Retraits</option>
                        <option value="virement">Virements</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button onclick="filtrer()" class="btn-primary-custom flex-1"><i class="fas fa-search me-2"></i>Filtrer</button>
                    <button onclick="exporterPDF()" class="btn-success-custom"><i class="fas fa-file-pdf"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique évolution -->
    <div class="data-card mb-4">
        <div class="data-header">
            <h3><i class="fas fa-chart-line me-2"></i>Évolution du solde bancaire</h3>
            <span class="badge bg-light">6 derniers mois</span>
        </div>
        <div class="p-4"><canvas id="evolutionChart" height="250"></canvas></div>
    </div>

    <!-- Tableau des opérations -->
    <div class="data-card">
        <div class="data-header">
            <h3><i class="fas fa-list-alt me-2"></i>Historique des opérations bancaires</h3>
            <div class="d-flex gap-2">
                <button onclick="exporterExcel()" class="btn-success-custom"><i class="fas fa-file-excel me-2"></i>Excel</button>
                <button onclick="window.print()" class="btn-primary-custom"><i class="fas fa-print me-2"></i>Imprimer</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table" id="operationsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Libellé</th>
                        <th>Montant (FC)</th>
                        <th>Référence</th>
                        <th>Enregistré par</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operations as $op)
                    <tr data-type="{{ $op->type }}">
                        <td>{{ $op->date->format('d/m/Y') }}</span>
                        <td>
                            @if($op->type == 'depot')
                                <span class="badge-success"><i class="fas fa-arrow-down me-1"></i>Dépôt</span>
                            @elseif($op->type == 'retrait')
                                <span class="badge-danger"><i class="fas fa-arrow-up me-1"></i>Retrait</span>
                            @else
                                <span class="badge-info"><i class="fas fa-exchange-alt me-1"></i>Virement</span>
                            @endif
                        </span>
                        <td>{{ $op->libelle }}</span>
                        <td class="{{ $op->type == 'depot' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ number_format(abs($op->montant), 0, ',', ' ') }} FC
                        </span>
                        <td>{{ $op->reference ?? '-' }}</span>
                        <td>{{ $op->enregistrePar->name ?? '-' }}</span>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <td colspan="6" class="py-5">
                            <i class="fas fa-university fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Aucune opération bancaire trouvée</p>
                        </span>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $operations->links() }}
        </div>
    </div>

    <!-- Alertes et informations -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition des opérations</h3></div>
                <div class="p-4"><canvas id="repartitionChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-info-circle me-2"></i>Informations</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3">
                        <label>Dernière opération</label>
                        <h5>{{ $operations->first()?->date->format('d/m/Y') ?? 'Aucune' }}</h5>
                    </div>
                    <div class="info-box">
                        <label>Moyenne des dépôts</label>
                        <h5 class="text-success">{{ number_format($depots_moyen ?? 0, 0, ',', ' ') }} FC</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique évolution
    const evoCtx = document.getElementById('evolutionChart')?.getContext('2d');
    if (evoCtx) {
        new Chart(evoCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($evolution_mois ?? ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun']) !!},
                datasets: [{
                    label: 'Solde bancaire (FC)',
                    data: {!! json_encode($evolution_soldes ?? [0, 0, 0, 0, 0, 0]) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.05)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${new Intl.NumberFormat('fr-FR').format(ctx.raw)} FC` } }
                },
                scales: { y: { beginAtZero: true, ticks: { callback: (v) => new Intl.NumberFormat('fr-FR').format(v) + ' FC' } } }
            }
        });
    }

    // Graphique répartition
    const repCtx = document.getElementById('repartitionChart')?.getContext('2d');
    if (repCtx) {
        new Chart(repCtx, {
            type: 'doughnut',
            data: {
                labels: ['Dépôts', 'Retraits', 'Virements'],
                datasets: [{
                    data: [{{ $depots ?? 0 }}, {{ $retraits ?? 0 }}, 0],
                    backgroundColor: ['#10b981', '#ef4444', '#3b82f6'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } },
                cutout: '60%'
            }
        });
    }

    // Filtres
    function filtrer() {
        let url = '{{ route("directrice.banque") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('type_filter').value) url += 'type=' + document.getElementById('type_filter').value;
        window.location.href = url;
    }

    function exporterPDF() {
        let url = '{{ route("directrice.export.banque") }}?format=pdf';
        if(document.getElementById('date_debut').value) url += '&date_debut=' + document.getElementById('date_debut').value;
        if(document.getElementById('date_fin').value) url += '&date_fin=' + document.getElementById('date_fin').value;
        window.open(url, '_blank');
    }

    function exporterExcel() {
        let url = '{{ route("directrice.export.banque") }}?format=excel';
        if(document.getElementById('date_debut').value) url += '&date_debut=' + document.getElementById('date_debut').value;
        if(document.getElementById('date_fin').value) url += '&date_fin=' + document.getElementById('date_fin').value;
        window.location.href = url;
    }
</script>
@endpush

@push('styles')
<style>
    .flex-1 { flex: 1; }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; }
    .info-box label { font-size: 11px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; display: block; margin-bottom: 5px; }
    .info-box h5 { font-size: 16px; font-weight: 700; margin: 0; color: #1e293b; }
</style>
@endpush
@endsection