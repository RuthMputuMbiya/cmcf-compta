<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - CMCF Compta Pro</title>
    
    <!-- Tailwind CSS + DaisyUI -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            animation: fadeInUp 0.6s ease-out;
            backdrop-filter: blur(10px);
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .input-group-custom {
            transition: all 0.3s ease;
        }
        
        .input-group-custom:focus-within {
            transform: translateX(5px);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        .alert-animate {
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-xl">
                    <i class="fas fa-chart-line text-4xl bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mt-4">CMCF<span class="text-indigo-200">.Pro</span></h1>
                <p class="text-white/80 mt-2">Solution comptable professionnelle</p>
            </div>
            
            <!-- Carte de connexion -->
            <div class="login-card bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-center">
                    <i class="fas fa-sign-in-alt text-4xl text-white mb-3"></i>
                    <h2 class="text-2xl font-bold text-white">Connexion</h2>
                    <p class="text-indigo-100 text-sm mt-1">Accédez à votre espace de travail</p>
                </div>
                
                <div class="p-8">
                    <!-- Messages flash -->
                    @if(session('success'))
                        <div class="alert-animate bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert-animate bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert-animate bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            @foreach($errors->all() as $error)
                                <div class="text-sm">{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Email -->
                        <div class="mb-5">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-envelope mr-2 text-indigo-500"></i>Adresse email
                            </label>
                            <div class="input-group-custom">
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                       placeholder="exemple@cmcf.com"
                                       required autofocus>
                            </div>
                        </div>
                        
                        <!-- Mot de passe -->
                        <div class="mb-5">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-lock mr-2 text-indigo-500"></i>Mot de passe
                            </label>
                            <div class="input-group-custom relative">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-12"
                                       placeholder="••••••••"
                                       required>
                                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-500">
                                    <i id="eyeIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Options -->
                        <div class="flex justify-between items-center mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                            </label>
                            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 transition">Mot de passe oublié ?</a>
                        </div>
                        
                        <!-- Bouton connexion -->
                        <button type="submit" class="btn-gradient w-full py-3 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            Se connecter
                        </button>
                        
                        <!-- Lien inscription -->
                        <div class="mt-6 text-center pt-4 border-t border-gray-200">
                            <p class="text-gray-600">
                                Pas encore de compte ?
                                <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:text-indigo-800 transition ml-1">
                                    Créer un compte gratuitement
                                </a>
                            </p>
                            <p class="text-gray-500 text-sm mt-3">
                                <a href="{{ url('/') }}" class="hover:text-indigo-600 transition">
                                    <i class="fas fa-home mr-1"></i>Retour à l'accueil
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-8 text-white/70 text-sm">
                <p>&copy; {{ date('Y') }} CMCF Consulting - Tous droits réservés</p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>