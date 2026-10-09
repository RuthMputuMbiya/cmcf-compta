<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanComptable;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PlanComptableImport;
use App\Exports\PlanComptableExport;

class PlanComptableController extends Controller
{
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
        $classes = PlanComptable::select('classe')->distinct()->orderBy('classe')->pluck('classe');
        $types = ['Actif', 'Passif', 'Charge', 'Produit'];
        
        $stats = [
            'total' => PlanComptable::count(),
            'par_classe' => PlanComptable::select('classe', DB::raw('count(*) as total'))->groupBy('classe')->get(),
            'par_type' => PlanComptable::select('type', DB::raw('count(*) as total'))->groupBy('type')->get(),
        ];
        
        return view('admin.plan-comptable.index', compact('comptes', 'classes', 'types', 'stats'));
    }
    
    public function create()
    {
        return view('admin.plan-comptable.create');
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
        
        $this->logAction('Création compte comptable', 'plan_comptable', $compte->id, "Compte {$compte->numero} - {$compte->nom} créé");
        
        return redirect()->to('/admin/plan-comptable')->with('success', 'Compte ajouté avec succès');
    }
    
    public function edit($id)
    {
        $compte = PlanComptable::findOrFail($id);
        return view('admin.plan-comptable.edit', compact('compte'));
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
        
        $this->logAction('Modification compte comptable', 'plan_comptable', $compte->id, "Compte {$compte->numero} modifié");
        
        return redirect()->to('/admin/plan-comptable')->with('success', 'Compte modifié avec succès');
    }
    
    public function confirmDelete($id)
    {
        $compte = PlanComptable::findOrFail($id);
        
        $usedInEcritures = false;
        if (class_exists('\App\Models\Ecriture')) {
            $usedInEcritures = \App\Models\Ecriture::where('compte_debit', $compte->numero)
                ->orWhere('compte_credit', $compte->numero)
                ->exists();
        }
        
        return view('admin.plan-comptable.confirm-delete', compact('compte', 'usedInEcritures'));
    }
    
    public function destroy($id)
    {
        $compte = PlanComptable::findOrFail($id);
        
        $usedInEcritures = false;
        if (class_exists('\App\Models\Ecriture')) {
            $usedInEcritures = \App\Models\Ecriture::where('compte_debit', $compte->numero)
                ->orWhere('compte_credit', $compte->numero)
                ->exists();
        }
        
        if ($usedInEcritures) {
            return redirect()->to('/admin/plan-comptable')
                ->with('error', 'Ce compte est utilisé dans des écritures comptables. Impossible de le supprimer.');
        }
        
        $this->logAction('Suppression compte comptable', 'plan_comptable', $compte->id, "Compte {$compte->numero} - {$compte->nom} supprimé");
        $compte->delete();
        
        return redirect()->to('/admin/plan-comptable')->with('success', 'Compte supprimé avec succès');
    }
    
    public function show($id)
    {
        $compte = PlanComptable::findOrFail($id);
        return view('admin.plan-comptable.show', compact('compte'));
    }
    
    public function importForm()
    {
        return view('admin.plan-comptable.import');
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'fichier' => 'required|mimes:xlsx,xls,csv',
        ]);
        
        try {
            Excel::import(new PlanComptableImport, $request->file('fichier'));
            
            $this->logAction('Import plan comptable', 'plan_comptable', null, 'Import réussi');
            
            return redirect()->to('/admin/plan-comptable')->with('success', 'Plan comptable importé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }
    
    public function export()
    {
        $this->logAction('Export plan comptable', 'plan_comptable', null, 'Export Excel');
        
        return Excel::download(new PlanComptableExport, 'plan_comptable_' . date('Y-m-d') . '.xlsx');
    }
    
    public function template()
    {
        $this->logAction('Téléchargement template plan comptable', 'plan_comptable', null, 'Template téléchargé');
        
        $headers = ['numero', 'nom', 'classe', 'type', 'solde_initial'];
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['101', 'Capital', '1', 'Passif', '0']);
            fputcsv($file, ['411', 'Clients', '4', 'Actif', '0']);
            fputcsv($file, ['401', 'Fournisseurs', '4', 'Passif', '0']);
            fputcsv($file, ['511', 'Banque', '5', 'Actif', '0']);
            fputcsv($file, ['601', 'Achats', '6', 'Charge', '0']);
            fputcsv($file, ['701', 'Ventes', '7', 'Produit', '0']);
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_plan_comptable.csv"',
        ]);
    }
    
    public function updateSolde(Request $request, $id)
    {
        $compte = PlanComptable::findOrFail($id);
        
        $validated = $request->validate([
            'solde_initial' => 'required|numeric',
        ]);
        
        $compte->update([
            'solde_initial' => $validated['solde_initial'],
            'solde_actuel' => $validated['solde_initial'],
        ]);
        
        $this->logAction('Mise à jour solde compte', 'plan_comptable', $compte->id, "Compte {$compte->numero} solde initial: {$validated['solde_initial']}");
        
        return redirect()->to('/admin/plan-comptable')->with('success', 'Solde mis à jour avec succès');
    }
    
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $comptes = PlanComptable::where('numero', 'like', "%{$search}%")
            ->orWhere('nom', 'like', "%{$search}%")
            ->limit(10)
            ->get();
        
        return response()->json($comptes);
    }
    
    private function logAction($action, $table, $recordId, $details)
    {
        try {
            if (class_exists('\App\Models\Log')) {
                \App\Models\Log::create([
                    'user_id' => auth()->id(),
                    'action' => $action,
                    'table' => $table,
                    'record_id' => $recordId,
                    'details' => $details,
                    'ip' => request()->ip(),
                ]);
            }
        } catch (\Exception $e) {
            // Ne pas bloquer l'application si le log échoue
        }
    }
}