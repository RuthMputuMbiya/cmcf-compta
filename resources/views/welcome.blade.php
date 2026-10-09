<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CMCF Pro - Solution comptable professionnelle pour les TPME en RDC. Gérez votre comptabilité, bilans et trésorerie en temps réel.">
    <title>CMCF Pro | Solution comptable professionnelle</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        /* Animations personnalisées */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .animate-fade-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .animate-slide-right {
            animation: slideInRight 0.8s ease-out forwards;
        }
        
        .animate-slide-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }
        
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        
        .animate-pulse-slow {
            animation: pulse 3s infinite;
        }
        
        .hover-lift {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 30px -12px rgba(0, 0, 0, 0.15);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 50%, #8b5cf6 100%);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid #0ea5e9;
            color: #0ea5e9;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.3);
        }
        
        .hero-bg {
            background: linear-gradient(135deg, #f0f9ff 0%, #eef2ff 50%, #f5f3ff 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: radial-gradient(circle, rgba(14,165,233,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .hero-bg::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 60%;
            height: 100%;
            background: radial-gradient(circle, rgba(99,102,241,0.05) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .stat-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(14, 165, 233, 0.1);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            border-color: #0ea5e9;
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 3.5rem;
            height: 3.5rem;
            background: linear-gradient(135deg, #e0f2fe 0%, #eef2ff 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            transition: all 0.3s;
        }
        
        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
        }
        
        .feature-card:hover .feature-icon i {
            color: white !important;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.75rem;
            border: 1px solid rgba(14, 165, 233, 0.1);
            transition: all 0.3s;
        }
        
        .testimonial-card:hover {
            border-color: #0ea5e9;
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        }
        
        .footer-link {
            transition: all 0.2s;
        }
        
        .footer-link:hover {
            color: #0ea5e9;
            transform: translateX(5px);
            display: inline-block;
        }
        
        .mobile-menu-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
            .desktop-menu {
                display: none;
            }
            .mobile-menu.active {
                display: block !important;
            }
        }
        
        .alert {
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
        }
        
        .alert-success {
            background: rgba(212, 237, 218, 0.95);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background: rgba(248, 215, 218, 0.95);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #0ea5e9, #6366f1);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0284c7, #4f46e5);
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Loading spinner */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(14, 165, 233, 0.2);
            border-top-color: #0ea5e9;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
    </style>
</head>
<body class="bg-white">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-md z-50 border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2 group cursor-pointer">
                    <div class="w-9 h-9 gradient-bg rounded-xl flex items-center justify-center shadow-lg transition-transform group-hover:scale-105">
                        <span class="text-white font-bold text-lg">CM</span>
                    </div>
                    <div>
                        <span class="font-bold text-xl text-gray-800">CMCF<span class="gradient-text">.Pro</span></span>
                        <p class="text-xs text-gray-500 hidden sm:block">Solution comptable</p>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#accueil" class="text-gray-600 hover:text-sky-500 transition font-medium">Accueil</a>
                    <a href="#fonctionnalites" class="text-gray-600 hover:text-sky-500 transition font-medium">Fonctionnalités</a>
                    <a href="#chiffres" class="text-gray-600 hover:text-sky-500 transition font-medium">Chiffres clés</a>
                    <a href="#processus" class="text-gray-600 hover:text-sky-500 transition font-medium">Processus</a>
                    <a href="#temoignages" class="text-gray-600 hover:text-sky-500 transition font-medium">Témoignages</a>
                </div>
                
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 gradient-bg text-white rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Tableau de bord</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-gray-600 hover:text-sky-500 transition font-medium flex items-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Connexion</span>
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 gradient-bg text-white rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            <span>Inscription</span>
                        </a>
                    @endauth
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn text-gray-600 focus:outline-none" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="mobile-menu hidden bg-white border-t border-gray-100 md:hidden shadow-lg">
            <div class="px-4 py-3 space-y-2">
                <a href="#accueil" class="block py-2 text-gray-600 hover:text-sky-500 transition" onclick="closeMobileMenu()">Accueil</a>
                <a href="#fonctionnalites" class="block py-2 text-gray-600 hover:text-sky-500 transition" onclick="closeMobileMenu()">Fonctionnalités</a>
                <a href="#chiffres" class="block py-2 text-gray-600 hover:text-sky-500 transition" onclick="closeMobileMenu()">Chiffres clés</a>
                <a href="#processus" class="block py-2 text-gray-600 hover:text-sky-500 transition" onclick="closeMobileMenu()">Processus</a>
                <a href="#temoignages" class="block py-2 text-gray-600 hover:text-sky-500 transition" onclick="closeMobileMenu()">Témoignages</a>
                <div class="pt-3 border-t border-gray-100">
                    @guest
                        <a href="{{ route('login') }}" class="block py-2 text-sky-600 font-medium">Connexion</a>
                        <a href="{{ route('register') }}" class="block py-2 gradient-bg text-white text-center rounded-full px-4 mt-2">Inscription</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Messages Flash -->
    @if(session('success'))
    <div class="fixed top-20 right-4 z-50 animate-slide-right" id="flashMessage">
        <div class="alert alert-success rounded-xl shadow-xl flex items-center gap-3">
            <i class="fas fa-check-circle text-xl"></i>
            <div>
                <p class="font-semibold">Succès !</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="fixed top-20 right-4 z-50 animate-slide-right" id="flashMessage">
        <div class="alert alert-danger rounded-xl shadow-xl flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-xl"></i>
            <div>
                <p class="font-semibold">Erreur !</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <section id="accueil" class="hero-bg pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-up">
                    <div class="inline-flex items-center px-4 py-2 bg-sky-50 rounded-full mb-6 shadow-sm">
                        <span class="w-2.5 h-2.5 bg-sky-500 rounded-full mr-2 animate-pulse"></span>
                        <span class="text-sm text-sky-600 font-semibold">✨ Solution comptable N°1 en RDC</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                        Gérez votre <span class="gradient-text">comptabilité</span><br>
                        en toute <span class="gradient-text">simplicité</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        CMCF Pro automatise vos écritures, génère vos bilans et suit votre trésorerie en temps réel. 
                        La solution complète pour les entreprises congolaises conformes aux normes OHADA.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" class="btn-primary px-8 py-3 text-white rounded-full font-semibold inline-flex items-center gap-2 shadow-lg">
                            <i class="fas fa-rocket"></i> Commencer gratuitement
                        </a>
                        
                    </div>
                    <div class="flex items-center gap-6 mt-8 pt-4">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center text-white text-sm font-bold border-2 border-white shadow-md">MK</div>
                            <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center text-white text-sm font-bold border-2 border-white shadow-md">JD</div>
                            <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center text-white text-sm font-bold border-2 border-white shadow-md">SN</div>
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-sm font-bold border-2 border-white">+2k</div>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-lg">+ 10 entreprises</p>
                            <p class="text-sm text-gray-500">nous font confiance</p>
                        </div>
                    </div>
                </div>
                
                <div class="relative animate-slide-left">
                    <div class="glass rounded-2xl p-6 border border-gray-200 shadow-2xl">
                        <div class="gradient-bg rounded-xl p-6">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <p class="text-white/80 text-sm">Chiffre d'affaires (2025)</p>
                                    <p class="text-3xl font-bold text-white">2.5M FC</p>
                                </div>
                                <div class="bg-white/20 px-3 py-1 rounded-full backdrop-blur">
                                    <span class="text-white text-sm font-semibold">+15.3%</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm text-white/80 mb-1">
                                        <span>Ventes</span>
                                        <span>1.8M FC</span>
                                    </div>
                                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                                        <div class="h-full bg-white rounded-full w-[72%]"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm text-white/80 mb-1">
                                        <span>Charges</span>
                                        <span>980K FC</span>
                                    </div>
                                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                                        <div class="h-full bg-white rounded-full w-[39%]"></div>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-white/20">
                                    <div class="flex justify-between">
                                        <span class="text-white/80">Résultat net</span>
                                        <span class="text-white font-bold text-lg">820K FC</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Badges flottants -->
                    <div class="absolute -top-6 -right-6 w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center shadow-2xl animate-bounce">
                        <i class="fas fa-chart-line text-white text-3xl"></i>
                    </div>
                    <div class="absolute -bottom-6 -left-6 w-20 h-20 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-coins text-white text-3xl"></i>
                    </div>
                    <div class="absolute top-1/2 -right-10 w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg animate-pulse-slow">
                        <i class="fas fa-file-invoice text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="chiffres" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-sky-500 font-semibold text-sm uppercase tracking-wider">Chiffres clés</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">
                    Notre impact en <span class="gradient-text">chiffres</span>
                </h2>
                <p class="text-gray-500 mt-3">Des résultats concrets pour nos clients</p>
            </div>
            <div class="grid md:grid-cols-4 gap-6">
                <div class="stat-card text-center hover-lift">
                    <div class="text-5xl mb-4 animate-pulse-slow">👥</div>
                    <div class="text-4xl font-bold gradient-text">3 </div>
                    <div class="text-gray-500 font-medium mt-1">Utilisateurs actifs</div>
                    <p class="text-xs text-gray-400 mt-2">+23% vs 2026</p>
                </div>
                <div class="stat-card text-center hover-lift">
                    <div class="text-5xl mb-4 animate-pulse-slow">🏢</div>
                    <div class="text-4xl font-bold gradient-text">14</div>
                    <div class="text-gray-500 font-medium mt-1">Entreprises clientes</div>
                    <p class="text-xs text-gray-400 mt-2">PME et Grandes entreprises</p>
                </div>
                <div class="stat-card text-center hover-lift">
                    <div class="text-5xl mb-4 animate-pulse-slow">📊</div>
                    <div class="text-4xl font-bold gradient-text">5K+</div>
                    <div class="text-gray-500 font-medium mt-1">Opérations traitées</div>
                    <p class="text-xs text-gray-400 mt-2">+12K ce mois</p>
                </div>
                <div class="stat-card text-center hover-lift">
                    <div class="text-5xl mb-4 animate-pulse-slow">💰</div>
                    <div class="text-4xl font-bold gradient-text">1.25B</div>
                    <div class="text-gray-500 font-medium mt-1">Montant total traité</div>
                    <p class="text-xs text-gray-400 mt-2">En Francs Congolais</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fonctionnalites" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-sky-500 font-semibold text-sm uppercase tracking-wider">Fonctionnalités</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3 mb-4">
                    Tout ce dont vous <span class="gradient-text">avez besoin</span>
                </h2>
                <p class="text-gray-600">Une solution complète pour gérer votre comptabilité en toute simplicité</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover-lift transition-all">
                    <div class="feature-icon">
                        <i class="fas fa-robot text-sky-500 text-2xl transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Journalisation automatique</h3>
                    <p class="text-gray-500 leading-relaxed">Écritures comptables automatiques selon les normes OHADA/SYSCOHADA</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover-lift transition-all">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line text-sky-500 text-2xl transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Bilan & Compte de résultat</h3>
                    <p class="text-gray-500 leading-relaxed">États financiers générés instantanément en un clic</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover-lift transition-all">
                    <div class="feature-icon">
                        <i class="fas fa-users text-sky-500 text-2xl transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Gestion des tiers</h3>
                    <p class="text-gray-500 leading-relaxed">Clients & fournisseurs centralisés avec suivi des encours</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover-lift transition-all">
                    <div class="feature-icon">
                        <i class="fas fa-chart-pie text-sky-500 text-2xl transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Tableaux de bord</h3>
                    <p class="text-gray-500 leading-relaxed">Visualisez vos performances avec des graphiques interactifs</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover-lift transition-all">
                    <div class="feature-icon">
                        <i class="fas fa-file-pdf text-sky-500 text-2xl transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Export PDF </h3>
                    <p class="text-gray-500 leading-relaxed">Générez vos rapports aux formats professionnels</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover-lift transition-all">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt text-sky-500 text-2xl transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Sécurité & Conformité</h3>
                    <p class="text-gray-500 leading-relaxed">Données sécurisées et conformes aux normes OHADA</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Processus Section -->
    <section id="processus" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-sky-500 font-semibold text-sm uppercase tracking-wider">Comment ça marche</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3 mb-4">
                    <span class="gradient-text">3 étapes</span> simples
                </h2>
                <p class="text-gray-600">Commencez en quelques minutes seulement</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <span class="text-white text-3xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Inscription</h3>
                    <p class="text-gray-500">Créez votre compte gratuitement en quelques clics</p>
                            </div>
                <div class="text-center group">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <span class="text-white text-3xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Visualisation</h3>
                    <p class="text-gray-500">Accédez à votre tableau de bord</p>
                </div>
                <div class="text-center group">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <span class="text-white text-3xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Gestion</h3>
                    <p class="text-gray-500">Saisissez vos opérations et laissez l'automatisation faire le reste</p>
                </div>
            </div>
        </div>
    </section> 
    <!-- CTA Section -->
    <section class="py-20 gradient-bg relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Prêt à <span class="underline decoration-2">simplifier</span> votre comptabilité ?
            </h2>
            <p class="text-lg text-white/90 mb-8">
                Rejoignez plus de 2 500 entreprises qui nous font confiance
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-sky-600 rounded-full font-semibold inline-flex items-center gap-2 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                    <i class="fas fa-rocket"></i> Commencer gratuitement
                </a>
                <a href="{{ route('login') }}" class="px-8 py-3 border-2 border-white text-white rounded-full font-semibold inline-flex items-center gap-2 hover:bg-white hover:text-sky-600 transition-all">
                    <i class="fas fa-sign-in-alt"></i> Déjà un compte
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold">CM</span>
                        </div>
                        <div>
                            <span class="font-bold text-xl">CMCF<span class="text-sky-400">.Pro</span></span>
                            <p class="text-xs text-gray-400">Solution comptable</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">Solution comptable professionnelle pour les TPME en RDC. Conforme aux normes OHADA.</p>
                    <div class="flex gap-3 mt-4">
                        <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-sky-600 transition"><i class="fab fa-facebook-f text-sm"></i></a>
                        <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-sky-600 transition"><i class="fab fa-twitter text-sm"></i></a>
                        <a href="#" class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center hover:bg-sky-600 transition"><i class="fab fa-linkedin-in text-sm"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-lg">Liens rapides</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#accueil" class="footer-link inline-block">Accueil</a></li>
                        <li><a href="#fonctionnalites" class="footer-link inline-block">Fonctionnalités</a></li>
                        <li><a href="#chiffres" class="footer-link inline-block">Chiffres clés</a></li>
                        <li><a href="#processus" class="footer-link inline-block">Processus</a></li>
                        
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-lg">Mon compte</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ route('login') }}" class="footer-link inline-block">Se connecter</a></li>
                        <li><a href="{{ route('register') }}" class="footer-link inline-block">Créer un compte</a></li>
                        <li><a href="#" class="footer-link inline-block">Mot de passe oublié</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-lg">Contact</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-center gap-2"><i class="fas fa-envelope w-5 text-sky-400"></i>@gmail.com</li>
                        <li class="flex items-center gap-2"><i class="fas fa-phone w-5 text-sky-400"></i> +243</li>
                        <li class="flex items-center gap-2"><i class="fas fa-map-marker-alt w-5 text-sky-400"></i> Lubumbashi, RDC</li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-800 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} CM CONSULTING-FORMATION SASU. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <style>
        @keyframes slide-in-right {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .animate-slide-right {
            animation: slide-in-right 0.3s ease-out;
        }
    </style>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
        
        function closeMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.add('hidden');
        }
        
        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                flashMessage.style.opacity = '0';
                flashMessage.style.transition = 'opacity 0.3s';
                setTimeout(() => flashMessage.remove(), 300);
            }
        }, 5000);
        
        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Animation au scroll
        const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.feature-card, .stat-card, .testimonial-card').forEach(el => {
            el.style.opacity = '0';
            observer.observe(el);
        });
    </script>
</body>
</html>