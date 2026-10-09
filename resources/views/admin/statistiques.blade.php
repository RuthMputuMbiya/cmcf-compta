@extends('layouts.admin')

@section('title', 'Statistiques')
@section('header-title', '📊 Statistiques avancées')
@section('header-subtitle', 'Analyse détaillée du système')

@section('admin-content')
<div class="fade-up">

    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary"><div class="stat-icon"><i class="fas fa-calendar-day"></i></div><div class="stat-label">Activité aujourd'hui</div><div class="stat-value">{{ $activite_aujourdhui ?? 0 }}</div></div>
        <div class="stat-card stat-success"><div class="stat-icon"><i class="fas fa-calendar-week"></i></div><div class="stat-label">Cette semaine</div><div class="stat-value">{{ $activite_semaine ?? 0 }}</div></div>
        <div class="stat-card stat-purple"><div class="stat-icon"><i class="fas fa-calendar-month"></i></div><div class="stat-label">Ce mois</div><div class="stat-value">{{ $activite_mois ?? 0 }}</div></div>
        <div class="stat-card stat-warning"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div class="stat-label">Tendance</div><div class="stat-value">{{ $tendance ?? '+12%' }}</div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Activité par jour</h3></div>
                <div class="p-4"><canvas id="dailyChart" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-bar me-2"></i>Top actions</h3></div>
                <div class="p-4"><canvas id="actionsChart" height="280"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-clock me-2"></i>Activité par heure</h3></div>
                <div class="p-4"><canvas id="hourlyChart" height="250"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-users me-2"></i>Top utilisateurs</h3></div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>Utilisateur</th><th>Actions</th><th>Dernière activité</th></tr></thead>
                        <tbody>
                            @forelse($top_utilisateurs ?? [] as $u)
                            <tr><td><strong>{{ $u->user?->name ?? 'Système' }}</strong></td><td>{{ $u->total }}</td><td>{{ $u->last_activity?->format('d/m/Y') ?? '-' }}</td></tr>
                            @empty<td colspan="3" class="text-center py-4">Aucune donnée</td>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: { labels: {!! json_encode($jours ?? []) !!}, datasets: [{ label: 'Activité', data: {!! json_encode($counts ?? []) !!}, borderColor: '#8b5cf6', tension: 0.4, fill: true }] },
        options: { responsive: true, maintainAspectRatio: true }
    });
    new Chart(document.getElementById('actionsChart'), {
        type: 'bar',
        data: { labels: {!! json_encode($action_labels ?? []) !!}, datasets: [{ label: 'Nombre', data: {!! json_encode($action_data ?? []) !!}, backgroundColor: '#8b5cf6', borderRadius: 8 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
    });
    new Chart(document.getElementById('hourlyChart'), {
        type: 'line',
        data: { labels: {!! json_encode($heures ?? []) !!}, datasets: [{ label: 'Activité', data: {!! json_encode($heures_counts ?? []) !!}, borderColor: '#10b981', fill: true }] },
        options: { responsive: true, maintainAspectRatio: true }
    });
</script>
@endpush
@endsection