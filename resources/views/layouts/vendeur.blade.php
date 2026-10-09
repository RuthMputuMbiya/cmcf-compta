@extends('layouts.app')

@section('title', 'Vendeur - ' . ($title ?? 'Dashboard'))

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Bannière de bienvenue -->
    <div class="welcome-banner mb-4">
        <div class="welcome-content">
            <div class="welcome-text">
                <h2><i class="fas fa-store me-2"></i>@yield('header-title', 'Tableau de bord')</h2>
                <p>@yield('header-subtitle', 'Gestion des ventes et factures')</p>
            </div>
            <div class="welcome-date">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ now()->translatedFormat('l d F Y') }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-4 mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button @click="show = false" class="btn-close float-end"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger rounded-4 mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button @click="show = false" class="btn-close float-end"></button>
    </div>
    @endif

    @yield('vendeur-content')
</div>

@push('styles')
<style>
    .welcome-banner { background: linear-gradient(135deg, #d97706, #f59e0b); border-radius: 20px; padding: 25px 30px; }
    .welcome-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .welcome-text h2 { color: white; font-size: 22px; font-weight: 700; margin: 0; }
    .welcome-text p { color: #fef3c7; font-size: 14px; margin: 5px 0 0; }
    .welcome-date { background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 40px; color: white; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
    .stat-card { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .stat-icon { width: 50px; height: 50px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
    .stat-primary .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .stat-success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-danger .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .stat-info .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
    .stat-purple .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
    .stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
    .stat-value { font-size: 26px; font-weight: 800; color: #1e293b; }
    .stat-trend { font-size: 11px; margin-top: 8px; }
    .stat-trend.up { color: #10b981; }
    .stat-trend.down { color: #ef4444; }
    .data-card { background: white; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 25px; }
    .data-header { padding: 18px 22px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .data-title-section { display: flex; align-items: center; gap: 12px; }
    .data-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .btn-view-all { background: #f1f5f9; padding: 6px 15px; border-radius: 30px; color: #f59e0b; text-decoration: none; font-size: 12px; font-weight: 500; transition: all 0.2s; }
    .btn-view-all:hover { background: #f59e0b; color: white; }
    .btn-primary-custom { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 10px 24px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245,158,11,0.3); }
    .btn-success-custom { background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 10px 24px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; }
    .btn-danger-custom { background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 10px 24px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th { background: #f8fafc; padding: 12px 16px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    .data-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .form-control-custom { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s; }
    .form-control-custom:focus { border-color: #f59e0b; outline: none; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
    .badge-success { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-danger { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-warning { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; }
    .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; }
    .quick-actions { background: linear-gradient(135deg, #78350f, #92400e); border-radius: 20px; padding: 25px; }
    .quick-header { color: white; font-size: 16px; font-weight: 600; margin-bottom: 20px; }
    .quick-grid { display: flex; flex-wrap: wrap; gap: 12px; }
    .quick-btn { background: rgba(255,255,255,0.15); padding: 10px 22px; border-radius: 40px; color: white; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s; }
    .quick-btn:hover { background: #f59e0b; transform: translateY(-2px); }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; }
    .info-box label { font-size: 11px; text-transform: uppercase; color: #64748b; margin-bottom: 5px; display: block; }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .welcome-content { flex-direction: column; text-align: center; } }
    @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush
@endsection