@php
    $userRole = auth()->user()->role->code ?? 'AD';
    
    $menuItems = [
        'DR' => [
            ['name' => 'Tableau de bord', 'icon' => 'fas fa-tachometer-alt', 'route' => 'directeur.dashboard', 'active' => 'directeur.dashboard*'],
            ['name' => 'Journal', 'icon' => 'fas fa-book', 'route' => 'directeur.journal', 'active' => 'directeur.journal*'],
            ['name' => 'Grand Livre', 'icon' => 'fas fa-list', 'route' => 'directeur.grand-livre', 'active' => 'directeur.grand-livre*'],
            ['name' => 'Balance', 'icon' => 'fas fa-balance-scale', 'route' => 'directeur.balance', 'active' => 'directeur.balance*'],
            ['name' => 'Bilan', 'icon' => 'fas fa-chart-pie', 'route' => 'directeur.bilan', 'active' => 'directeur.bilan*'],
            ['name' => 'Compte Résultat', 'icon' => 'fas fa-chart-line', 'route' => 'directeur.resultat', 'active' => 'directeur.resultat*'],
            ['name' => 'Banque', 'icon' => 'fas fa-university', 'route' => 'directeur.banque', 'active' => 'directeur.banque*'],
            ['name' => 'Caisse', 'icon' => 'fas fa-money-bill-wave', 'route' => 'directeur.caisse', 'active' => 'directeur.caisse*'],
            ['name' => 'Statistiques', 'icon' => 'fas fa-chart-bar', 'route' => 'directeur.statistiques', 'active' => 'directeur.statistiques*'],
        ],
        'DA' => [
            ['name' => 'Tableau de bord', 'icon' => 'fas fa-tachometer-alt', 'route' => 'directrice.dashboard', 'active' => 'directrice.dashboard*'],
            ['name' => 'Validations', 'icon' => 'fas fa-check-double', 'route' => 'directrice.operations', 'active' => 'directrice.operations*'],
            ['name' => 'Journal', 'icon' => 'fas fa-book', 'route' => 'directrice.journal', 'active' => 'directrice.journal*'],
            ['name' => 'Balance', 'icon' => 'fas fa-balance-scale', 'route' => 'directrice.balance', 'active' => 'directrice.balance*'],
            ['name' => 'Bilan', 'icon' => 'fas fa-chart-pie', 'route' => 'directrice.bilan', 'active' => 'directrice.bilan*'],
            ['name' => 'Compte Résultat', 'icon' => 'fas fa-chart-line', 'route' => 'directrice.resultat', 'active' => 'directrice.resultat*'],
            ['name' => 'Statistiques', 'icon' => 'fas fa-chart-bar', 'route' => 'directrice.statistiques', 'active' => 'directrice.statistiques*'],
        ],
        'CO' => [
            ['name' => 'Tableau de bord', 'icon' => 'fas fa-tachometer-alt', 'route' => 'comptable.dashboard', 'active' => 'comptable.dashboard*'],
            ['name' => 'Écritures', 'icon' => 'fas fa-pen', 'route' => 'comptable.ecritures', 'active' => 'comptable.ecritures*'],
            ['name' => 'Journal', 'icon' => 'fas fa-book', 'route' => 'comptable.journal', 'active' => 'comptable.journal*'],
            ['name' => 'Grand Livre', 'icon' => 'fas fa-list', 'route' => 'comptable.grand-livre', 'active' => 'comptable.grand-livre*'],
            ['name' => 'Balance', 'icon' => 'fas fa-balance-scale', 'route' => 'comptable.balance', 'active' => 'comptable.balance*'],
            ['name' => 'Bilan', 'icon' => 'fas fa-chart-pie', 'route' => 'comptable.bilan', 'active' => 'comptable.bilan*'],
            ['name' => 'Compte Résultat', 'icon' => 'fas fa-chart-line', 'route' => 'comptable.resultat', 'active' => 'comptable.resultat*'],
            ['name' => 'Clients', 'icon' => 'fas fa-users', 'route' => 'comptable.clients', 'active' => 'comptable.clients*'],
            ['name' => 'Fournisseurs', 'icon' => 'fas fa-truck', 'route' => 'comptable.fournisseurs', 'active' => 'comptable.fournisseurs*'],
        ],
        'CA' => [
            ['name' => 'Tableau de bord', 'icon' => 'fas fa-tachometer-alt', 'route' => 'caissier.dashboard', 'active' => 'caissier.dashboard*'],
            ['name' => 'Caisse', 'icon' => 'fas fa-money-bill-wave', 'route' => 'caissier.caisse', 'active' => 'caissier.caisse*'],
            ['name' => 'Banque', 'icon' => 'fas fa-university', 'route' => 'caissier.banque', 'active' => 'caissier.banque*'],
            ['name' => 'Rapports', 'icon' => 'fas fa-file-alt', 'route' => 'caissier.rapports', 'active' => 'caissier.rapports*'],
        ],
        'VE' => [
            ['name' => 'Tableau de bord', 'icon' => 'fas fa-tachometer-alt', 'route' => 'vendeur.dashboard', 'active' => 'vendeur.dashboard*'],
            ['name' => 'Factures', 'icon' => 'fas fa-file-invoice', 'route' => 'vendeur.factures', 'active' => 'vendeur.factures*'],
            ['name' => 'Encaissements', 'icon' => 'fas fa-hand-holding-usd', 'route' => 'vendeur.encaissements', 'active' => 'vendeur.encaissements*'],
            ['name' => 'Ventes', 'icon' => 'fas fa-chart-line', 'route' => 'vendeur.ventes', 'active' => 'vendeur.ventes*'],
            ['name' => 'Clients', 'icon' => 'fas fa-users', 'route' => 'vendeur.clients', 'active' => 'vendeur.clients*'],
        ],
        'AD' => [
            ['name' => 'Tableau de bord', 'icon' => 'fas fa-tachometer-alt', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard*'],
            ['name' => 'Utilisateurs', 'icon' => 'fas fa-users-cog', 'route' => 'admin.users', 'active' => 'admin.users*'],
            ['name' => 'Plan Comptable', 'icon' => 'fas fa-table-list', 'route' => 'admin.plan-comptable', 'active' => 'admin.plan-comptable*'],
            ['name' => 'Logs', 'icon' => 'fas fa-history', 'route' => 'admin.logs', 'active' => 'admin.logs*'],
            ['name' => 'Permissions', 'icon' => 'fas fa-lock', 'route' => 'admin.permissions', 'active' => 'admin.permissions*'],
            ['name' => 'Système', 'icon' => 'fas fa-server', 'route' => 'admin.systeme', 'active' => 'admin.systeme*'],
            ['name' => 'Statistiques', 'icon' => 'fas fa-chart-bar', 'route' => 'admin.statistiques', 'active' => 'admin.statistiques*'],
        ],
    ];
    
    $currentMenu = $menuItems[$userRole] ?? $menuItems['AD'];
@endphp

<aside class="w-72 bg-gradient-to-b from-gray-900 to-gray-800 text-white shadow-xl min-h-screen fixed left-0 top-0 z-20 pt-20">
    <div class="px-4 py-6">
        <!-- Menu de navigation -->
        <nav class="space-y-1">
            @foreach($currentMenu as $item)
                <a href="{{ route($item['route']) }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 group
                          {{ request()->routeIs($item['active']) ? 'bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg' : 'hover:bg-gray-700' }}">
                    <i class="{{ $item['icon'] }} w-6 text-lg"></i>
                    <span class="ml-3">{{ $item['name'] }}</span>
                    @if(request()->routeIs($item['active']))
                        <i class="fas fa-chevron-right ml-auto text-sm"></i>
                    @endif
                </a>
            @endforeach
        </nav>
        
        <hr class="my-6 border-gray-700">
        
        <!-- Informations entreprise -->
        <div class="bg-gray-800 rounded-lg p-4">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Entreprise</p>
                    <p class="text-sm font-semibold">CMCF SASU</p>
                </div>
            </div>
            <div class="text-xs text-gray-400 space-y-1">
                <p><i class="fas fa-calendar w-4"></i> Exercice 2025</p>
                <p><i class="fas fa-chart-line w-4"></i> Comptabilité automatisée</p>
            </div>
        </div>
        
        <!-- Version -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">Version 1.0.0 | &copy; 2025</p>
        </div>
    </div>
</aside>

<style>
    aside {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 768px) {
        aside {
            transform: translateX(-100%);
        }
        aside.open {
            transform: translateX(0);
        }
    }
</style>

<script>
    // Mobile menu toggle
    const menuBtn = document.createElement('button');
    menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    menuBtn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg z-50 md:hidden';
    menuBtn.onclick = () => document.querySelector('aside').classList.toggle('open');
    document.body.appendChild(menuBtn);
</script>