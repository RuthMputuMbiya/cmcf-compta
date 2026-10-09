<?php
namespace App\Http\Controllers;

use App\Models\Ecriture;
use App\Models\Journal;
use App\Models\Bilan;
use App\Models\Resultat;
use App\Models\Caisse;
use App\Models\Banque;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleCode = $user->role->code;
        
        // Rediriger vers le dashboard spécifique selon le rôle
        switch($roleCode) {
            case 'DR':
                return redirect()->route('directeur.dashboard');
            case 'DA':
                return redirect()->route('directrice.dashboard');
            case 'CO':
                return redirect()->route('comptable.dashboard');
            case 'CA':
                return redirect()->route('caissier.dashboard');
            case 'VE':
                return redirect()->route('vendeur.dashboard');
            case 'AD':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('directeur.dashboard');
        }
    }
}