<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - CMCF Compta Pro</title>
    
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
            padding: 40px 0;
        }
        
        .register-card {
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
        
        .strength-meter {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .strength-meter-fill {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background 0.3s ease;
        }
        
        .alert-animate {
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .info-badge {
            background: linear-gradient(135deg, #667eea20, #764ba220);
            border-left: 3px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Logo -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                    <i class="fas fa-chart-line text-3xl bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mt-3">CMCF<span class="text-indigo-200">.Pro</span></h1>
            </div>
            
            <!-- Carte d'inscription -->
            <div class="register-card bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-6 text-center">
                    <i class="fas fa-user-plus text-3xl text-white mb-2"></i>
                    <h2 class="text-xl font-bold text-white">Créer un compte</h2>
                    <p class="text-indigo-100 text-sm">Rejoignez CMCF Compta Pro</p>
                </div>
                
                <div class="p-8">
                    @if($errors->any())
                    <div class="alert-animate bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Erreur de validation :</strong>
                        <ul class="mt-2 ml-5 list-disc">
                            @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <!-- Information matricule -->
                    <div class="info-badge bg-indigo-50 p-3 rounded-lg mb-6 flex items-start gap-3">
                        <i class="fas fa-id-card text-indigo-500 mt-0.5"></i>
                        <div class="text-sm text-gray-600">
                            <span class="font-semibold">Format du matricule :</span><br>
                            <span class="text-xs">DR001 (Directeur), CO001 (Comptable)</span>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf
                        
                        <!-- Matricule -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-id-card mr-2 text-indigo-500"></i>Matricule <span class="text-red-500">*</span>
                            </label>
                            <div class="input-group-custom">
                                <input type="text" 
                                       name="matricule" 
                                       value="{{ old('matricule') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition uppercase"
                                       placeholder="Ex: CO001"
                                       required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle"></i> Le matricule détermine votre rôle dans l'application
                            </p>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Nom -->
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-user mr-2 text-indigo-500"></i>Nom <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-custom">
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                           placeholder="Nom"
                                           required>
                                </div>
                            </div>
                            
                            <!-- Prénom -->
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-user mr-2 text-indigo-500"></i>Prénom
                                </label>
                                <div class="input-group-custom">
                                    <input type="text" 
                                           name="prenom" 
                                           value="{{ old('prenom') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                           placeholder="Prénom">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-envelope mr-2 text-indigo-500"></i>Adresse email <span class="text-red-500">*</span>
                            </label>
                            <div class="input-group-custom">
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                       placeholder="utilisateur@cmcf.com"
                                       required>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Téléphone -->
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-phone mr-2 text-indigo-500"></i>Téléphone
                                </label>
                                <div class="input-group-custom">
                                    <input type="tel" 
                                           name="telephone" 
                                           value="{{ old('telephone') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                           placeholder="+243 812 212 290">
                                </div>
                            </div>
                            
                            <!-- Adresse -->
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i>Adresse
                                </label>
                                <div class="input-group-custom">
                                    <input type="text" 
                                           name="adresse" 
                                           value="{{ old('adresse') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                           placeholder="Lubumbashi, RDC">
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Mot de passe -->
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-lock mr-2 text-indigo-500"></i>Mot de passe <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-custom relative">
                                    <input type="password" 
                                           name="password" 
                                           id="password"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-12"
                                           placeholder="••••••••"
                                           required>
                                    <button type="button" onclick="togglePassword('password', 'eyeIcon1')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-500">
                                        <i id="eyeIcon1" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="strength-meter mt-2">
                                    <div class="strength-meter-fill" id="strengthFill"></div>
                                </div>
                                <p id="strengthText" class="text-xs text-gray-500 mt-1"></p>
                            </div>
                            
                            <!-- Confirmation -->
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-lock mr-2 text-indigo-500"></i>Confirmer le mot de passe <span class="text-red-500">*</span>
                                </label>
                                <div class="input-group-custom relative">
                                    <input type="password" 
                                           name="password_confirmation" 
                                           id="password_confirmation"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition pr-12"
                                           placeholder="••••••••"
                                           required>
                                    <button type="button" onclick="togglePassword('password_confirmation', 'eyeIcon2')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-500">
                                        <i id="eyeIcon2" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conditions -->
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="terms" required class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">
                                    J'accepte les <a href="#" class="text-indigo-600 hover:underline">conditions générales d'utilisation</a>
                                </span>
                            </label>
                        </div>
                        
                        <!-- Bouton inscription -->
                        <button type="submit" class="btn-gradient w-full py-3 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            Créer mon compte
                        </button>
                        
                        <!-- Lien connexion -->
                        <div class="mt-6 text-center pt-4 border-t border-gray-200">
                            <p class="text-gray-600">
                                Déjà un compte ?
                                <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:text-indigo-800 transition ml-1">
                                    Se connecter
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
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let message = '';
                let color = '';
                
                if (password.length > 0) {
                    if (password.length >= 6) strength += 25;
                    if (password.match(/[a-z]+/)) strength += 25;
                    if (password.match(/[A-Z]+/)) strength += 25;
                    if (password.match(/[0-9]+/)) strength += 25;
                    if (password.match(/[$@#&!]+/)) strength += 25;
                    
                    if (strength <= 25) { message = 'Très faible'; color = '#ef4444'; }
                    else if (strength <= 50) { message = 'Faible'; color = '#f59e0b'; }
                    else if (strength <= 75) { message = 'Moyen'; color = '#3b82f6'; }
                    else { message = 'Fort'; color = '#10b981'; }
                    
                    strengthFill.style.width = Math.min(strength, 100) + '%';
                    strengthFill.style.backgroundColor = color;
                    strengthText.textContent = message;
                    strengthText.style.color = color;
                } else {
                    strengthFill.style.width = '0';
                    strengthText.textContent = '';
                }
            });
        }
        
        // Form validation
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const terms = document.getElementById('terms').checked;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas !');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères !');
                return false;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Veuillez accepter les conditions générales d\'utilisation');
                return false;
            }
        });
    </script>
</body>
</html>