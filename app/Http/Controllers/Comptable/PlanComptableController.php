<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\PlanComptable;
use App\Models\Ecriture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanComptableController extends Controller
{
    // Supprimer le constructeur avec middleware
    // Les middlewares sont déjà définis dans les routes

    public function index(Request $request)
    {
        $query = PlanComptable::query();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('numero', 'like', '%' . $request->search . '%')
                  ->orWhere('nom', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('classe')) {
            $query->where('classe', $request->classe);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $comptes = $query->orderBy('numero')->paginate(50);
        
        $stats = [
            'total' => PlanComptable::count(),
            'par_classe' => PlanComptable::select('classe', DB::raw('count(*) as total'))->groupBy('classe')->get(),
        ];
        
        return view('comptable.plan-comptable.index', compact('comptes', 'stats'));
    }
    
    public function create()
    {
        return view('comptable.plan-comptable.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:20|unique:plan_comptable',
            'nom' => 'required|string|max:255',
            'classe' => 'required|string|max:10',
            'type' => 'required|in:Actif,Passif,Charge,Produit',
            'solde_initial' => 'nullable|numeric|min:0',
        ]);
        
        $compte = PlanComptable::create([
            'numero' => $validated['numero'],
            'nom' => $validated['nom'],
            'classe' => $validated['classe'],
            'type' => $validated['type'],
            'solde_initial' => $validated['solde_initial'] ?? 0,
            'solde_actuel' => $validated['solde_initial'] ?? 0,
        ]);
        
        return redirect()->to('/comptable/plan-comptable')->with('success', 'Compte ajouté avec succès');
    }
    
    public function edit($id)
    {
        $compte = PlanComptable::findOrFail($id);
        return view('comptable.plan-comptable.edit', compact('compte'));
    }
    
    public function update(Request $request, $id)
    {
        $compte = PlanComptable::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'classe' => 'required|string|max:10',
            'type' => 'required|in:Actif,Passif,Charge,Produit',
        ]);
        
        $compte->update($validated);
        
        return redirect()->to('/comptable/plan-comptable')->with('success', 'Compte modifié avec succès');
    }
    
    public function confirmDelete($id)
    {
        $compte = PlanComptable::findOrFail($id);
        
        // Vérifier si le compte est utilisé dans des écritures
        $usedInEcritures = false;
        if (class_exists('App\Models\Ecriture')) {
            $usedInEcritures = Ecriture::where('compte_debit', $compte->numero)
                ->orWhere('compte_credit', $compte->numero)
                ->exists();
        }
        
        return view('comptable.plan-comptable.confirm-delete', compact('compte', 'usedInEcritures'));
    }
    
    public function destroy($id)
    {
        $compte = PlanComptable::findOrFail($id);
        
        // Vérifier si le compte est utilisé dans des écritures
        $usedInEcritures = false;
        if (class_exists('App\Models\Ecriture')) {
            $usedInEcritures = Ecriture::where('compte_debit', $compte->numero)
                ->orWhere('compte_credit', $compte->numero)
                ->exists();
        }
        
        if ($usedInEcritures) {
            return redirect()->to('/comptable/plan-comptable')
                ->with('error', 'Ce compte est utilisé dans des écritures comptables. Impossible de le supprimer.');
        }
        
        $compte->delete();
        
        return redirect()->to('/comptable/plan-comptable')->with('success', 'Compte supprimé avec succès');
    }
    
    public function show($id)
    {
        $compte = PlanComptable::findOrFail($id);
        return view('comptable.plan-comptable.show', compact('compte'));
    }
}