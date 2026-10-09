<?php
namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Balance;
use App\Models\Bilan;
use App\Models\Resultat;
use App\Models\Caisse;
use App\Models\Banque;
use App\Models\Ecriture;
use App\Models\PlanComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BanqueExport;
use App\Exports\CaisseExport;

class DirectriceAdjointeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:DA']);
    }
    
    /**
     * Afficher le tableau de bord
     */
    public function dashboard()
    {
        $operations_attente = Ecriture::where('statut', 'brouillon')->limit(5)->get();
        $montant_total_valide = Ecriture::where('statut', 'valide')->sum('montant_debit');
        
        $stats = [
            'en_attente_validation' => Ecriture::where('statut', 'brouillon')->count(),
            'total_validees' => Ecriture::where('statut', 'valide')->count(),
            'taux_validation' => $this->getValidationTaux(),
            'montant_total_valide' => $montant_total_valide,
            'total_banque' => Banque::sum('montant'),
            'solde_caisse' => Caisse::where('type', 'entree')->sum('montant') - Caisse::where('type', 'sortie')->sum('montant'),
            'nb_operations_mois' => Ecriture::whereMonth('date', now()->month)->count(),
            'dernier_bilan' => Bilan::latest()->first(),
            'dernier_resultat' => Resultat::latest()->first(),
            'evolution_validation' => $this->getEvolutionValidation(),
            'operations_attente' => $operations_attente,
        ];
        
        return view('directrice.dashboard', $stats);
    }
    
    /**
     * Afficher les statistiques détaillées
     */
    public function statistiques()
    {
        $validations_par_mois = $this->getValidationsParMois();
        $delai_moyen = $this->getDelaiMoyenValidation();
        $operations_par_type = Ecriture::select('compte_debit', DB::raw('COUNT(*) as nombre'))
            ->groupBy('compte_debit')
            ->orderBy('nombre', 'desc')
            ->limit(5)
            ->get();
        
        $stats = [
            'validations_par_mois' => $validations_par_mois,
            'taux_validation_global' => $this->getValidationTaux(),
            'delai_moyen' => $delai_moyen,
            'operations_en_attente' => Ecriture::where('statut', 'brouillon')->count(),
            'operations_en_retard' => Ecriture::where('statut', 'brouillon')
                ->where('created_at', '<', now()->subDays(7))
                ->count(),
            'operations_par_type' => $operations_par_type,
        ];
        
        return view('directrice.statistiques', $stats);
    }
    
    /**
     * Liste des opérations à valider
     */
    public function operationsAValider()
    {
        $operations = Ecriture::with(['saisiPar', 'client', 'fournisseur'])
            ->where('statut', 'brouillon')
            ->orderBy('date', 'desc')
            ->paginate(30);
        
        $total_montant = $operations->sum('montant_debit');
        
        return view('directrice.validations.index', compact('operations', 'total_montant'));
    }
    
    /**
     * Valider une opération
     */
    public function validerOperation(Request $request, $id)
    {
        $ecriture = Ecriture::findOrFail($id);
        
        if ($ecriture->statut !== 'brouillon') {
            return back()->with('error', 'Cette opération a déjà été validée');
        }
        
        $ecriture->update([
            'statut' => 'valide',
            'valide_par' => auth()->id(),
            'valide_le' => now(),
        ]);
        
        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'action' => 'Validation écriture',
            'table' => 'ecritures',
            'details' => "Écriture {$ecriture->numero_piece} validée",
            'ip' => $request->ip(),
        ]);
        
        return back()->with('success', 'Opération validée avec succès');
    }
    
    /**
     * Rejeter une opération
     */
    public function rejeterOperation(Request $request, $id)
    {
        $request->validate(['motif' => 'required|string']);
        
        $ecriture = Ecriture::findOrFail($id);
        
        $ecriture->update(['statut' => 'rejete']);
        
        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'action' => 'Rejet écriture',
            'table' => 'ecritures',
            'details' => "Écriture {$ecriture->numero_piece} rejetée. Motif: " . $request->motif,
            'ip' => $request->ip(),
        ]);
        
        return back()->with('warning', 'Opération rejetée');
    }
    
    /**
     * Afficher le journal comptable
     */
    public function journal(Request $request)
    {
        $query = Journal::with('ecriture')->whereHas('ecriture', function($q) {
            $q->where('statut', 'valide');
        });
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $journal = $query->orderBy('date', 'desc')->paginate(50);
        $comptes = PlanComptable::orderBy('numero')->get();
        
        return view('directrice.journal', compact('journal', 'comptes'));
    }
    
    /**
     * Exporter le journal en PDF
     */
    public function exportJournal(Request $request)
    {
        $query = Journal::with('ecriture')->whereHas('ecriture', function($q) {
            $q->where('statut', 'valide');
        });
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $journal = $query->orderBy('date', 'desc')->get();
        $total_debit = $journal->sum('debit');
        $total_credit = $journal->sum('credit');
        
        $pdf = Pdf::loadView('exports.journal', compact('journal', 'total_debit', 'total_credit'));
        return $pdf->download('journal_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Afficher la balance de vérification
     */
    public function balance()
    {
        $balance = Balance::orderBy('compte')->get();
        
        // Ajouter les noms des comptes
        foreach ($balance as $item) {
            $compteInfo = PlanComptable::where('numero', $item->compte)->first();
            $item->nom_compte = $compteInfo->nom ?? $item->compte;
        }
        
        $total_debiteur = $balance->sum('solde_debiteur');
        $total_crediteur = $balance->sum('solde_crediteur');
        $equilibre = $total_debiteur == $total_crediteur;
        
        return view('directrice.balance', compact('balance', 'total_debiteur', 'total_crediteur', 'equilibre'));
    }
    
    /**
     * Afficher le bilan comptable
     */
    public function bilan()
    {
        $bilan = Bilan::latest()->first();
        $bilanPrecedent = Bilan::where('date', '<', $bilan->date)->latest()->first();
        
        return view('directrice.bilan', compact('bilan', 'bilanPrecedent'));
    }
    
    /**
     * Afficher le compte de résultat
     */
    public function resultat()
    {
        $resultat = Resultat::latest()->first();
        $resultatPrecedent = Resultat::where('date', '<', $resultat->date)->latest()->first();
        
        return view('directrice.resultat', compact('resultat', 'resultatPrecedent'));
    }
    
    /**
     * Afficher la situation bancaire
     */
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
        $depots_moyen = Banque::where('type', 'depot')->avg('montant');
        
        // Données pour graphique
        $evolution_mois = [];
        $evolution_soldes = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $evolution_mois[] = $date->format('M Y');
            $evolution_soldes[] = Banque::whereYear('date', $date->year)
                ->whereMonth('date', '<=', $date->month)
                ->sum('montant');
        }
        
        return view('directrice.banque', compact('operations', 'solde', 'depots', 'retraits', 'depots_moyen', 'evolution_mois', 'evolution_soldes'));
    }
    
    /**
     * Exporter les opérations bancaires
     */
    public function exportBanque(Request $request)
    {
        $query = Banque::with('enregistrePar');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $operations = $query->orderBy('date', 'desc')->get();
        $solde = Banque::sum('montant');
        
        if ($request->format === 'excel') {
            return Excel::download(new BanqueExport($operations), 'banque_' . date('Y-m-d') . '.xlsx');
        } else {
            $pdf = Pdf::loadView('exports.banque', compact('operations', 'solde'));
            return $pdf->download('banque_' . date('Y-m-d') . '.pdf');
        }
    }
    
    /**
     * Afficher la situation de caisse
     */
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
        $entree_moyenne = Caisse::where('type', 'entree')->avg('montant');
        
        // Données pour graphique
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
        
        return view('directrice.caisse', compact('operations', 'entrees', 'sorties', 'solde', 'entree_moyenne', 'evolution_mois', 'evolution_entrees', 'evolution_sorties'));
    }
    
    /**
     * Exporter les opérations de caisse
     */
    public function exportCaisse(Request $request)
    {
        $query = Caisse::with('enregistrePar');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $operations = $query->orderBy('date', 'desc')->get();
        $entrees = Caisse::where('type', 'entree')->sum('montant');
        $sorties = Caisse::where('type', 'sortie')->sum('montant');
        $solde = $entrees - $sorties;
        
        if ($request->format === 'excel') {
            return Excel::download(new CaisseExport($operations), 'caisse_' . date('Y-m-d') . '.xlsx');
        } else {
            $pdf = Pdf::loadView('exports.caisse', compact('operations', 'entrees', 'sorties', 'solde'));
            return $pdf->download('caisse_' . date('Y-m-d') . '.pdf');
        }
    }
    
    // ==================== MÉTHODES PRIVÉES ====================
    
    /**
     * Calculer le taux de validation
     */
    private function getValidationTaux()
    {
        $total = Ecriture::count();
        $validees = Ecriture::where('statut', 'valide')->count();
        if ($total == 0) return 0;
        return round(($validees / $total) * 100, 1);
    }
    
    /**
     * Évolution des validations (6 derniers mois)
     */
    private function getEvolutionValidation()
    {
        $mois = [];
        $validees = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            
            $validees[] = Ecriture::where('statut', 'valide')
                ->whereYear('valide_le', $date->year)
                ->whereMonth('valide_le', $date->month)
                ->count();
        }
        
        return compact('mois', 'validees');
    }
    
    /**
     * Validations par mois (12 derniers mois)
     */
    private function getValidationsParMois()
    {
        return Ecriture::where('statut', 'valide')
            ->select(DB::raw('DATE_FORMAT(valide_le, "%Y-%m") as mois'), DB::raw('COUNT(*) as total'))
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->limit(12)
            ->get();
    }
    
    /**
     * Délai moyen de validation en heures
     */
    private function getDelaiMoyenValidation()
    {
        $ecritures = Ecriture::whereNotNull('valide_le')->get();
        if ($ecritures->count() == 0) return 0;
        
        $totalDelai = 0;
        foreach ($ecritures as $ecriture) {
            $totalDelai += $ecriture->valide_le->diffInHours($ecriture->created_at);
        }
        return round($totalDelai / $ecritures->count(), 1);
    }
}