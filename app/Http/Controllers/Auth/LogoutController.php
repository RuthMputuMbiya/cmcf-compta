<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Journaliser la déconnexion
            \App\Models\Log::create([
                'user_id' => $user->id,
                'action' => 'Déconnexion',
                'table' => 'users',
                'details' => 'Utilisateur déconnecté',
                'ip' => $request->ip(),
            ]);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}