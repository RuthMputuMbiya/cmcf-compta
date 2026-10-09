<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CMCF Compta - Caissière | @yield('title', 'Dashboard')</title>
    
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
        
        /* ========== SIDEBAR CAISSIER ========== */
        .caissier-sidebar {
            position: fixed; left: 0; top: 0; width: 280px; height: 100vh;
            background: linear-gradient(180deg, #064e3b 0%, #065f46 100%);
            transition: all 0.3s ease; z-index: 1000; overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar-header { padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-logo { font-size: 22px; font-weight: 800; color: white; }
        .sidebar-logo span { color: #10b981; }
        .sidebar-logo small { font-size: 10px; display: block; color: rgba(255,255,255,0.4); margin-top: 5px; }
        
        .sidebar-user { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .user-avatar {
            width: 65px; height: 65px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
            font-size: 26px; font-weight: 700; color: white;
            box-shadow: 0 8px 20px rgba(16,185,129,0.3);
        }
        .user-name { font-weight: 600; color: white; margin-bottom: 4px; font-size: 15px; }
        .user-role { font-size: 11px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; }
        
        .sidebar-nav { padding: 20px 15px; }
        .nav-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); padding: 0 15px; margin-bottom: 15px; font-weight: 600; }
        .sidebar-nav .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 18px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }
        .sidebar-nav .nav-link i { width: 22px; font-size: 16px; }
        .sidebar-nav .nav-link:hover { background: rgba(255,255,255,0.08); color: white; transform: translateX(3px); }
        .sidebar-nav .nav-link.active { background: #10b981; color: white; box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
        
        .submenu { margin-left: 35px; margin-top: 5px; margin-bottom: 5px; }
        .sidebar-footer {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.2);
        }
        .logout-btn {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 18px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 12px;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.8); color: white; }
        
        /* ========== MAIN CONTENT ========== */
        .caissier-main { margin-left: 280px; padding: 20px; min-height: 100vh; transition: all 0.3s ease; }
        
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
        .date-badge i { color: #10b981; margin-right: 8px; }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .caissier-sidebar { transform: translateX(-100%); }
            .caissier-sidebar.mobile-open { transform: translateX(0); }
            .caissier-main { margin-left: 0; }
            .top-bar { flex-direction: column; gap: 15px; text-align: center; }
        }
        
        /* ========== SCROLLBAR ========== */
        .caissier-sidebar::-webkit-scrollbar { width: 5px; }
        .caissier-sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .caissier-sidebar::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
        
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

    <!-- SIDEBAR CAISSIER -->
    <div class="caissier-sidebar" :class="{'mobile-open': sidebarOpen}" x-data="{ sidebarOpen: false }">
        <div class="sidebar-header">
            <div class="sidebar-logo">CMCF<span>Compta</span><small>Gestion caisse</small></div>
        </div>
        
        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'C', 0, 1)) }}</div>
            <div class="user-name">{{ Auth::user()->name ?? 'Caissière' }}</div>
            <div class="user-role">{{ Auth::user()->role->nom ?? 'Caissière' }}</div>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-title"><i class="fas fa-circle text-[6px] mr-2"></i> MENU PRINCIPAL</div>
            <a href="{{ url('/caissier/dashboard') }}" class="nav-link"><i class="fas fa-chart-pie"></i> Dashboard</a>
            
            <div x-data="{ open: false }">
                <a href="#" @click.prevent="open = !open" class="nav-link"><i class="fas fa-money-bill-wave"></i> Caisse <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i></a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/caissier/caisse') }}" class="nav-link"><i class="fas fa-list"></i> Mouvements</a>
                    <a href="{{ url('/caissier/caisse/entree') }}" class="nav-link"><i class="fas fa-plus-circle"></i> Encaissement</a>
                    <a href="{{ url('/caissier/caisse/sortie') }}" class="nav-link"><i class="fas fa-minus-circle"></i> Décaissement</a>
                </div>
            </div>
            
            <div x-data="{ open: false }">
                <a href="#" @click.prevent="open = !open" class="nav-link"><i class="fas fa-university"></i> Banque <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i></a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/caissier/banque') }}" class="nav-link"><i class="fas fa-list"></i> Opérations</a>
                    <a href="{{ url('/caissier/banque/depot') }}" class="nav-link"><i class="fas fa-plus-circle"></i> Dépôt</a>
                    <a href="{{ url('/caissier/banque/retrait') }}" class="nav-link"><i class="fas fa-minus-circle"></i> Retrait</a>
                </div>
            </div>
            
            <div x-data="{ open: false }">
                <a href="#" @click.prevent="open = !open" class="nav-link"><i class="fas fa-file-alt"></i> Rapports <i class="fas fa-chevron-down ms-auto text-xs" :style="open ? 'transform: rotate(180deg)' : ''"></i></a>
                <div x-show="open" x-collapse class="submenu">
                    <a href="{{ url('/caissier/rapports/journalier') }}" class="nav-link"><i class="fas fa-calendar-day"></i> Journalier</a>
                    <a href="{{ url('/caissier/rapports/mensuel') }}" class="nav-link"><i class="fas fa-calendar-month"></i> Mensuel</a>
                </div>
            </div>
            
            <a href="{{ url('/caissier/statistiques') }}" class="nav-link"><i class="fas fa-chart-bar"></i> Statistiques</a>
        </div>
        
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="caissier-main">
        <div class="top-bar">
            <div class="page-title">
                <h2>@yield('header-title', 'Tableau de bord')</h2>
                <p>@yield('header-subtitle', 'Gestion de caisse')</p>
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
        
        @yield('caissier-content')
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('.btn-menu-mobile')?.addEventListener('click', function() {
            document.querySelector('.caissier-sidebar')?.classList.toggle('mobile-open');
        });
    </script>
    @stack('scripts')
</body>
</html>