<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CMCF Compta - Admin | @yield('title', 'Dashboard')</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        
        body { background: #f1f5f9; min-height: 100vh; }
        
        /* ========== SIDEBAR ADMIN - DESIGN CARRÉ MODERNE ========== */
        .admin-sidebar {
            position: fixed; left: 0; top: 0; width: 280px; height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            transition: all 0.3s ease; z-index: 1000; overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar-header { padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-logo { font-size: 22px; font-weight: 800; color: white; }
        .sidebar-logo span { color: #8b5cf6; }
        .sidebar-logo small { font-size: 10px; display: block; color: rgba(255,255,255,0.4); margin-top: 5px; }
        
        .sidebar-user { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .user-avatar {
            width: 65px; height: 65px; background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px; font-size: 26px; font-weight: 700; color: white;
            box-shadow: 0 8px 20px rgba(139,92,246,0.3);
        }
        .user-name { font-weight: 600; color: white; margin-bottom: 4px; font-size: 15px; }
        .user-role { font-size: 11px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; }
        
        .sidebar-nav { padding: 20px 15px; }
        .nav-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); padding: 0 15px; margin-bottom: 15px; font-weight: 600; }
        .sidebar-nav .nav-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 18px;
            color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px;
            margin-bottom: 6px; transition: all 0.2s ease; font-size: 14px; font-weight: 500;
        }
        .sidebar-nav .nav-link i { width: 22px; font-size: 16px; }
        .sidebar-nav .nav-link:hover { background: rgba(255,255,255,0.08); color: white; transform: translateX(3px); }
        .sidebar-nav .nav-link.active { background: #8b5cf6; color: white; box-shadow: 0 4px 12px rgba(139,92,246,0.3); }
        
        .submenu { margin-left: 35px; margin-top: 5px; margin-bottom: 5px; }
        
        .sidebar-footer {
            position: absolute; bottom: 0; left: 0; right: 0; padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.2);
        }
        .logout-btn {
            display: flex; align-items: center; gap: 12px; padding: 12px 18px;
            color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px;
            width: 100%; background: none; border: none; cursor: pointer; font-size: 14px; font-weight: 500;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.8); color: white; }
        
        /* ========== MAIN CONTENT ========== */
        .admin-main { margin-left: 280px; padding: 20px; min-height: 100vh; transition: all 0.3s ease; }
        
        /* ========== TOP BAR ========== */
        .top-bar {
            background: white; border-radius: 20px; padding: 16px 25px; margin-bottom: 25px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;
        }
        .page-title h2 { font-size: 18px; font-weight: 700; margin: 0; color: #1e293b; }
        .page-title p { font-size: 12px; margin: 4px 0 0 0; color: #64748b; }
        .date-badge {
            background: #f8fafc; padding: 8px 18px; border-radius: 40px;
            font-size: 13px; font-weight: 500; color: #1e293b; border: 1px solid #e2e8f0;
        }
        .date-badge i { color: #8b5cf6; margin-right: 8px; }
        
        /* ========== CARTES STATISTIQUES ========== */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-card {
            background: white; border-radius: 20px; padding: 20px; position: relative;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .stat-icon {
            width: 50px; height: 50px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 15px;
        }
        .stat-primary .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
        .stat-success .stat-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .stat-purple .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
        .stat-icon i { font-size: 22px; }
        .stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .stat-value { font-size: 26px; font-weight: 800; color: #1e293b; }
        
        /* ========== TABLEAUX ========== */
        .data-card {
            background: white; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden; margin-bottom: 25px;
        }
        .data-header {
            padding: 18px 22px; border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;
        }
        .btn-view-all {
            background: #f1f5f9; padding: 6px 15px; border-radius: 30px;
            color: #8b5cf6; text-decoration: none; font-size: 12px; font-weight: 500; transition: all 0.2s;
        }
        .btn-view-all:hover { background: #8b5cf6; color: white; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            background: #f8fafc; padding: 12px 16px; font-size: 11px; font-weight: 600;
            text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0;
        }
        .data-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        .data-table tbody tr:hover { background: #f8fafc; }
        
        /* ========== BOUTONS ========== */
        .btn-primary-custom {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none;
            padding: 10px 24px; border-radius: 12px; color: white; font-weight: 600; transition: all 0.2s;
        }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(139,92,246,0.3); }
        
        /* ========== BADGES ========== */
        .badge-success { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; }
        .badge-danger { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; }
        .badge-info { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; }
        
        /* ========== ACTIONS RAPIDES ========== */
        .quick-actions {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            border-radius: 20px; padding: 25px;
        }
        .quick-header { color: white; font-size: 16px; font-weight: 600; margin-bottom: 20px; }
        .quick-grid { display: flex; flex-wrap: wrap; gap: 12px; }
        .quick-btn {
            background: rgba(255,255,255,0.1); padding: 10px 22px; border-radius: 40px;
            color: white; text-decoration: none; font-size: 13px; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .quick-btn:hover { background: #8b5cf6; transform: translateY(-2px); }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.mobile-open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .top-bar { flex-direction: column; gap: 15px; text-align: center; }
        }
        @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }
        
        /* ========== SCROLLBAR ========== */
        .admin-sidebar::-webkit-scrollbar { width: 5px; }
        .admin-sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .admin-sidebar::-webkit-scrollbar-thumb { background: #8b5cf6; border-radius: 10px; }
        
        /* ========== ANIMATIONS ========== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeInUp 0.5s ease-out; }
    </style>
    
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR ADMIN -->
    <div class="admin-sidebar" :class="{'mobile-open': sidebarOpen}" x-data="{ sidebarOpen: false }">
        <div class="sidebar-header">
            <div class="sidebar-logo">CMCF<span>Admin</span><small>Administration</small></div>
        </div>
        
        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <div class="user-name">{{ Auth::user()->name ?? 'Administrateur' }}</div>
            <div class="user-role">Super Administrateur</div>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-title"><i class="fas fa-circle text-[6px] mr-2"></i> MENU PRINCIPAL</div>
            <a href="{{ url('/admin/dashboard') }}" class="nav-link {{ request()->path() == 'admin/dashboard' ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Dashboard</a>
            
            <div x-data="{ open: false }">
                <a href="#" @click.prevent="open = !open" class="nav-link"><i class="fas fa-users-cog"></i> Utilisateurs <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i></a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/admin/users') }}" class="nav-link"><i class="fas fa-list"></i> Liste des utilisateurs</a>
                    <a href="{{ url('/admin/users/create') }}" class="nav-link"><i class="fas fa-plus-circle"></i> Ajouter un utilisateur</a>
                </div>
            </div>
            
            <!-- ========== NOUVEAU MENU ÉCRITURES COMPTABLES ========== -->
            <div x-data="{ open: {{ request()->routeIs('admin.ecritures.*') ? 'true' : 'false' }} }">
                <a href="#" @click.prevent="open = !open" class="nav-link {{ request()->routeIs('admin.ecritures.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Écritures comptables 
                    <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                </a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/admin/ecritures') }}" class="nav-link {{ request()->routeIs('admin.ecritures.index') ? 'active' : '' }}">
                        <i class="fas fa-list"></i> Liste des écritures
                    </a>
                </div>
            </div>
            
            <a href="{{ url('/admin/plan-comptable') }}" class="nav-link"><i class="fas fa-table-list"></i> Plan comptable</a>
            
            <div x-data="{ open: false }">
                <a href="#" @click.prevent="open = !open" class="nav-link"><i class="fas fa-history"></i> Logs système <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i></a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/admin/logs') }}" class="nav-link"><i class="fas fa-list"></i> Consulter les logs</a>
                    <a href="{{ url('/admin/logs/statistiques') }}" class="nav-link"><i class="fas fa-chart-bar"></i> Statistiques</a>
                </div>
            </div>
            
            <div x-data="{ open: false }">
                <a href="#" @click.prevent="open = !open" class="nav-link"><i class="fas fa-lock"></i> Permissions <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i></a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/admin/permissions') }}" class="nav-link"><i class="fas fa-list"></i> Rôles</a>
                    <a href="{{ url('/admin/permissions/matrix') }}" class="nav-link"><i class="fas fa-th"></i> Matrice</a>
                </div>
            </div>
            
            <div x-data="{ open: false }">
                <a href="#" @click.prevent="open = !open" class="nav-link"><i class="fas fa-server"></i> Système <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i></a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/admin/systeme') }}" class="nav-link"><i class="fas fa-info-circle"></i> Informations</a>
                    <a href="{{ url('/admin/systeme/backups') }}" class="nav-link"><i class="fas fa-database"></i> Backups</a>
                    <a href="{{ url('/admin/systeme/maintenance') }}" class="nav-link"><i class="fas fa-tools"></i> Maintenance</a>
                </div>
            </div>
            
            <a href="{{ url('/admin/statistiques') }}" class="nav-link"><i class="fas fa-chart-line"></i> Statistiques</a>
        </div>
        
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="admin-main">
        <div class="top-bar">
            <div class="page-title">
                <h2>@yield('header-title', 'Tableau de bord')</h2>
                <p>@yield('header-subtitle', 'Administration du système')</p>
            </div>
            <div class="date-badge"><i class="fas fa-calendar-alt"></i> {{ now()->translatedFormat('l d F Y') }}</div>
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
        
        @yield('admin-content')
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menu mobile toggle
        document.querySelector('.btn-menu-mobile')?.addEventListener('click', function() {
            document.querySelector('.admin-sidebar')?.classList.toggle('mobile-open');
        });
        
        // Fermer le menu mobile en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.admin-sidebar');
            const menuBtn = document.querySelector('.btn-menu-mobile');
            if (window.innerWidth < 768 && sidebar && !sidebar.contains(event.target) && !menuBtn?.contains(event.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>