@extends('layouts.directrice')

@section('title', 'Tableau de bord')
@section('header-title', '📊 Tableau de bord - Direction Adjointe')
@section('header-subtitle', 'Vue d\'ensemble des opérations')

@section('directrice-content')
<div class="fade-up">

    <!-- Cartes KPI -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-label">En attente de validation</div>
            <div class="stat-value">{{ number_format($en_attente_validation ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend warning"><i class="fas fa-clock me-1"></i> À traiter</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-label">Déjà validées</div>
            <div class="stat-value">{{ number_format($total_validees ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> Traitées</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-percent"></i></div>
            <div class="stat-label">Taux de validation</div>
            <div class="stat-value">{{ $taux_validation ?? 0 }}<span class="small">%</span></div>
            <div class="stat-trend up"><i class="fas fa-arrow-up me-1"></i> En progression</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Montant total validé</div>
            <div class="stat-value">{{ number_format($montant_total_valide ?? 0, 0, ',', ' ') }} FC</div>
        </div>
    </div>

    <!-- Deuxième ligne -->
    <div class="stats-grid">
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fas fa-university"></i></div>
            <div class="stat-label">Solde banque</div>
            <div class="stat-value">{{ number_format($total_banque ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-label">Solde caisse</div>
            <div class="stat-value">{{ number_format($solde_caisse ?? 0, 0, ',', ' ') }} FC</div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fas fa-calendar"></i></div>
            <div class="stat-label">Opérations ce mois</div>
            <div class="stat-value">{{ number_format($nb_operations_mois ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Dernier résultat</div>
            <div class="stat-value">{{ number_format($dernier_resultat->resultat_net ?? 0, 0, ',', ' ') }} FC</div>
        </div>
    </div>

    <!-- Graphique évolution validation -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="data-card">
                <div class="data-header">
                    <h3><i class="fas fa-chart-line me-2"></i>Évolution des validations</h3>
                    <span class="badge bg-light">6 derniers mois</span>
                </div>
                <div class="p-4"><canvas id="evolutionChart" height="280"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Répartition</h3></div>
                <div class="p-4"><canvas id="repartitionChart" height="250"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Opérations en attente -->
    <div class="data-card">
        <div class="data-header">
            <div><h3><i class="fas fa-clock me-2"></i>Opérations en attente de validation</h3></div>
            <a href="{{ route('directrice.operations') }}" class="btn-view-all">Voir toutes <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>N° Pièce</th><th>Date</th><th>Libellé</th><th>Montant</th><th>Type</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($operations_attente ?? [] as $op)
                    <tr>
                        <td><strong class="text-primary">{{ $op->numero_piece }}</strong></td>
                        <td>{{ $op->date->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($op->libelle, 40) }}</td>
                        <td class="text-success">{{ number_format($op->montant_debit, 0, ',', ' ') }} FC</span>
                        <td><span class="badge-warning">En attente</span></span>
                        <td>
                            <div class="d-flex gap-2">
                                <button onclick="validerOperation({{ $op->id }})" class="btn-validate"><i class="fas fa-check-circle"></i> Valider</button>
                                <button onclick="rejeterOperation({{ $op->id }})" class="btn-reject"><i class="fas fa-times-circle"></i> Rejeter</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4"><i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>Aucune opération en attente</span></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Derniers bilans -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-pie me-2"></i>Dernier bilan</h3></div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-6"><div class="info-box"><label>Total Actif</label><h4 class="text-primary">{{ number_format($dernier_bilan->total_actif ?? 0, 0, ',', ' ') }} FC</h4></div></div>
                        <div class="col-6"><div class="info-box"><label>Total Passif</label><h4 class="text-success">{{ number_format($dernier_bilan->total_passif ?? 0, 0, ',', ' ') }} FC</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-header"><h3><i class="fas fa-chart-line me-2"></i>Dernier résultat</h3></div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-6"><div class="info-box"><label>Total Produits</label><h4 class="text-success">{{ number_format($dernier_resultat->total_produits ?? 0, 0, ',', ' ') }} FC</h4></div></div>
                        <div class="col-6"><div class="info-box"><label>Total Charges</label><h4 class="text-danger">{{ number_format($dernier_resultat->total_charges ?? 0, 0, ',', ' ') }} FC</h4></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions">
        <div class="quick-header"><i class="fas fa-bolt me-2"></i> Actions rapides</div>
        <div class="quick-grid">
            <a href="{{ route('directrice.operations') }}" class="quick-btn"><i class="fas fa-check-double"></i> Valider des opérations</a>
            <a href="{{ route('directrice.journal') }}" class="quick-btn"><i class="fas fa-book"></i> Journal comptable</a>
            <a href="{{ route('directrice.bilan') }}" class="quick-btn"><i class="fas fa-chart-pie"></i> Bilan</a>
            <a href="{{ route('directrice.resultat') }}" class="quick-btn"><i class="fas fa-chart-line"></i> Résultat</a>
            <a href="{{ route('directrice.statistiques') }}" class="quick-btn"><i class="fas fa-chart-bar"></i> Statistiques</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    new Chart(document.getElementById('evolutionChart'), {
        type: 'line',
        data: { labels: {!! json_encode($evolution_validation['mois'] ?? []) !!}, datasets: [{ label: 'Validations', data: {!! json_encode($evolution_validation['validees'] ?? []) !!}, borderColor: '#8b5cf6', tension: 0.4, fill: true }] },
        options: { responsive: true, maintainAspectRatio: true }
    });
    new Chart(document.getElementById('repartitionChart'), {
        type: 'doughnut',
        data: { labels: ['Validées', 'En attente', 'Rejetées'], datasets: [{ data: [{{ $total_validees ?? 0 }}, {{ $en_attente_validation ?? 0 }}, 0], backgroundColor: ['#10b981', '#f59e0b', '#ef4444'], borderWidth: 0 }] },
        options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
    });
    function validerOperation(id) { if(confirm('Valider cette opération ?')) window.location.href = `/directrice/valider/${id}`; }
    function rejeterOperation(id) { if(confirm('Rejeter cette opération ?')) window.location.href = `/directrice/rejeter/${id}`; }
</script>
@endpush
@endsection