<?php
namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Banque;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CaissierController extends Controller
{
    // ==================== DASHBOARD ====================
    public function dashboard()
    {
        $total_entrees_caisse = Caisse::where('type', 'entree')->sum('montant');
        $total_sorties_caisse = Caisse::where('type', 'sortie')->sum('montant');
        $solde_caisse = $total_entrees_caisse - $total_sorties_caisse;
        
        $solde_banque = Banque::sum('montant');
        $depots_banque = Banque::where('type', 'depot')->sum('montant');
        $retraits_banque = Banque::where('type', 'retrait')->sum('montant');
        
        $entrees_jour = Caisse::where('type', 'entree')->whereDate('date', today())->sum('montant');
        $sorties_jour = Caisse::where('type', 'sortie')->whereDate('date', today())->sum('montant');
        $depots_jour = Banque::where('type', 'depot')->whereDate('date', today())->sum('montant');
        $retraits_jour = Banque::where('type', 'retrait')->whereDate('date', today())->sum('montant');
        
        $nb_transactions_jour = Caisse::whereDate('date', today())->count() + Banque::whereDate('date', today())->count();
        
        $dernieres_operations_caisse = Caisse::with('enregistrePar')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        $dernieres_operations_banque = Banque::with('enregistrePar')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        // Données pour graphiques
        $evolution_mois = [];
        $evolution_entrees = [];
        $evolution_sorties = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $evolution_mois[] = $date->format('M Y');
            $evolution_entrees[] = Caisse::where('type', 'entree')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('montant');
            $evolution_sorties[] = Caisse::where('type', 'sortie')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('montant');
        }
        
        return view('caissier.dashboard', compact(
            'total_entrees_caisse', 'total_sorties_caisse', 'solde_caisse',
            'solde_banque', 'depots_banque', 'retraits_banque',
            'entrees_jour', 'sorties_jour', 'depots_jour', 'retraits_jour',
            'nb_transactions_jour', 'dernieres_operations_caisse', 'dernieres_operations_banque',
            'evolution_mois', 'evolution_entrees', 'evolution_sorties'
        ));
    }
    
    // ==================== CAISSE ====================
    public function caisse(Request $request)
    {
        $query = Caisse::with('enregistrePar');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $operations = $query->orderBy('date', 'desc')->paginate(50);
        $entrees = Caisse::where('type', 'entree')->sum('montant');
        $sorties = Caisse::where('type', 'sortie')->sum('montant');
        $solde = $entrees - $sorties;
        
        return view('caissier.caisse.index', compact('operations', 'entrees', 'sorties', 'solde'));
    }
    
    public function createEntree()
    {
        return view('caissier.caisse.entree');
    }
    
    public function storeEntree(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'observation' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            $caisse = Caisse::create([
                'date' => $validated['date'],
                'type' => 'entree',
                'libelle' => $validated['libelle'],
                'montant' => $validated['montant'],
                'reference' => $validated['reference'],
                'observation' => $validated['observation'],
                'enregistre_par' => auth()->id(),
            ]);
            
            $this->logAction('Entrée caisse', 'caisses', $caisse->id, "Entrée de {$validated['montant']} FC - {$validated['libelle']}");
            
            DB::commit();
            return redirect()->to('/caissier/caisse')->with('success', 'Entrée en caisse enregistrée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function createSortie()
    {
        $solde = Caisse::where('type', 'entree')->sum('montant') - Caisse::where('type', 'sortie')->sum('montant');
        return view('caissier.caisse.sortie', compact('solde'));
    }
    
    public function storeSortie(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'observation' => 'nullable|string',
        ]);
        
        $solde = Caisse::where('type', 'entree')->sum('montant') - Caisse::where('type', 'sortie')->sum('montant');
        
        if ($validated['montant'] > $solde) {
            return back()->with('error', 'Solde de caisse insuffisant. Solde actuel: ' . number_format($solde, 0, ',', ' ') . ' FC');
        }
        
        DB::beginTransaction();
        try {
            $caisse = Caisse::create([
                'date' => $validated['date'],
                'type' => 'sortie',
                'libelle' => $validated['libelle'],
                'montant' => $validated['montant'],
                'reference' => $validated['reference'],
                'observation' => $validated['observation'],
                'enregistre_par' => auth()->id(),
            ]);
            
            $this->logAction('Sortie caisse', 'caisses', $caisse->id, "Sortie de {$validated['montant']} FC - {$validated['libelle']}");
            
            DB::commit();
            return redirect()->to('/caissier/caisse')->with('success', 'Sortie de caisse enregistrée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function deleteOperationCaisse($id)
    {
        $operation = Caisse::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $operation->delete();
            DB::commit();
            
            $this->logAction('Suppression opération caisse', 'caisses', $operation->id, "Opération supprimée");
            return redirect()->to('/caissier/caisse')->with('success', 'Opération supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    // ==================== BANQUE ====================
    public function banque(Request $request)
    {
        $query = Banque::with('enregistrePar');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $operations = $query->orderBy('date', 'desc')->paginate(50);
        $solde = Banque::sum('montant');
        $depots = Banque::where('type', 'depot')->sum('montant');
        $retraits = Banque::where('type', 'retrait')->sum('montant');
        
        return view('caissier.banque.index', compact('operations', 'solde', 'depots', 'retraits'));
    }
    
    public function createDepot()
    {
        return view('caissier.banque.depot');
    }
    
    public function storeDepot(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'observation' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            $banque = Banque::create([
                'date' => $validated['date'],
                'type' => 'depot',
                'libelle' => $validated['libelle'],
                'montant' => $validated['montant'],
                'reference' => $validated['reference'],
                'observation' => $validated['observation'],
                'enregistre_par' => auth()->id(),
            ]);
            
            $this->logAction('Dépôt banque', 'banques', $banque->id, "Dépôt de {$validated['montant']} FC - {$validated['libelle']}");
            
            DB::commit();
            return redirect()->to('/caissier/banque')->with('success', 'Dépôt bancaire enregistré avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function createRetrait()
    {
        $solde = Banque::sum('montant');
        return view('caissier.banque.retrait', compact('solde'));
    }
    
    public function storeRetrait(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'observation' => 'nullable|string',
        ]);
        
        $solde = Banque::sum('montant');
        
        if ($validated['montant'] > $solde) {
            return back()->with('error', 'Solde bancaire insuffisant. Solde actuel: ' . number_format($solde, 0, ',', ' ') . ' FC');
        }
        
        DB::beginTransaction();
        try {
            $banque = Banque::create([
                'date' => $validated['date'],
                'type' => 'retrait',
                'libelle' => $validated['libelle'],
                'montant' => -$validated['montant'],
                'reference' => $validated['reference'],
                'observation' => $validated['observation'],
                'enregistre_par' => auth()->id(),
            ]);
            
            $this->logAction('Retrait banque', 'banques', $banque->id, "Retrait de {$validated['montant']} FC - {$validated['libelle']}");
            
            DB::commit();
            return redirect()->to('/caissier/banque')->with('success', 'Retrait bancaire enregistré avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function deleteOperationBanque($id)
    {
        $operation = Banque::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $operation->delete();
            DB::commit();
            
            $this->logAction('Suppression opération banque', 'banques', $operation->id, "Opération supprimée");
            return redirect()->to('/caissier/banque')->with('success', 'Opération supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    // ==================== STATISTIQUES ====================
    public function statistiques()
    {
        $total_entrees = Caisse::where('type', 'entree')->sum('montant');
        $total_sorties = Caisse::where('type', 'sortie')->sum('montant');
        $total_depots = Banque::where('type', 'depot')->sum('montant');
        $total_retraits = Banque::where('type', 'retrait')->sum('montant');
        
        $evolution_mois = [];
        $evolution_entrees = [];
        $evolution_sorties = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $evolution_mois[] = $date->format('M Y');
            $evolution_entrees[] = Caisse::where('type', 'entree')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('montant');
            $evolution_sorties[] = Caisse::where('type', 'sortie')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('montant');
        }
        
        $top_operations = Caisse::orderBy('montant', 'desc')->limit(10)->get();
        
        return view('caissier.statistiques', compact(
            'total_entrees', 'total_sorties', 'total_depots', 'total_retraits',
            'evolution_mois', 'evolution_entrees', 'evolution_sorties', 'top_operations'
        ));
    }
    
    // ==================== RAPPORTS ====================
    public function rapportJournalier(Request $request)
    {
        $date = $request->date ?? today();
        
        $operations_caisse = Caisse::whereDate('date', $date)->get();
        $operations_banque = Banque::whereDate('date', $date)->get();
        
        $total_entrees = $operations_caisse->where('type', 'entree')->sum('montant');
        $total_sorties = $operations_caisse->where('type', 'sortie')->sum('montant');
        $solde_cloture = $total_entrees - $total_sorties;
        
        $total_depots = $operations_banque->where('type', 'depot')->sum('montant');
        $total_retraits = $operations_banque->where('type', 'retrait')->sum('montant');
        
        return view('caissier.rapports.journalier', compact(
            'date', 'operations_caisse', 'operations_banque',
            'total_entrees', 'total_sorties', 'solde_cloture',
            'total_depots', 'total_retraits'
        ));
    }
    
    public function rapportMensuel(Request $request)
    {
        $mois = $request->mois ?? now()->month;
        $annee = $request->annee ?? now()->year;
        
        $operations_caisse = Caisse::whereYear('date', $annee)->whereMonth('date', $mois)->get();
        $operations_banque = Banque::whereYear('date', $annee)->whereMonth('date', $mois)->get();
        
        $total_entrees_mois = $operations_caisse->where('type', 'entree')->sum('montant');
        $total_sorties_mois = $operations_caisse->where('type', 'sortie')->sum('montant');
        $solde_moyen = ($total_entrees_mois + $total_sorties_mois) / 2;
        $total_operations = $operations_caisse->count() + $operations_banque->count();
        
        // Récapitulatif par jour
        $recap_journalier = [];
        $jours = [];
        $entrees_journalieres = [];
        $sorties_journalieres = [];
        
        for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $mois, $annee); $i++) {
            $date = $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $entrees = Caisse::whereDate('date', $date)->where('type', 'entree')->sum('montant');
            $sorties = Caisse::whereDate('date', $date)->where('type', 'sortie')->sum('montant');
            $solde = $entrees - $sorties;
            
            $recap_journalier[] = ['jour' => $i, 'entrees' => $entrees, 'sorties' => $sorties, 'solde' => $solde];
            $jours[] = $i;
            $entrees_journalieres[] = $entrees;
            $sorties_journalieres[] = $sorties;
        }
        
        $solde_final_mois = end($recap_journalier)['solde'] ?? 0;
        
        // Répartition par type
        $entrees_labels = ['Espèces', 'Chèques', 'Virements'];
        $entrees_values = [
            Caisse::where('type', 'entree')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant'),
            0, 0
        ];
        
        $sorties_labels = ['Achats', 'Salaires', 'Frais généraux', 'Autres'];
        $sorties_values = [
            Caisse::where('type', 'sortie')->where('libelle', 'like', '%achat%')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant'),
            Caisse::where('type', 'sortie')->where('libelle', 'like', '%salaire%')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant'),
            Caisse::where('type', 'sortie')->where('libelle', 'like', '%frais%')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant'),
            Caisse::where('type', 'sortie')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant') - 
            (Caisse::where('type', 'sortie')->where('libelle', 'like', '%achat%')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant') +
             Caisse::where('type', 'sortie')->where('libelle', 'like', '%salaire%')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant') +
             Caisse::where('type', 'sortie')->where('libelle', 'like', '%frais%')->whereYear('date', $annee)->whereMonth('date', $mois)->sum('montant'))
        ];
        
        return view('caissier.rapports.mensuel', compact(
            'mois', 'annee', 'operations_caisse', 'operations_banque',
            'total_entrees_mois', 'total_sorties_mois', 'solde_moyen', 'total_operations',
            'recap_journalier', 'jours', 'entrees_journalieres', 'sorties_journalieres',
            'solde_final_mois', 'entrees_labels', 'entrees_values', 'sorties_labels', 'sorties_values'
        ));
    }
    
    public function exportRapportJournalierPDF(Request $request)
    {
        $date = $request->date ?? today();
        
        $operations_caisse = Caisse::whereDate('date', $date)->get();
        $operations_banque = Banque::whereDate('date', $date)->get();
        
        $total_entrees = $operations_caisse->where('type', 'entree')->sum('montant');
        $total_sorties = $operations_caisse->where('type', 'sortie')->sum('montant');
        $solde_cloture = $total_entrees - $total_sorties;
        
        $total_depots = $operations_banque->where('type', 'depot')->sum('montant');
        $total_retraits = $operations_banque->where('type', 'retrait')->sum('montant');
        
        // CORRECTION: Utiliser un chemin de vue simple
        $pdf = Pdf::loadView('exports.rapport-journalier-caisse', compact(
            'date', 'operations_caisse', 'operations_banque',
            'total_entrees', 'total_sorties', 'solde_cloture',
            'total_depots', 'total_retraits'
        ));
        
        return $pdf->download('rapport_caisse_' . $date . '.pdf');
    }
    
    public function exportRapportMensuelPDF(Request $request)
    {
        $mois = $request->mois ?? now()->month;
        $annee = $request->annee ?? now()->year;
        
        $operations_caisse = Caisse::whereYear('date', $annee)->whereMonth('date', $mois)->get();
        $operations_banque = Banque::whereYear('date', $annee)->whereMonth('date', $mois)->get();
        
        $total_entrees_mois = $operations_caisse->where('type', 'entree')->sum('montant');
        $total_sorties_mois = $operations_caisse->where('type', 'sortie')->sum('montant');
        
        // CORRECTION: Utiliser un chemin de vue simple
        $pdf = Pdf::loadView('exports.rapport-mensuel-caisse', compact(
            'mois', 'annee', 'operations_caisse', 'operations_banque',
            'total_entrees_mois', 'total_sorties_mois'
        ));
        
        return $pdf->download('rapport_caisse_' . $annee . '_' . $mois . '.pdf');
    }
    
    // ==================== MÉTHODES PRIVÉES ====================
    private function logAction($action, $table, $recordId, $details)
    {
        Log::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'table' => $table,
            'details' => $details,
            'ip' => request()->ip(),
        ]);
    }
}