<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        
        // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.'])->onlyInput('email');
        }
        
        // Vérifier si le compte est actif
        if (!$user->actif) {
            return back()->withErrors(['email' => 'Votre compte a été désactivé. Contactez l\'administrateur.'])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Mettre à jour la dernière connexion
            $user->update(['derniere_connexion' => now()]);
            
            // Journaliser la connexion
            Log::create([
                'user_id' => $user->id,
                'action' => 'Connexion',
                'table' => 'users',
                'details' => "Utilisateur {$user->email} connecté",
                'ip' => $request->ip(),
            ]);
            
            // Rediriger selon le rôle
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors(['email' => 'Les identifiants sont incorrects.'])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'Déconnexion',
                'table' => 'users',
                'details' => "Utilisateur {$user->email} déconnecté",
                'ip' => $request->ip(),
            ]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
    
    private function redirectBasedOnRole($user)
    {
        $roleCode = $user->role->code;
        
        switch($roleCode) {
            case 'DR':
                return redirect()->route('directeur.dashboard')->with('success', 'Bienvenue Directeur Général !');
            case 'DA':
                return redirect()->route('directrice.dashboard')->with('success', 'Bienvenue Directrice Générale Adjointe !');
            case 'CO':
                return redirect()->route('comptable.dashboard')->with('success', 'Bienvenue Comptable !');
            case 'CA':
                return redirect()->route('caissier.dashboard')->with('success', 'Bienvenue Caissière !');
            case 'VE':
                return redirect()->route('vendeur.dashboard')->with('success', 'Bienvenue Vendeur !');
            case 'AD':
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenue Administrateur !');
            default:
                return redirect()->route('dashboard')->with('success', 'Connexion réussie !');
        }
    }
}