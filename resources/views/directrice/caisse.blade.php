@extends('layouts.directrice')

@section('title', 'Situation de caisse')
@section('header-title', '💰 Situation de caisse')
@section('header-subtitle', 'Suivi des mouvements de caisse')

@section('directrice-content')
<div class="fade-up">

    <!-- Cartes statistiques -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-label">Total entrées</div>
            <div class="stat-value">{{ number_format($entrees ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Encaissements</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-label">Total sorties</div>
            <div class="stat-value">{{ number_format($sorties ?? 0, 0, ',', ' ') }} FC</div>
            <div class="stat-trend down"><i class="fas fa-arrow-down me-1"></i> Décaissements</div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Solde actuel</div>
            <div class="stat-value {{ $solde >= 0 ? 'text-primary' : 'text-danger' }}">
                {{ number_format(abs($solde), 0, ',', ' ') }} FC
            </div>
            <div class="stat-trend">{{ $solde >= 0 ? 'Créditeur' : 'Débiteur' }}</div>
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
                        <option value="entree">Entrées</option>
                        <option value="sortie">Sorties</option>
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
            <h3><i class="fas fa-chart-line me-2"></i>Évolution de la caisse</h3>
            <span class="badge bg-light">6 derniers mois</span>
        </div>
        <div class="p-4"><canvas id="evolutionChart" height="250"></canvas></div>
    </div>

    <!-- Tableau des opérations -->
    <div class="data-card">
        <div class="data-header">
            <h3><i class="fas fa-list-alt me-2"></i>Historique des mouvements de caisse</h3>
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
                            @if($op->type == 'entree')
                                <span class="badge-success"><i class="fas fa-arrow-down me-1"></i>Entrée</span>
                            @else
                                <span class="badge-danger"><i class="fas fa-arrow-up me-1"></i>Sortie</span>
                            @endif
                        </span>
                        <td>{{ $op->libelle }}</span>
                        <td class="{{ $op->type == 'entree' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ number_format($op->montant, 0, ',', ' ') }} FC
                        </span>
                        <td>{{ $op->reference ?? '-' }}</span>
                        <td>{{ $op->enregistrePar->name ?? '-' }}</span>
                    </tr>
                    @empty
                    <tr class="text-center">
                        <td colspan="6" class="py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Aucun mouvement de caisse trouvé</p>
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

    <!-- Graphique répartition et informations -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition des mouvements</h3></div>
                <div class="p-4"><canvas id="repartitionChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-info-circle me-2"></i>Informations</h3></div>
                <div class="p-4">
                    <div class="info-box mb-3">
                        <label>Dernier mouvement</label>
                        <h5>{{ $operations->first()?->date->format('d/m/Y') ?? 'Aucun' }}</h5>
                    </div>
                    <div class="info-box">
                        <label>Entrée moyenne</label>
                        <h5 class="text-success">{{ number_format($entree_moyenne ?? 0, 0, ',', ' ') }} FC</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    @if($solde < 100000)
    <div class="alert alert-warning rounded-4 mt-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Attention :</strong> Le solde de caisse est faible (moins de 100 000 FC). Veuillez prévoir un réapprovisionnement.
    </div>
    @endif
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
                datasets: [
                    {
                        label: 'Entrées (FC)',
                        data: {!! json_encode($evolution_entrees ?? [0, 0, 0, 0, 0, 0]) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Sorties (FC)',
                        data: {!! json_encode($evolution_sorties ?? [0, 0, 0, 0, 0, 0]) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }
                ]
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
                labels: ['Entrées', 'Sorties'],
                datasets: [{
                    data: [{{ $entrees ?? 0 }}, {{ $sorties ?? 0 }}],
                    backgroundColor: ['#10b981', '#ef4444'],
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
        let url = '{{ route("directrice.caisse") }}?';
        if(document.getElementById('date_debut').value) url += 'date_debut=' + document.getElementById('date_debut').value + '&';
        if(document.getElementById('date_fin').value) url += 'date_fin=' + document.getElementById('date_fin').value + '&';
        if(document.getElementById('type_filter').value) url += 'type=' + document.getElementById('type_filter').value;
        window.location.href = url;
    }

    function exporterPDF() {
        let url = '{{ route("directrice.export.caisse") }}?format=pdf';
        if(document.getElementById('date_debut').value) url += '&date_debut=' + document.getElementById('date_debut').value;
        if(document.getElementById('date_fin').value) url += '&date_fin=' + document.getElementById('date_fin').value;
        window.open(url, '_blank');
    }

    function exporterExcel() {
        let url = '{{ route("directrice.export.caisse") }}?format=excel';
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
    .alert-warning { background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; }
</style>
@endpush
@endsection