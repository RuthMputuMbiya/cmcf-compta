@extends('layouts.directrice')

@section('title', 'Statistiques')
@section('header-title', '📈 Statistiques')
@section('header-subtitle', 'Analyse des validations')

@section('directrice-content')
<div class="fade-up">

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Validations par mois</h3></div>
                <div class="p-4"><canvas id="validationsChart" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-bar me-2"></i>Délai moyen de validation</h3></div>
                <div class="p-4 text-center">
                    <h1 class="display-1 fw-bold text-primary">{{ $delai_moyen ?? 0 }}<span class="fs-2">h</span></h1>
                    <p class="text-muted">Temps moyen entre création et validation</p>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-header"><h3><i class="fas fa-chart-simple me-2"></i>Validations par type d'opération</h3></div>
        <div class="p-4"><canvas id="typeChart" height="250"></canvas></div>
    </div>
</div>

@push('scripts')
<script>
    new Chart(document.getElementById('validationsChart'), {
        type: 'bar',
        data: { labels: {!! json_encode($validations_par_mois->pluck('mois') ?? []) !!}, datasets: [{ label: 'Validations', data: {!! json_encode($validations_par_mois->pluck('total') ?? []) !!}, backgroundColor: '#8b5cf6', borderRadius: 8 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
    });
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: { labels: {!! json_encode($operations_par_type->pluck('compte_debit') ?? []) !!}, datasets: [{ data: {!! json_encode($operations_par_type->pluck('nombre') ?? []) !!}, backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'], borderWidth: 0 }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
@endsection