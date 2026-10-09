@extends('layouts.directrice')

@section('title', 'Balance de vérification')
@section('header-title', '⚖️ Balance de vérification')
@section('header-subtitle', 'Équilibre des comptes')

@section('directrice-content')
<div class="fade-up">

    @if($equilibre)
    <div class="alert alert-success rounded-4 mb-4"><i class="fas fa-check-circle me-2"></i>✅ La balance est équilibrée</div>
    @else
    <div class="alert alert-danger rounded-4 mb-4"><i class="fas fa-exclamation-triangle me-2"></i>⚠️ La balance n'est pas équilibrée</div>
    @endif

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Compte</th><th>Libellé</th><th>Solde débiteur (FC)</th><th>Solde créditeur (FC)</th></tr></thead>
                <tbody>
                    @foreach($balance as $ligne)
                    <tr>
                        <td><strong class="text-primary">{{ $ligne->compte }}</strong></td>
                        <td>{{ $ligne->nom_compte ?? '-' }}</td>
                        <td class="text-success">{{ number_format($ligne->solde_debiteur, 0, ',', ' ') }}</td>
                        <td class="text-danger">{{ number_format($ligne->solde_crediteur, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light fw-bold"><tr><td colspan="2" class="text-end">TOTAUX :</td><td class="text-success">{{ number_format($total_debiteur, 0, ',', ' ') }} FC</span><td class="text-danger">{{ number_format($total_crediteur, 0, ',', ' ') }} FC</span></tr></tfoot>
            </table>
        </div>
    </div>
</div>
@endsection