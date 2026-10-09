<footer class="bg-white border-t border-gray-200 py-4 px-6 mt-auto">
    <div class="container mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
            <div class="flex items-center space-x-4">
                <p>&copy; {{ date('Y') }} CMCF-COMPTA. Tous droits réservés.</p>
                <span class="hidden md:inline">|</span>
                <a href="#" class="hover:text-blue-600 transition">Mentions légales</a>
                <a href="#" class="hover:text-blue-600 transition">CGU</a>
                <a href="#" class="hover:text-blue-600 transition">Support</a>
            </div>
            <div class="flex items-center space-x-4 mt-2 md:mt-0">
                <div class="flex items-center space-x-1">
                    <i class="fas fa-circle text-green-500 text-xs"></i>
                    <span>Système opérationnel</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i class="fas fa-database text-blue-500 text-xs"></i>
                    <span>Base de données connectée</span>
                </div>
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition">
                    <i class="fas fa-arrow-up text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>
</footer>