@extends('layouts.caissier')

@section('title', 'Rapport mensuel')
@section('header-title', '📅 Rapport mensuel de caisse')
@section('header-subtitle', 'Récapitulatif des opérations du mois')

@section('caissier-content')
<div class="fade-up">

    <!-- Sélecteur de mois -->
    <div class="data-card mb-4">
        <div class="p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Mois</label>
                    <select id="mois" class="form-control-custom">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ ($mois ?? now()->month) == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Année</label>
                    <select id="annee" class="form-control-custom">
                        @for($i = now()->year; $i >= now()->year-2; $i--)
                        <option value="{{ $i }}" {{ ($annee ?? now()->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button onclick="chargerRapport()" class="btn-primary-custom w-100"><i class="fas fa-search me-2"></i>Voir</button>
                </div>
                <div class="col-md-2">
                    <button onclick="exporterPDF()" class="btn-success-custom w-100"><i class="fas fa-file-pdf me-2"></i>PDF</button>
                </div>
                <div class="col-md-2">
                    <button onclick="exporterExcel()" class="btn-info-custom w-100"><i class="fas fa-file-excel me-2"></i>Excel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête -->
    <div class="rapport-header mb-4">
        <div class="text-center">
            <h2 class="mb-1">CMCF CONSULTING-FORMATION SASU</h2>
            <p class="text-muted">Rapport mensuel de caisse</p>
            <h4 class="text-primary">
                Mois de {{ \Carbon\Carbon::create()->month($mois ?? now()->month)->translatedFormat('F') }} {{ $annee ?? now()->year }}
            </h4>
        </div>
    </div>

    <!-- Cartes récapitulatives -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-label">Total entrées</div>
            <div class="stat-value">{{ number_format($total_entrees_mois ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-label">Total sorties</div>
            <div class="stat-value">{{ number_format($total_sorties_mois ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Solde moyen</div>
            <div class="stat-value">{{ number_format($solde_moyen ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
            <div class="stat-label">Nombre opérations</div>
            <div class="stat-value">{{ $total_operations ?? 0 }}</div>
        </div>
    </div>

    <!-- Évolution mensuelle -->
    <div class="data-card mb-4">
        <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Évolution quotidienne</h3></div>
        <div class="p-4"><canvas id="evolutionChart" height="300"></canvas></div>
    </div>

    <!-- Tableau récapitulatif par jour -->
    <div class="data-card mb-4">
        <div class="data-header"><h3><i class="fas fa-table me-2"></i>Récapitulatif par jour</h3></div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>Jour</th><th>Entrées (FC)</th><th>Sorties (FC)</th><th>Solde (FC)</th>
                </thead>
                <tbody>
                    @foreach($recap_journalier ?? [] as $jour)
                    <tr>
                        <td>{{ $jour['jour'] }}</span>
                        <td class="text-success">{{ number_format($jour['entrees'], 0, ',', ' ') }}</span>
                        <td class="text-danger">{{ number_format($jour['sorties'], 0, ',', ' ') }}</span>
                        <td class="fw-bold">{{ number_format($jour['solde'], 0, ',', ' ') }}</span>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr><td>TOTAUX :</td>
                    <td class="text-success">{{ number_format($total_entrees_mois ?? 0, 0, ',', ' ') }}</td>
                    <td class="text-danger">{{ number_format($total_sorties_mois ?? 0, 0, ',', ' ') }}</td>
                    <td>{{ number_format($solde_final_mois ?? 0, 0, ',', ' ') }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Graphique répartition -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition des entrées</h3></div>
                <div class="p-4"><canvas id="entreesChart" height="250"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition des sorties</h3></div>
                <div class="p-4"><canvas id="sortiesChart" height="250"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Signature -->
    <div class="data-card mt-4">
        <div class="data-header"><h3><i class="fas fa-check-circle me-2"></i>Validation du rapport mensuel</h3></div>
        <div class="p-4 text-center">
            <div class="signature-box">
                <p class="text-muted">Je certifie que les informations ci-dessus sont exactes et conformes aux registres</p>
                <div class="row">
                    <div class="col-6">
                        <div class="signature-line">
                            <p>Fait à Lubumbashi, le {{ now()->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="signature-line">
                            <p>Signature de la caissière</p>
                            <div class="mt-4">_________________</div>
                            <p class="mt-2">{{ auth()->user()->name }} {{ auth()->user()->prenom }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function chargerRapport() {
        let mois = document.getElementById('mois').value;
        let annee = document.getElementById('annee').value;
        window.location.href = '{{ route("caissier.rapports.mensuel") }}?mois=' + mois + '&annee=' + annee;
    }
    function exporterPDF() {
        let mois = document.getElementById('mois').value;
        let annee = document.getElementById('annee').value;
        window.open('{{ route("caissier.rapports.mensuel.pdf") }}?mois=' + mois + '&annee=' + annee, '_blank');
    }
    function exporterExcel() {
        let mois = document.getElementById('mois').value;
        let annee = document.getElementById('annee').value;
        window.location.href = '{{ route("caissier.rapports.mensuel.excel") }}?mois=' + mois + '&annee=' + annee;
    }

    // Graphique évolution
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($jours ?? []) !!},
            datasets: [
                { label: 'Entrées', data: {!! json_encode($entrees_journalieres ?? []) !!}, borderColor: '#10b981', fill: true, tension: 0.4 },
                { label: 'Sorties', data: {!! json_encode($sorties_journalieres ?? []) !!}, borderColor: '#ef4444', fill: true, tension: 0.4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: true, scales: { y: { ticks: { callback: (v) => new Intl.NumberFormat('fr-FR').format(v) + ' FC' } } } }
    });

    // Graphique entrées
    new Chart(document.getElementById('entreesChart'), {
        type: 'doughnut',
        data: { labels: {!! json_encode($entrees_labels ?? []) !!}, datasets: [{ data: {!! json_encode($entrees_values ?? []) !!}, backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'], borderWidth: 0 }] },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
    });

    // Graphique sorties
    new Chart(document.getElementById('sortiesChart'), {
        type: 'doughnut',
        data: { labels: {!! json_encode($sorties_labels ?? []) !!}, datasets: [{ data: {!! json_encode($sorties_values ?? []) !!}, backgroundColor: ['#ef4444', '#dc2626', '#b91c1c', '#991b1b'], borderWidth: 0 }] },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
@endsection