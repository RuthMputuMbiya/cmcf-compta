<header class="bg-white shadow-md sticky top-0 z-30">
    <div class="flex justify-between items-center px-6 py-3">
        <!-- Logo et titre -->
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                    CMCF-COMPTA
                </h1>
                <p class="text-xs text-gray-500">Logiciel Comptable Professionnel</p>
            </div>
        </div>
        
        <!-- Recherche -->
        <div class="hidden md:block flex-1 max-w-md mx-6">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="global-search" 
                       placeholder="Rechercher (factures, clients, écritures...)" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
            </div>
        </div>
        
        <!-- Menu utilisateur -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button id="notif-btn" class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <!-- Dropdown notifications -->
                <div id="notif-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl hidden z-50">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div class="p-3 hover:bg-gray-50 border-b cursor-pointer">
                            <p class="text-sm text-gray-800">Nouvelle facture créée</p>
                            <p class="text-xs text-gray-500">Il y a 5 minutes</p>
                        </div>
                        <div class="p-3 hover:bg-gray-50 border-b cursor-pointer">
                            <p class="text-sm text-gray-800">Écriture comptable en attente</p>
                            <p class="text-xs text-gray-500">Il y a 1 heure</p>
                        </div>
                    </div>
                    <div class="p-3 text-center border-t">
                        <a href="#" class="text-sm text-blue-600 hover:underline">Voir toutes</a>
                    </div>
                </div>
            </div>
            
            <!-- Profil -->
            <div class="relative">
                <button id="profile-btn" class="flex items-center space-x-3 focus:outline-none">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}{{ substr(auth()->user()->prenom ?? 'D', 0, 1) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name ?? 'Admin' }} {{ auth()->user()->prenom ?? '' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->role->nom ?? 'Administrateur' }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                </button>
                
                <!-- Dropdown profil -->
                <div id="profile-dropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl hidden z-50">
                    <div class="p-4 border-b bg-gray-50 rounded-t-xl">
                        <p class="font-semibold text-gray-800">{{ auth()->user()->name ?? 'Admin' }} {{ auth()->user()->prenom ?? '' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@cmcf.com' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Matricule: {{ auth()->user()->matricule ?? 'AD001' }}</p>
                    </div>
                    <div class="py-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-user w-5 text-gray-400"></i>
                            <span class="ml-3">Mon profil</span>
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-cog w-5 text-gray-400"></i>
                            <span class="ml-3">Paramètres</span>
                        </a>
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span class="ml-3">Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
    // Toggle notifications dropdown
    const notifBtn = document.getElementById('notif-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown');
    
    if (notifBtn) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
            profileDropdown?.classList.add('hidden');
        });
    }
    
    if (profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
            notifDropdown?.classList.add('hidden');
        });
    }
    
    // Fermer les dropdowns en cliquant ailleurs
    document.addEventListener('click', () => {
        notifDropdown?.classList.add('hidden');
        profileDropdown?.classList.add('hidden');
    });
</script>
@endpush