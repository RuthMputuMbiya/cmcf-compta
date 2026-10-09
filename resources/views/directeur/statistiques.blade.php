@extends('layouts.directeur')

@section('title', 'Statistiques')
@section('header-title', '📈 Statistiques avancées')
@section('header-subtitle', 'Analyse des performances')

@section('directeur-content')
<div class="fade-up">

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Évolution mensuelle du CA</h3></div>
                <div class="p-4"><canvas id="caEvolutionChart" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Évolution des charges</h3></div>
                <div class="p-4"><canvas id="chargesEvolutionChart" height="280"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition des charges</h3></div>
                <div class="p-4"><canvas id="chargesRepartitionChart" height="250"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-bar me-2"></i>Top 10 clients</h3></div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>Client</th><th>CA (FC)</th><th>%</th></tr></thead>
                        <tbody>
                            @forelse($top_clients ?? [] as $c)
                            <tr><td>{{ $c->nom }}</td><td class="text-success">{{ number_format($c->total, 0, ',', ' ') }}</td><td>{{ $c->pourcentage }}%</td></tr>
                            @empty<tr><td colspan="3" class="text-center">Aucune donnée</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-chart-simple me-2"></i>Indicateurs clés</h3></div>
        <div class="p-4">
            <div class="row">
                <div class="col-md-3"><div class="info-box"><label>Rentabilité</label><h3 class="text-primary">{{ $ratio_rentabilite ?? 0 }}%</h3></div></div>
                <div class="col-md-3"><div class="info-box"><label>Trésorerie nette</label><h3 class="text-success">{{ number_format($tresorerie ?? 0, 0, ',', ' ') }} FC</h3></div></div>
                <div class="col-md-3"><div class="info-box"><label>Fonds de roulement</label><h3 class="text-info">{{ number_format($fdr ?? 0, 0, ',', ' ') }} FC</h3></div></div>
                <div class="col-md-3"><div class="info-box"><label>Rotation stocks</label><h3 class="text-warning">{{ $rotation_stocks ?? 0 }} jours</h3></div></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    new Chart(document.getElementById('caEvolutionChart'), { type: 'line', data: { labels: {!! json_encode($mois ?? []) !!}, datasets: [{ label: 'CA (FC)', data: {!! json_encode($ca_data ?? []) !!}, borderColor: '#3b82f6', tension: 0.4, fill: true }] }, options: { responsive: true } });
    new Chart(document.getElementById('chargesEvolutionChart'), { type: 'line', data: { labels: {!! json_encode($mois ?? []) !!}, datasets: [{ label: 'Charges (FC)', data: {!! json_encode($charges_data ?? []) !!}, borderColor: '#ef4444', tension: 0.4, fill: true }] }, options: { responsive: true } });
    new Chart(document.getElementById('chargesRepartitionChart'), { type: 'doughnut', data: { labels: {!! json_encode($charges_labels ?? []) !!}, datasets: [{ data: {!! json_encode($charges_values ?? []) !!}, backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b', '#10b981', '#8b5cf6'], borderWidth: 0 }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
</script>
@endpush
@endsection