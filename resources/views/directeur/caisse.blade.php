@extends('layouts.directeur')

@section('title', 'Situation de caisse')
@section('header-title', '💰 Situation de caisse')
@section('header-subtitle', 'Suivi des mouvements de caisse')

@section('directeur-content')
<div class="fade-up">

    <div class="stats-grid mb-4">
        <div class="stat-card stat-success"><div class="stat-icon"><i class="fas fa-arrow-down"></i></div><div class="stat-label">Total entrées</div><div class="stat-value">{{ number_format($entrees ?? 0, 0, ',', ' ') }} FC</div></div>
        <div class="stat-card stat-danger"><div class="stat-icon"><i class="fas fa-arrow-up"></i></div><div class="stat-label">Total sorties</div><div class="stat-value">{{ number_format($sorties ?? 0, 0, ',', ' ') }} FC</div></div>
        <div class="stat-card stat-primary"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div class="stat-label">Solde actuel</div><div class="stat-value">{{ number_format($solde ?? 0, 0, ',', ' ') }} FC</div></div>
        <div class="stat-card stat-info"><div class="stat-icon"><i class="fas fa-calendar"></i></div><div class="stat-label">Opérations</div><div class="stat-value">{{ $operations->total() }}</div></div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Date</th><th>Type</th><th>Libellé</th><th>Montant (FC)</th><th>Référence</th></tr></thead>
                <tbody>
                    @foreach($operations as $op)
                    <tr><td>{{ $op->date->format('d/m/Y') }}</td><td><span class="badge-{{ $op->type == 'entree' ? 'success' : 'danger' }}">{{ $op->type == 'entree' ? 'Entrée' : 'Sortie' }}</span></td><td>{{ $op->libelle }}</td><td class="{{ $op->type == 'entree' ? 'text-success' : 'text-danger' }}">{{ number_format($op->montant, 0, ',', ' ') }} FC</td><td>{{ $op->reference ?? '-' }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $operations->links() }}</div>
    </div>
</div>
@endsection