@extends('layouts.app')

@section('title', 'Directrice Adjointe - ' . ($title ?? 'Dashboard'))

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Bannière de bienvenue -->
    <div class="welcome-banner mb-4">
        <div class="welcome-content">
            <div class="welcome-text">
                <h2><i class="fas fa-user-tie me-2"></i>@yield('header-title', 'Tableau de bord')</h2>
                <p>@yield('header-subtitle', 'Direction Générale Adjointe')</p>
            </div>
            <div class="welcome-date">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ now()->translatedFormat('l d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Messages flash -->
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

    @yield('directrice-content')
</div>

@push('styles')
<style>
    .welcome-banner { background: linear-gradient(135deg, #5b21b6, #7c3aed); border-radius: 20px; padding: 25px 30px; }
    .welcome-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .welcome-text h2 { color: white; font-size: 22px; font-weight: 700; margin: 0; }
    .welcome-text p { color: #c4b5fd; font-size: 14px; margin: 5px 0 0; }
    .welcome-date { background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 40px; color: white; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
    .stat-card { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .stat-icon { width: 50px; height: 50px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
    .stat-primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
    .stat-success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .stat-purple .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
    .stat-info .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
    .stat-danger .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
    .stat-value { font-size: 26px; font-weight: 800; color: #1e293b; }
    .stat-trend { font-size: 11px; margin-top: 8px; }
    .stat-trend.up { color: #10b981; }
    .stat-trend.warning { color: #f59e0b; }
    .data-card { background: white; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 25px; }
    .data-header { padding: 18px 22px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .data-title-section { display: flex; align-items: center; gap: 12px; }
    .data-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
    .btn-view-all { background: #f1f5f9; padding: 6px 15px; border-radius: 30px; color: #8b5cf6; text-decoration: none; font-size: 12px; font-weight: 500; transition: all 0.2s; }
    .btn-view-all:hover { background: #8b5cf6; color: white; }
    .btn-primary-custom { background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none; padding: 10px 24px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(139,92,246,0.3); }
    .btn-success-custom { background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 8px 20px; border-radius: 10px; color: white; font-weight: 600; transition: all 0.2s; }
    .btn-danger-custom { background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 8px 20px; border-radius: 10px; color: white; font-weight: 600; transition: all 0.2s; }
    .btn-warning-custom { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 8px 20px; border-radius: 10px; color: white; font-weight: 600; transition: all 0.2s; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th { background: #f8fafc; padding: 12px 16px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    .data-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .form-control-custom { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s; }
    .form-control-custom:focus { border-color: #8b5cf6; outline: none; box-shadow: 0 0 0 3px rgba(139,92,246,0.1); }
    .badge-success { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-warning { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-danger { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .quick-actions { background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 20px; padding: 25px; }
    .quick-header { color: white; font-size: 16px; font-weight: 600; margin-bottom: 20px; }
    .quick-grid { display: flex; flex-wrap: wrap; gap: 12px; }
    .quick-btn { background: rgba(255,255,255,0.1); padding: 10px 22px; border-radius: 40px; color: white; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s; }
    .quick-btn:hover { background: #8b5cf6; transform: translateY(-2px); }
    .validation-item { display: flex; align-items: center; gap: 15px; padding: 16px 22px; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
    .validation-item:hover { background: #f8fafc; }
    .validation-avatar { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .validation-info { flex: 1; }
    .validation-name { font-weight: 700; font-size: 15px; color: #1e293b; margin-bottom: 6px; }
    .validation-details { display: flex; flex-wrap: wrap; gap: 15px; font-size: 12px; color: #64748b; }
    .btn-validate { background: #10b981; color: white; padding: 8px 18px; border-radius: 10px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-validate:hover { background: #059669; transform: translateY(-2px); }
    .btn-reject { background: #ef4444; color: white; padding: 8px 18px; border-radius: 10px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-reject:hover { background: #dc2626; transform: translateY(-2px); }
    .empty-state { text-align: center; padding: 50px; }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .welcome-content { flex-direction: column; text-align: center; } .validation-item { flex-direction: column; text-align: center; } .validation-actions { width: 100%; justify-content: center; } }
    @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush
@endsection