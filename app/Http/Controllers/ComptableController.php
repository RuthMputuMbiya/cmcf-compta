<?php
namespace App\Http\Controllers;

use App\Models\Ecriture;
use App\Models\Journal;
use App\Models\GrandLivre;
use App\Models\Balance;
use App\Models\Bilan;
use App\Models\Resultat;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\PlanComptable;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JournalExport;
use App\Exports\GrandLivreExport;
use App\Exports\BalanceExport;
use App\Exports\BilanExport;
use App\Exports\ResultatExport;

class ComptableController extends Controller
{
    // ==================== DASHBOARD ====================
    public function dashboard()
    {
        $dernieres_ecritures = Ecriture::with('client')->orderBy('date', 'desc')->limit(5)->get();
        
        $evolution_mois = [];
        $evolution_counts = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $evolution_mois[] = $date->format('M Y');
            $evolution_counts[] = Ecriture::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->count();
        }
        
        $stats = [
            'total_ecritures' => Ecriture::count(),
            'ecritures_mois' => Ecriture::whereMonth('date', now()->month)->count(),
            'total_clients' => Client::count(),
            'total_fournisseurs' => Fournisseur::count(),
            'total_debit' => Ecriture::sum('montant_debit'),
            'total_credit' => Ecriture::sum('montant_credit'),
            'dernier_bilan' => Bilan::latest()->first(),
            'dernier_resultat' => Resultat::latest()->first(),
            'ecritures_en_attente' => Ecriture::where('statut', 'brouillon')->count(),
            'resultat_net' => Resultat::latest()->first()->resultat_net ?? 0,
            'dernieres_ecritures' => $dernieres_ecritures,
            'evolution_mois' => $evolution_mois,
            'evolution_counts' => $evolution_counts,
        ];
        
        return view('comptable.dashboard', $stats);
    }
    
    // ==================== ÉCRITURES COMPTABLES ====================
    public function ecritures(Request $request)
    {
        $query = Ecriture::with(['client', 'fournisseur', 'saisiPar', 'validePar']);
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        $ecritures = $query->orderBy('date', 'desc')->paginate(50);
        $total_debit = $ecritures->sum('montant_debit');
        $total_credit = $ecritures->sum('montant_credit');
        
        return view('comptable.ecritures.index', compact('ecritures', 'total_debit', 'total_credit'));
    }
    
    public function createEcriture()
    {
        $clients = Client::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $comptes = PlanComptable::orderBy('numero')->get();
        
        return view('comptable.ecritures.create', compact('clients', 'fournisseurs', 'comptes'));
    }
    
    /**
     * Générer un numéro de pièce unique
     * Format: PC-YYYYMMDD-XXXX (ex: PC-20260420-0001)
     */
    private function genererNumeroPiece()
    {
        $prefix = 'PC-' . date('Ymd') . '-';
        
        // Récupérer le dernier numéro de la journée
        $dernier = Ecriture::where('numero_piece', 'LIKE', $prefix . '%')
                           ->orderBy('numero_piece', 'desc')
                           ->first();
        
        if ($dernier) {
            // Extraire le compteur (ex: 0004) et l'incrémenter
            $dernierNumero = (int) substr($dernier->numero_piece, -4);
            $nouveauNumero = str_pad($dernierNumero + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nouveauNumero = '0001';
        }
        
        $numeroPiece = $prefix . $nouveauNumero;
        
        // Sécurité : vérifier que le numéro n'existe pas (boucle de sécurité)
        $compteur = 0;
        while (Ecriture::where('numero_piece', $numeroPiece)->exists() && $compteur < 100) {
            $nouveauNumero = str_pad(((int) $nouveauNumero) + 1, 4, '0', STR_PAD_LEFT);
            $numeroPiece = $prefix . $nouveauNumero;
            $compteur++;
        }
        
        return $numeroPiece;
    }
    
    public function storeEcriture(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'libelle' => 'required|string|max:500',
            'compte_debit' => 'required|string|exists:plan_comptable,numero',
            'montant_debit' => 'required|numeric|min:0',
            'compte_credit' => 'required|string|exists:plan_comptable,numero',
            'montant_credit' => 'required|numeric|min:0',
            'client_id' => 'nullable|exists:clients,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
        ]);
        
        if ($validated['montant_debit'] != $validated['montant_credit']) {
            return back()->with('error', 'Le montant débit doit être égal au montant crédit')->withInput();
        }
        
        DB::beginTransaction();
        try {
            // Générer un numéro de pièce unique
            $numeroPiece = $this->genererNumeroPiece();
            
            $ecriture = Ecriture::create([
                'numero_piece' => $numeroPiece,
                'date' => $validated['date'],
                'libelle' => $validated['libelle'],
                'compte_debit' => $validated['compte_debit'],
                'montant_debit' => $validated['montant_debit'],
                'compte_credit' => $validated['compte_credit'],
                'montant_credit' => $validated['montant_credit'],
                'client_id' => $validated['client_id'] ?? null,
                'fournisseur_id' => $validated['fournisseur_id'] ?? null,
                'saisi_par' => auth()->id(),
                'statut' => 'brouillon',
            ]);
            
            $this->updateJournal($ecriture);
            $this->updateGrandLivre();
            $this->updateBalance();
            $this->updateBilanEtResultat();
            
            DB::commit();
            $this->logAction('Création écriture', 'ecritures', $ecriture->id, "Écriture {$numeroPiece} créée");
            
            return redirect()->to('/comptable/ecritures')->with('success', 'Écriture créée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function editEcriture($id)
    {
        $ecriture = Ecriture::findOrFail($id);
        $clients = Client::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $comptes = PlanComptable::orderBy('numero')->get();
        
        return view('comptable.ecritures.edit', compact('ecriture', 'clients', 'fournisseurs', 'comptes'));
    }
    
    public function updateEcriture(Request $request, $id)
    {
        $ecriture = Ecriture::findOrFail($id);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'libelle' => 'required|string|max:500',
            'compte_debit' => 'required|string|exists:plan_comptable,numero',
            'montant_debit' => 'required|numeric|min:0',
            'compte_credit' => 'required|string|exists:plan_comptable,numero',
            'montant_credit' => 'required|numeric|min:0',
            'client_id' => 'nullable|exists:clients,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
        ]);
        
        if ($validated['montant_debit'] != $validated['montant_credit']) {
            return back()->with('error', 'Le montant débit doit être égal au montant crédit')->withInput();
        }
        
        DB::beginTransaction();
        try {
            Journal::where('ecriture_id', $ecriture->id)->delete();
            $ecriture->update($validated);
            $this->updateJournal($ecriture);
            $this->updateGrandLivre();
            $this->updateBalance();
            $this->updateBilanEtResultat();
            
            DB::commit();
            $this->logAction('Modification écriture', 'ecritures', $ecriture->id, "Écriture {$ecriture->numero_piece} modifiée");
            
            return redirect()->to('/comptable/ecritures')->with('success', 'Écriture modifiée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    // ==================== SUPPRESSION ÉCRITURE ====================
    public function destroyEcriture($id)
    {
        $ecriture = Ecriture::findOrFail($id);
        
        DB::beginTransaction();
        try {
            Journal::where('ecriture_id', $ecriture->id)->delete();
            $ecriture->delete();
            $this->updateGrandLivre();
            $this->updateBalance();
            $this->updateBilanEtResultat();
            
            DB::commit();
            
            $this->logAction('Suppression écriture', 'ecritures', $ecriture->id, "Écriture {$ecriture->numero_piece} supprimée");
            
            return redirect()->to('/comptable/ecritures')->with('success', 'Écriture supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function showEcriture($id)
    {
        $ecriture = Ecriture::with(['saisiPar', 'validePar'])->findOrFail($id);
        $compteDebit = PlanComptable::where('numero', $ecriture->compte_debit)->first();
        $compteCredit = PlanComptable::where('numero', $ecriture->compte_credit)->first();
        $ecriture->compte_debit_nom = $compteDebit->nom ?? null;
        $ecriture->compte_credit_nom = $compteCredit->nom ?? null;
        
        return view('comptable.ecritures.show', compact('ecriture'));
    }
    
    public function validerEcriture($id)
    {
        $ecriture = Ecriture::findOrFail($id);
        if ($ecriture->statut === 'valide') {
            return back()->with('error', 'Cette écriture est déjà validée');
        }
        
        $ecriture->update([
            'statut' => 'valide',
            'valide_par' => auth()->id(),
            'valide_le' => now(),
        ]);
        
        $this->logAction('Validation écriture', 'ecritures', $ecriture->id, "Écriture {$ecriture->numero_piece} validée");
        
        return redirect()->to('/comptable/ecritures')->with('success', 'Écriture validée avec succès');
    }
    
    // ==================== CONFIRMATION DE SUPPRESSION ====================
    public function confirmDeleteEcriture($id)
    {
        $ecriture = Ecriture::with(['compteDebiteur', 'compteCrediteur'])->findOrFail($id);
        
        return view('comptable.ecritures.confirm-delete', compact('ecriture'));
    }
    
    // ==================== JOURNAL ====================
    public function journal(Request $request)
    {
        $query = Journal::with('ecriture');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        if ($request->filled('compte')) {
            $query->where('compte', $request->compte);
        }
        
        $journal = $query->orderBy('date', 'desc')->paginate(50);
        $total_debit = $journal->sum('debit');
        $total_credit = $journal->sum('credit');
        $comptes = PlanComptable::orderBy('numero')->get();
        
        return view('comptable.journal.index', compact('journal', 'total_debit', 'total_credit', 'comptes'));
    }
    
    // ==================== GRAND LIVRE (AUTOMATIQUE) ====================
    public function grandLivre(Request $request)
    {
        $query = Ecriture::where('statut', 'valide');
        
        if ($request->filled('compte')) {
            $query->where(function($q) use ($request) {
                $q->where('compte_debit', $request->compte)
                  ->orWhere('compte_credit', $request->compte);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $ecritures = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        
        $totalDebit = $ecritures->sum('montant_debit');
        $totalCredit = $ecritures->sum('montant_credit');
        
        $comptesListe = PlanComptable::pluck('nom', 'numero')->toArray();
        
        $ecrituresParCompte = [];
        
        foreach ($ecritures as $ecriture) {
            if (!isset($ecrituresParCompte[$ecriture->compte_debit])) {
                $ecrituresParCompte[$ecriture->compte_debit] = [
                    'compte' => $ecriture->compte_debit,
                    'nom_compte' => $comptesListe[$ecriture->compte_debit] ?? 'Compte inconnu',
                    'lignes' => [],
                ];
            }
            
            if (!isset($ecrituresParCompte[$ecriture->compte_credit])) {
                $ecrituresParCompte[$ecriture->compte_credit] = [
                    'compte' => $ecriture->compte_credit,
                    'nom_compte' => $comptesListe[$ecriture->compte_credit] ?? 'Compte inconnu',
                    'lignes' => [],
                ];
            }
        }
        
        foreach ($ecritures as $ecriture) {
            $ecrituresParCompte[$ecriture->compte_debit]['lignes'][] = [
                'date' => $ecriture->date,
                'libelle' => $ecriture->libelle,
                'numero_piece' => $ecriture->numero_piece,
                'debit' => $ecriture->montant_debit,
                'credit' => 0,
                'compte_correspondant' => $ecriture->compte_credit
            ];
            
            $ecrituresParCompte[$ecriture->compte_credit]['lignes'][] = [
                'date' => $ecriture->date,
                'libelle' => $ecriture->libelle,
                'numero_piece' => $ecriture->numero_piece,
                'debit' => 0,
                'credit' => $ecriture->montant_credit,
                'compte_correspondant' => $ecriture->compte_debit
            ];
        }
        
        foreach ($ecrituresParCompte as &$data) {
            usort($data['lignes'], function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            
            $solde = 0;
            foreach ($data['lignes'] as &$ligne) {
                $solde += $ligne['debit'] - $ligne['credit'];
                $ligne['solde'] = $solde;
                $ligne['nature_solde'] = $solde > 0 ? 'Débiteur' : ($solde < 0 ? 'Créditeur' : 'Équilibré');
                $ligne['solde_abs'] = abs($solde);
            }
            $data['solde_final'] = $solde;
            $data['nature_final'] = $solde > 0 ? 'Débiteur' : ($solde < 0 ? 'Créditeur' : 'Équilibré');
        }
        
        $comptesAvecEcritures = collect($ecrituresParCompte)->keys()->map(function($compte) use ($comptesListe) {
            return (object)[
                'numero' => $compte,
                'nom' => $comptesListe[$compte] ?? 'Compte inconnu'
            ];
        })->sortBy('numero');
        
        $comptes = $comptesAvecEcritures;
        
        return view('comptable.grand-livre.index', compact('ecrituresParCompte', 'totalDebit', 'totalCredit', 'comptes'));
    }
    
    // ==================== BALANCE (AUTOMATIQUE) ====================
    public function balance()
    {
        $ecritures = Ecriture::where('statut', 'valide')->get();
        
        $comptesAvecMouvements = [];
        
        foreach ($ecritures as $ecriture) {
            if (!in_array($ecriture->compte_debit, $comptesAvecMouvements)) {
                $comptesAvecMouvements[] = $ecriture->compte_debit;
            }
            if (!in_array($ecriture->compte_credit, $comptesAvecMouvements)) {
                $comptesAvecMouvements[] = $ecriture->compte_credit;
            }
        }
        
        // Ajouter aussi les comptes du plan comptable qui ont des soldes initiaux
        $tousComptes = PlanComptable::where('solde_initial', '!=', 0)->pluck('numero')->toArray();
        foreach ($tousComptes as $compte) {
            if (!in_array($compte, $comptesAvecMouvements)) {
                $comptesAvecMouvements[] = $compte;
            }
        }
        
        $balance = [];
        $totalSoldeDebitOuverture = 0;
        $totalSoldeCreditOuverture = 0;
        $totalMouvementDebit = 0;
        $totalMouvementCredit = 0;
        $totalSoldeDebitCloture = 0;
        $totalSoldeCreditCloture = 0;
        
        foreach ($comptesAvecMouvements as $compteNum) {
            $planCompte = PlanComptable::where('numero', $compteNum)->first();
            
            if (!$planCompte) continue;
            
            $soldeOuverture = $planCompte->solde_initial ?? 0;
            
            // ==================== SOLDE D'OUVERTURE ====================
            $soldeDebitOuverture = 0;
            $soldeCreditOuverture = 0;
            
            if ($soldeOuverture > 0) {
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeDebitOuverture = $soldeOuverture;
                } else {
                    $soldeCreditOuverture = $soldeOuverture;
                }
            } elseif ($soldeOuverture < 0) {
                $soldeAbsolu = abs($soldeOuverture);
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeCreditOuverture = $soldeAbsolu;
                } else {
                    $soldeDebitOuverture = $soldeAbsolu;
                }
            }
            
            // ==================== MOUVEMENTS ====================
            $totalDebit = Ecriture::where('statut', 'valide')
                ->where('compte_debit', $compteNum)
                ->sum('montant_debit');
            
            $totalCredit = Ecriture::where('statut', 'valide')
                ->where('compte_credit', $compteNum)
                ->sum('montant_credit');
            
            // ==================== SOLDE DE CLÔTURE CORRIGÉ ====================
            // Pour les comptes Actif/Charge : Solde = Solde initial + Débit - Crédit
            // Pour les comptes Passif/Produit : Solde = Solde initial - Débit + Crédit
            if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                $soldeReel = $soldeOuverture + $totalDebit - $totalCredit;
            } else {
                // Passif ou Produit : la logique est inversée
                $soldeReel = $soldeOuverture - $totalDebit + $totalCredit;
            }
            
            $soldeDebitCloture = 0;
            $soldeCreditCloture = 0;
            
            // Placement du solde dans la bonne colonne
            if ($soldeReel > 0) {
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeDebitCloture = $soldeReel;
                } else {
                    $soldeCreditCloture = $soldeReel;
                }
            } elseif ($soldeReel < 0) {
                $soldeAbsolu = abs($soldeReel);
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeCreditCloture = $soldeAbsolu;
                } else {
                    $soldeDebitCloture = $soldeAbsolu;
                }
            }
            
            // Accumulation des totaux
            $totalSoldeDebitOuverture += $soldeDebitOuverture;
            $totalSoldeCreditOuverture += $soldeCreditOuverture;
            $totalMouvementDebit += $totalDebit;
            $totalMouvementCredit += $totalCredit;
            $totalSoldeDebitCloture += $soldeDebitCloture;
            $totalSoldeCreditCloture += $soldeCreditCloture;
            
            // N'afficher que les comptes qui ont des mouvements ou soldes non nuls
            if ($totalDebit > 0 || $totalCredit > 0 || $soldeOuverture != 0 || $soldeReel != 0) {
                $balance[] = (object)[
                    'compte' => $compteNum,
                    'nom_compte' => $planCompte->nom ?? 'Compte inconnu',
                    'type' => $planCompte->type ?? 'Actif',
                    'solde_debit_ouverture' => $soldeDebitOuverture,
                    'solde_credit_ouverture' => $soldeCreditOuverture,
                    'mouvement_debit' => $totalDebit,
                    'mouvement_credit' => $totalCredit,
                    'solde_debit_cloture' => $soldeDebitCloture,
                    'solde_credit_cloture' => $soldeCreditCloture,
                ];
            }
        }
        
        // Trier par numéro de compte
        usort($balance, function($a, $b) {
            return strcmp($a->compte, $b->compte);
        });
        
        // Vérifier l'équilibre de la balance
        $equilibre = ($totalSoldeDebitCloture == $totalSoldeCreditCloture);
        
        return view('comptable.balance.index', compact(
            'balance', 
            'totalSoldeDebitOuverture', 
            'totalSoldeCreditOuverture',
            'totalMouvementDebit', 
            'totalMouvementCredit',
            'totalSoldeDebitCloture', 
            'totalSoldeCreditCloture',
            'equilibre'
        ));
    }
    
    // ==================== BILAN (AUTOMATIQUE OHADA) ====================
    public function bilan(Request $request)
    {
        $dateBilan = $request->date ?: now()->toDateString();
        
        $ecritures = Ecriture::where('statut', 'valide')
            ->whereDate('date', '<=', $dateBilan)
            ->get();
        
        $comptes = PlanComptable::all();
        $soldesParCompte = [];
        
        // Initialiser les soldes avec les soldes initiaux
        foreach ($comptes as $compte) {
            $soldesParCompte[$compte->numero] = $compte->solde_initial;
        }
        
        // Appliquer les mouvements des écritures
        foreach ($ecritures as $ecriture) {
            $compteDebit = PlanComptable::where('numero', $ecriture->compte_debit)->first();
            $compteCredit = PlanComptable::where('numero', $ecriture->compte_credit)->first();
            
            if ($compteDebit) {
                if ($compteDebit->type == 'Actif' || $compteDebit->type == 'Charge') {
                    $soldesParCompte[$ecriture->compte_debit] += $ecriture->montant_debit;
                } else {
                    $soldesParCompte[$ecriture->compte_debit] -= $ecriture->montant_debit;
                }
            }
            
            if ($compteCredit) {
                if ($compteCredit->type == 'Actif' || $compteCredit->type == 'Charge') {
                    $soldesParCompte[$ecriture->compte_credit] -= $ecriture->montant_credit;
                } else {
                    $soldesParCompte[$ecriture->compte_credit] += $ecriture->montant_credit;
                }
            }
        }
        
        // ==================== ACTIF ====================
        // Actif immobilisé (Classe 2)
        $actifImmobiliseDetails = [];
        $totalActifImmobilise = 0;
        $comptesClasse2 = PlanComptable::where('classe', '2')->where('type', 'Actif')->get();
        foreach ($comptesClasse2 as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde != 0) {
                $actifImmobiliseDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalActifImmobilise += $solde;
            }
        }
        
        // Actif circulant (Classe 3 Stocks + Classe 4 Créances)
        $actifCirculantDetails = [];
        $totalActifCirculant = 0;
        $comptesActifCirculant = PlanComptable::where(function($q) {
            $q->where('classe', '3')->where('type', 'Actif')
              ->orWhere(function($q2) {
                  $q2->where('classe', '4')->where('type', 'Actif');
              });
        })->get();
        
        foreach ($comptesActifCirculant as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde > 0) {
                $actifCirculantDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalActifCirculant += $solde;
            }
        }
        
        // Trésorerie Actif (Classe 5 Actif)
        $tresorerieActifDetails = [];
        $totalTresorerieActif = 0;
        $comptesTresorerieActif = PlanComptable::where('classe', '5')->where('type', 'Actif')->get();
        foreach ($comptesTresorerieActif as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde > 0) {
                $tresorerieActifDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalTresorerieActif += $solde;
            }
        }
        
        $totalGeneralActif = $totalActifImmobilise + $totalActifCirculant + $totalTresorerieActif;
        
        // ==================== PASSIF ====================
        // Capitaux propres (Classe 1)
        $capitauxProporesDetails = [];
        $totalCapitauxPropores = 0;
        $comptesClasse1 = PlanComptable::where('classe', '1')->where('type', 'Passif')->get();
        foreach ($comptesClasse1 as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde > 0) {
                $capitauxProporesDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalCapitauxPropores += $solde;
            } elseif ($solde < 0) {
                $capitauxProporesDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom . ' (négatif)',
                    'solde' => $solde
                ];
                $totalCapitauxPropores += $solde;
            }
        }
        
        // Dettes circulantes (Classe 4 Passif + Classe 5 Passif découverts)
        $passifCirculantDetails = [];
        $totalPassifCirculant = 0;
        $comptesPassifCirculant = PlanComptable::where(function($q) {
            $q->where('classe', '4')->where('type', 'Passif')
              ->orWhere(function($q2) {
                  $q2->where('classe', '5')->where('type', 'Passif');
              });
        })->get();
        
        foreach ($comptesPassifCirculant as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde > 0) {
                $passifCirculantDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalPassifCirculant += $solde;
            }
        }
        
        $totalGeneralPassif = $totalCapitauxPropores + $totalPassifCirculant;
        $resultatNet = $totalGeneralActif - $totalGeneralPassif;
        
        return view('comptable.bilan.index', compact(
            'dateBilan',
            'actifImmobiliseDetails',
            'totalActifImmobilise',
            'actifCirculantDetails',
            'totalActifCirculant',
            'tresorerieActifDetails',
            'totalTresorerieActif',
            'totalGeneralActif',
            'capitauxProporesDetails',
            'totalCapitauxPropores',
            'passifCirculantDetails',
            'totalPassifCirculant',
            'totalGeneralPassif',
            'resultatNet'
        ));
    }
    
    // ==================== COMPTE DE RÉSULTAT ====================
    public function resultat(Request $request)
    {
        // Dates par défaut
        $dateDebut = $request->date_debut ?: date('Y-m-01');
        $dateFin = $request->date_fin ?: date('Y-m-d');
        
        // Récupérer TOUTES les écritures validées
        $ecritures = Ecriture::where('statut', 'valide')
            ->whereDate('date', '>=', $dateDebut)
            ->whereDate('date', '<=', $dateFin)
            ->get();
        
        // Initialisation des tableaux et totaux
        $chargesExploitationDetails = [];
        $totalChargesExploitation = 0;
        $chargesFinancieresDetails = [];
        $totalChargesFinancieres = 0;
        $chargesExceptionnellesDetails = [];
        $totalChargesExceptionnelles = 0;
        
        $produitsExploitationDetails = [];
        $totalProduitsExploitation = 0;
        $produitsFinanciersDetails = [];
        $totalProduitsFinancieres = 0;
        $produitsExceptionnelsDetails = [];
        $totalProduitsExceptionnels = 0;
        
        // Pour chaque écriture, ajouter les montants aux comptes correspondants
        foreach ($ecritures as $ecriture) {
            // Traitement du débit (charges)
            $compteDebit = PlanComptable::where('numero', $ecriture->compte_debit)->first();
            if ($compteDebit && $compteDebit->classe == '6') {
                $montant = $ecriture->montant_debit;
                $numero = $compteDebit->numero;
                $nom = $compteDebit->nom;
                
                // Classer selon le début du numéro de compte
                if (preg_match('/^6[0-5]/', $numero)) {
                    $chargesExploitationDetails[] = (object)['compte' => $numero, 'nom_compte' => $nom, 'montant' => $montant];
                    $totalChargesExploitation += $montant;
                } elseif (preg_match('/^67/', $numero)) {
                    $chargesFinancieresDetails[] = (object)['compte' => $numero, 'nom_compte' => $nom, 'montant' => $montant];
                    $totalChargesFinancieres += $montant;
                } elseif (preg_match('/^6[8-9]/', $numero)) {
                    $chargesExceptionnellesDetails[] = (object)['compte' => $numero, 'nom_compte' => $nom, 'montant' => $montant];
                    $totalChargesExceptionnelles += $montant;
                }
            }
            
            // Traitement du crédit (produits)
            $compteCredit = PlanComptable::where('numero', $ecriture->compte_credit)->first();
            if ($compteCredit && $compteCredit->classe == '7') {
                $montant = $ecriture->montant_credit;
                $numero = $compteCredit->numero;
                $nom = $compteCredit->nom;
                
                if (preg_match('/^7[0-5]/', $numero)) {
                    $produitsExploitationDetails[] = (object)['compte' => $numero, 'nom_compte' => $nom, 'montant' => $montant];
                    $totalProduitsExploitation += $montant;
                } elseif (preg_match('/^77/', $numero)) {
                    $produitsFinanciersDetails[] = (object)['compte' => $numero, 'nom_compte' => $nom, 'montant' => $montant];
                    $totalProduitsFinancieres += $montant;
                } elseif (preg_match('/^7[8-9]/', $numero)) {
                    $produitsExceptionnelsDetails[] = (object)['compte' => $numero, 'nom_compte' => $nom, 'montant' => $montant];
                    $totalProduitsExceptionnels += $montant;
                }
            }
        }
        
        $totalGeneralCharges = $totalChargesExploitation + $totalChargesFinancieres + $totalChargesExceptionnelles;
        $totalGeneralProduits = $totalProduitsExploitation + $totalProduitsFinancieres + $totalProduitsExceptionnels;
        
        // Résultat net
        $resultatNet = $totalGeneralProduits - $totalGeneralCharges;
        
        return view('comptable.resultat.index', compact(
            'dateDebut', 'dateFin',
            'chargesExploitationDetails', 'totalChargesExploitation',
            'chargesFinancieresDetails', 'totalChargesFinancieres',
            'chargesExceptionnellesDetails', 'totalChargesExceptionnelles',
            'totalGeneralCharges',
            'produitsExploitationDetails', 'totalProduitsExploitation',
            'produitsFinanciersDetails', 'totalProduitsFinancieres',
            'produitsExceptionnelsDetails', 'totalProduitsExceptionnels',
            'totalGeneralProduits',
            'resultatNet'
        ));
    }
    
    // ==================== STATISTIQUES ====================
    public function statistiques()
    {
        $evolution_ecritures = $this->getEvolutionEcritures();
        $evolution_journal = $this->getEvolutionJournal();
        $top_comptes = $this->getTopComptes();
        $repartition_charges_produits = $this->getRepartitionChargesProduits();
        
        return view('comptable.statistiques', compact('evolution_ecritures', 'evolution_journal', 'top_comptes', 'repartition_charges_produits'));
    }
    
    // ==================== CLIENTS ====================
    public function clients(Request $request)
    {
        $query = Client::query();
        
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        $clients = $query->orderBy('nom')->paginate(30);
        
        return view('comptable.clients.index', compact('clients'));
    }
    
    public function createClient()
    {
        return view('comptable.clients.create');
    }
    
    public function storeClient(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string',
        ]);
        
        $client = Client::create($validated);
        
        $this->logAction('Création client', 'clients', $client->id, "Client {$client->nom} créé");
        
        return redirect()->to('/comptable/clients')->with('success', 'Client ajouté avec succès');
    }
    
    public function editClient($id)
    {
        $client = Client::findOrFail($id);
        return view('comptable.clients.edit', compact('client'));
    }
    
    public function updateClient(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $id,
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string',
        ]);
        
        $client->update($validated);
        
        $this->logAction('Modification client', 'clients', $client->id, "Client {$client->nom} modifié");
        
        return redirect()->to('/comptable/clients')->with('success', 'Client modifié avec succès');
    }
    
    public function destroyClient($id)
    {
        $client = Client::findOrFail($id);
        
        if ($client->factures()->count() > 0) {
            return back()->with('error', 'Ce client a des factures associées. Impossible de le supprimer.');
        }
        
        $this->logAction('Suppression client', 'clients', $client->id, "Client {$client->nom} supprimé");
        $client->delete();
        
        return redirect()->to('/comptable/clients')->with('success', 'Client supprimé avec succès');
    }
    
    public function showClient($id)
    {
        $client = Client::with('factures')->findOrFail($id);
        $total_achats = $client->factures()->sum('montant_ttc');
        $factures = $client->factures()->orderBy('date', 'desc')->paginate(20);
        
        return view('comptable.clients.show', compact('client', 'total_achats', 'factures'));
    }
    
    // ==================== FOURNISSEURS ====================
    public function fournisseurs(Request $request)
    {
        $query = Fournisseur::query();
        
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        $fournisseurs = $query->orderBy('nom')->paginate(30);
        
        return view('comptable.fournisseurs.index', compact('fournisseurs'));
    }
    
    public function createFournisseur()
    {
        return view('comptable.fournisseurs.create');
    }
    
    public function storeFournisseur(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:fournisseurs',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string',
        ]);
        
        $fournisseur = Fournisseur::create($validated);
        
        $this->logAction('Création fournisseur', 'fournisseurs', $fournisseur->id, "Fournisseur {$fournisseur->nom} créé");
        
        return redirect()->to('/comptable/fournisseurs')->with('success', 'Fournisseur ajouté avec succès');
    }
    
    public function editFournisseur($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('comptable.fournisseurs.edit', compact('fournisseur'));
    }
    
    public function updateFournisseur(Request $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:fournisseurs,email,' . $id,
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string',
        ]);
        
        $fournisseur->update($validated);
        
        $this->logAction('Modification fournisseur', 'fournisseurs', $fournisseur->id, "Fournisseur {$fournisseur->nom} modifié");
        
        return redirect()->to('/comptable/fournisseurs')->with('success', 'Fournisseur modifié avec succès');
    }
    
    public function destroyFournisseur($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        if ($fournisseur->ecritures()->count() > 0) {
            return back()->with('error', 'Ce fournisseur a des écritures associées. Impossible de le supprimer.');
        }
        
        $this->logAction('Suppression fournisseur', 'fournisseurs', $fournisseur->id, "Fournisseur {$fournisseur->nom} supprimé");
        $fournisseur->delete();
        
        return redirect()->to('/comptable/fournisseurs')->with('success', 'Fournisseur supprimé avec succès');
    }
    
    public function showFournisseur($id)
    {
        $fournisseur = Fournisseur::with('ecritures')->findOrFail($id);
        $total_achats = $fournisseur->ecritures()->sum('montant_credit');
        
        return view('comptable.fournisseurs.show', compact('fournisseur', 'total_achats'));
    }
    
    // ==================== EXPORTS PDF ====================
    public function exportJournalPDF(Request $request)
    {
        $query = Journal::with('ecriture');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $journal = $query->orderBy('date', 'desc')->get();
        $total_debit = $journal->sum('debit');
        $total_credit = $journal->sum('credit');
        
        $pdf = Pdf::loadView('exports.pdf.journal', compact('journal', 'total_debit', 'total_credit'));
        return $pdf->download('journal_' . date('Y-m-d') . '.pdf');
    }
    
    public function exportGrandLivrePDF(Request $request)
    {
        $query = Ecriture::where('statut', 'valide');
        
        if ($request->filled('compte')) {
            $query->where(function($q) use ($request) {
                $q->where('compte_debit', $request->compte)
                  ->orWhere('compte_credit', $request->compte);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $ecritures = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        
        $totalDebit = $ecritures->sum('montant_debit');
        $totalCredit = $ecritures->sum('montant_credit');
        
        $comptesListe = PlanComptable::pluck('nom', 'numero')->toArray();
        
        $ecrituresParCompte = [];
        
        foreach ($ecritures as $ecriture) {
            if (!isset($ecrituresParCompte[$ecriture->compte_debit])) {
                $ecrituresParCompte[$ecriture->compte_debit] = [
                    'compte' => $ecriture->compte_debit,
                    'nom_compte' => $comptesListe[$ecriture->compte_debit] ?? 'Compte inconnu',
                    'lignes' => [],
                ];
            }
            
            if (!isset($ecrituresParCompte[$ecriture->compte_credit])) {
                $ecrituresParCompte[$ecriture->compte_credit] = [
                    'compte' => $ecriture->compte_credit,
                    'nom_compte' => $comptesListe[$ecriture->compte_credit] ?? 'Compte inconnu',
                    'lignes' => [],
                ];
            }
        }
        
        foreach ($ecritures as $ecriture) {
            $ecrituresParCompte[$ecriture->compte_debit]['lignes'][] = [
                'date' => $ecriture->date,
                'libelle' => $ecriture->libelle,
                'numero_piece' => $ecriture->numero_piece,
                'debit' => $ecriture->montant_debit,
                'credit' => 0,
                'compte_correspondant' => $ecriture->compte_credit
            ];
            
            $ecrituresParCompte[$ecriture->compte_credit]['lignes'][] = [
                'date' => $ecriture->date,
                'libelle' => $ecriture->libelle,
                'numero_piece' => $ecriture->numero_piece,
                'debit' => 0,
                'credit' => $ecriture->montant_credit,
                'compte_correspondant' => $ecriture->compte_debit
            ];
        }
        
        foreach ($ecrituresParCompte as &$data) {
            usort($data['lignes'], function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            
            $solde = 0;
            foreach ($data['lignes'] as &$ligne) {
                $solde += $ligne['debit'] - $ligne['credit'];
                $ligne['solde'] = $solde;
                $ligne['nature_solde'] = $solde > 0 ? 'Débiteur' : ($solde < 0 ? 'Créditeur' : 'Équilibré');
                $ligne['solde_abs'] = abs($solde);
            }
            $data['solde_final'] = $solde;
            $data['nature_final'] = $solde > 0 ? 'Débiteur' : ($solde < 0 ? 'Créditeur' : 'Équilibré');
        }
        
        $pdf = Pdf::loadView('exports.pdf.grand-livre', compact('ecrituresParCompte', 'totalDebit', 'totalCredit'));
        return $pdf->download('grand_livre_' . date('Y-m-d') . '.pdf');
    }
    
    public function exportBalancePDF()
    {
        $ecritures = Ecriture::where('statut', 'valide')->get();
        
        $comptesAvecMouvements = [];
        
        foreach ($ecritures as $ecriture) {
            if (!in_array($ecriture->compte_debit, $comptesAvecMouvements)) {
                $comptesAvecMouvements[] = $ecriture->compte_debit;
            }
            if (!in_array($ecriture->compte_credit, $comptesAvecMouvements)) {
                $comptesAvecMouvements[] = $ecriture->compte_credit;
            }
        }
        
        $balance = [];
        $totalSoldeDebitOuverture = 0;
        $totalSoldeCreditOuverture = 0;
        $totalMouvementDebit = 0;
        $totalMouvementCredit = 0;
        $totalSoldeDebitCloture = 0;
        $totalSoldeCreditCloture = 0;
        
        foreach ($comptesAvecMouvements as $compteNum) {
            $planCompte = PlanComptable::where('numero', $compteNum)->first();
            
            $soldeOuverture = $planCompte->solde_initial ?? 0;
            $soldeDebitOuverture = 0;
            $soldeCreditOuverture = 0;
            
            if ($soldeOuverture > 0) {
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeDebitOuverture = $soldeOuverture;
                } else {
                    $soldeCreditOuverture = $soldeOuverture;
                }
            }
            
            $totalDebit = Ecriture::where('statut', 'valide')
                ->where('compte_debit', $compteNum)
                ->sum('montant_debit');
            
            $totalCredit = Ecriture::where('statut', 'valide')
                ->where('compte_credit', $compteNum)
                ->sum('montant_credit');
            
            $soldeCloture = $soldeOuverture + $totalDebit - $totalCredit;
            $soldeDebitCloture = 0;
            $soldeCreditCloture = 0;
            
            if ($soldeCloture > 0) {
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeDebitCloture = $soldeCloture;
                } else {
                    $soldeCreditCloture = $soldeCloture;
                }
            }
            
            $totalSoldeDebitOuverture += $soldeDebitOuverture;
            $totalSoldeCreditOuverture += $soldeCreditOuverture;
            $totalMouvementDebit += $totalDebit;
            $totalMouvementCredit += $totalCredit;
            $totalSoldeDebitCloture += $soldeDebitCloture;
            $totalSoldeCreditCloture += $soldeCreditCloture;
            
            $balance[] = (object)[
                'compte' => $compteNum,
                'nom_compte' => $planCompte->nom ?? 'Compte inconnu',
                'type' => $planCompte->type ?? 'Actif',
                'solde_debit_ouverture' => $soldeDebitOuverture,
                'solde_credit_ouverture' => $soldeCreditOuverture,
                'mouvement_debit' => $totalDebit,
                'mouvement_credit' => $totalCredit,
                'solde_debit_cloture' => $soldeDebitCloture,
                'solde_credit_cloture' => $soldeCreditCloture,
            ];
        }
        
        usort($balance, function($a, $b) {
            return strcmp($a->compte, $b->compte);
        });
        
        $equilibre = ($totalSoldeDebitCloture == $totalSoldeCreditCloture);
        
        $pdf = Pdf::loadView('exports.pdf.balance', compact(
            'balance', 
            'totalSoldeDebitOuverture', 
            'totalSoldeCreditOuverture',
            'totalMouvementDebit', 
            'totalMouvementCredit',
            'totalSoldeDebitCloture', 
            'totalSoldeCreditCloture',
            'equilibre'
        ));
        
        return $pdf->download('balance_' . date('Y-m-d') . '.pdf');
    }
    
    public function exportBilanPDF(Request $request)
{
    $dateBilan = $request->date ?: now()->toDateString();
    
    $ecritures = Ecriture::where('statut', 'valide')
        ->whereDate('date', '<=', $dateBilan)
        ->get();
    
    $comptes = PlanComptable::all();
    $soldesParCompte = [];
    foreach ($comptes as $compte) {
        $soldesParCompte[$compte->numero] = $compte->solde_initial;
    }
    
    foreach ($ecritures as $ecriture) {
        $soldesParCompte[$ecriture->compte_debit] += $ecriture->montant_debit;
        $soldesParCompte[$ecriture->compte_credit] -= $ecriture->montant_credit;
    }
    
    // ==================== ACTIF ====================
    // Actif immobilisé (Classe 2)
    $actifImmobiliseDetails = [];
    $totalActifImmobilise = 0;
    $comptesClasse2 = PlanComptable::where('classe', '2')->where('type', 'Actif')->get();
    foreach ($comptesClasse2 as $compte) {
        $solde = $soldesParCompte[$compte->numero] ?? 0;
        if ($solde != 0) {
            $actifImmobiliseDetails[] = (object)[
                'compte' => $compte->numero,
                'nom_compte' => $compte->nom,
                'solde' => $solde
            ];
            $totalActifImmobilise += $solde;
        }
    }
    
    // Actif circulant (Classe 3 + Classe 4 Actif)
    $actifCirculantDetails = [];
    $totalStocks = 0;
    $totalCreances = 0;
    $comptesActifCirculant = PlanComptable::where(function($q) {
        $q->where('classe', '3')->where('type', 'Actif')
          ->orWhere(function($q2) {
              $q2->where('classe', '4')->where('type', 'Actif');
          });
    })->get();
    
    foreach ($comptesActifCirculant as $compte) {
        $solde = $soldesParCompte[$compte->numero] ?? 0;
        if ($solde > 0) {
            $actifCirculantDetails[] = (object)[
                'compte' => $compte->numero,
                'nom_compte' => $compte->nom,
                'solde' => $solde
            ];
            if (substr($compte->numero, 0, 1) == '3') {
                $totalStocks += $solde;
            } else {
                $totalCreances += $solde;
            }
        }
    }
    $totalActifCirculant = $totalStocks + $totalCreances;
    
    // Trésorerie Actif (Classe 5 Actif)
    $tresorerieActifDetails = [];
    $totalTresorerieActif = 0;
    $comptesClasse5Actif = PlanComptable::where('classe', '5')->where('type', 'Actif')->get();
    foreach ($comptesClasse5Actif as $compte) {
        $solde = $soldesParCompte[$compte->numero] ?? 0;
        if ($solde > 0) {
            $tresorerieActifDetails[] = (object)[
                'compte' => $compte->numero,
                'nom_compte' => $compte->nom,
                'solde' => $solde
            ];
            $totalTresorerieActif += $solde;
        }
    }
    
    $totalGeneralActif = $totalActifImmobilise + $totalActifCirculant + $totalTresorerieActif;
    
    // ==================== PASSIF ====================
    // Capitaux propres (Classe 1)
    $capitauxProporesDetails = [];
    $totalCapitauxPropores = 0;
    $comptesClasse1 = PlanComptable::where('classe', '1')->where('type', 'Passif')->get();
    foreach ($comptesClasse1 as $compte) {
        $solde = $soldesParCompte[$compte->numero] ?? 0;
        if ($solde != 0) {
            $capitauxProporesDetails[] = (object)[
                'compte' => $compte->numero,
                'nom_compte' => $compte->nom,
                'solde' => $solde
            ];
            $totalCapitauxPropores += $solde;
        }
    }
    
    // Passif circulant (Classe 4 Passif + Classe 5 Passif découverts)
    $passifCirculantDetails = [];
    $totalPassifCirculant = 0;
    $comptesPassifCirculant = PlanComptable::where(function($q) {
        $q->where('classe', '4')->where('type', 'Passif')
          ->orWhere(function($q2) {
              $q2->where('classe', '5')->where('type', 'Passif');
          });
    })->get();
    
    foreach ($comptesPassifCirculant as $compte) {
        $solde = $soldesParCompte[$compte->numero] ?? 0;
        if ($solde > 0) {
            $passifCirculantDetails[] = (object)[
                'compte' => $compte->numero,
                'nom_compte' => $compte->nom,
                'solde' => $solde
            ];
            $totalPassifCirculant += $solde;
        }
    }
    
    // Trésorerie Passif (découverts séparés)
    $tresoreriePassifDetails = [];
    $totalTresoreriePassif = 0;
    $comptesTresoreriePassif = PlanComptable::where('classe', '5')->where('type', 'Passif')->get();
    foreach ($comptesTresoreriePassif as $compte) {
        $solde = $soldesParCompte[$compte->numero] ?? 0;
        if ($solde > 0) {
            $tresoreriePassifDetails[] = (object)[
                'compte' => $compte->numero,
                'nom_compte' => $compte->nom,
                'solde' => $solde
            ];
            $totalTresoreriePassif += $solde;
        }
    }
    
    $totalGeneralPassif = $totalCapitauxPropores + $totalPassifCirculant;
    $resultatNet = $totalGeneralActif - $totalGeneralPassif;
    
    $pdf = Pdf::loadView('exports.pdf.bilan', compact(
        'dateBilan',
        'actifImmobiliseDetails',
        'totalActifImmobilise',
        'actifCirculantDetails',
        'totalActifCirculant',
        'totalStocks',
        'totalCreances',
        'tresorerieActifDetails',
        'totalTresorerieActif',
        'totalGeneralActif',
        'capitauxProporesDetails',
        'totalCapitauxPropores',
        'passifCirculantDetails',
        'totalPassifCirculant',
        'tresoreriePassifDetails',
        'totalTresoreriePassif',
        'totalGeneralPassif',
        'resultatNet'
    ));
    
    return $pdf->download('bilan_' . date('Y-m-d') . '.pdf');
}
    
    public function exportResultatPDF(Request $request)
    {
        $dateDebut = $request->date_debut ?: date('Y-m-01');
        $dateFin = $request->date_fin ?: date('Y-m-d');
        
        $ecritures = Ecriture::where('statut', 'valide')
            ->whereDate('date', '>=', $dateDebut)
            ->whereDate('date', '<=', $dateFin)
            ->get();
        
        // CHARGES
        $chargesExploitationDetails = [];
        $totalChargesExploitation = 0;
        $comptesChargesExploitation = PlanComptable::where('classe', '6')
            ->where(function($q) {
                $q->where('numero', 'like', '60%')
                  ->orWhere('numero', 'like', '61%')
                  ->orWhere('numero', 'like', '62%')
                  ->orWhere('numero', 'like', '63%')
                  ->orWhere('numero', 'like', '64%')
                  ->orWhere('numero', 'like', '65%');
            })->get();
        
        foreach ($comptesChargesExploitation as $compte) {
            $montant = $ecritures->where('compte_debit', $compte->numero)->sum('montant_debit');
            if ($montant > 0) {
                $chargesExploitationDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalChargesExploitation += $montant;
            }
        }
        
        $chargesFinancieresDetails = [];
        $totalChargesFinancieres = 0;
        $comptesChargesFinancieres = PlanComptable::where('classe', '6')->where('numero', 'like', '67%')->get();
        foreach ($comptesChargesFinancieres as $compte) {
            $montant = $ecritures->where('compte_debit', $compte->numero)->sum('montant_debit');
            if ($montant > 0) {
                $chargesFinancieresDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalChargesFinancieres += $montant;
            }
        }
        
        $chargesExceptionnellesDetails = [];
        $totalChargesExceptionnelles = 0;
        $comptesChargesExceptionnelles = PlanComptable::where('classe', '6')
            ->where(function($q) {
                $q->where('numero', 'like', '68%')->orWhere('numero', 'like', '69%');
            })->get();
        foreach ($comptesChargesExceptionnelles as $compte) {
            $montant = $ecritures->where('compte_debit', $compte->numero)->sum('montant_debit');
            if ($montant > 0) {
                $chargesExceptionnellesDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalChargesExceptionnelles += $montant;
            }
        }
        
        $totalGeneralCharges = $totalChargesExploitation + $totalChargesFinancieres + $totalChargesExceptionnelles;
        
        // PRODUITS
        $produitsExploitationDetails = [];
        $totalProduitsExploitation = 0;
        $comptesProduitsExploitation = PlanComptable::where('classe', '7')
            ->where(function($q) {
                $q->where('numero', 'like', '70%')
                  ->orWhere('numero', 'like', '71%')
                  ->orWhere('numero', 'like', '72%')
                  ->orWhere('numero', 'like', '73%')
                  ->orWhere('numero', 'like', '74%')
                  ->orWhere('numero', 'like', '75%');
            })->get();
        foreach ($comptesProduitsExploitation as $compte) {
            $montant = $ecritures->where('compte_credit', $compte->numero)->sum('montant_credit');
            if ($montant > 0) {
                $produitsExploitationDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalProduitsExploitation += $montant;
            }
        }
        
        $produitsFinanciersDetails = [];
        $totalProduitsFinancieres = 0;
        $comptesProduitsFinanciers = PlanComptable::where('classe', '7')->where('numero', 'like', '77%')->get();
        foreach ($comptesProduitsFinanciers as $compte) {
            $montant = $ecritures->where('compte_credit', $compte->numero)->sum('montant_credit');
            if ($montant > 0) {
                $produitsFinanciersDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalProduitsFinancieres += $montant;
            }
        }
        
        $produitsExceptionnelsDetails = [];
        $totalProduitsExceptionnels = 0;
        $comptesProduitsExceptionnels = PlanComptable::where('classe', '7')
            ->where(function($q) {
                $q->where('numero', 'like', '78%')->orWhere('numero', 'like', '79%');
            })->get();
        foreach ($comptesProduitsExceptionnels as $compte) {
            $montant = $ecritures->where('compte_credit', $compte->numero)->sum('montant_credit');
            if ($montant > 0) {
                $produitsExceptionnelsDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalProduitsExceptionnels += $montant;
            }
        }
        
        $totalGeneralProduits = $totalProduitsExploitation + $totalProduitsFinancieres + $totalProduitsExceptionnels;
        $resultatNet = $totalGeneralProduits - $totalGeneralCharges;
        
        $pdf = Pdf::loadView('exports.pdf.resultat', compact(
            'dateDebut', 'dateFin',
            'chargesExploitationDetails', 'totalChargesExploitation',
            'chargesFinancieresDetails', 'totalChargesFinancieres',
            'chargesExceptionnellesDetails', 'totalChargesExceptionnelles',
            'totalGeneralCharges',
            'produitsExploitationDetails', 'totalProduitsExploitation',
            'produitsFinanciersDetails', 'totalProduitsFinancieres',
            'produitsExceptionnelsDetails', 'totalProduitsExceptionnels',
            'totalGeneralProduits',
            'resultatNet'
        ));
        
        return $pdf->download('compte_resultat_' . date('Y-m-d') . '.pdf');
    }
    
    // ==================== EXPORTS EXCEL ====================
    public function exportJournalExcel(Request $request)
    {
        $query = Journal::with('ecriture');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $journal = $query->orderBy('date', 'desc')->get();
        $total_debit = $journal->sum('debit');
        $total_credit = $journal->sum('credit');
        
        return Excel::download(new JournalExport($journal, $total_debit, $total_credit), 'journal_' . date('Y-m-d') . '.xlsx');
    }
    
    public function exportGrandLivreExcel(Request $request)
    {
        $query = Ecriture::where('statut', 'valide');
        
        if ($request->filled('compte')) {
            $query->where(function($q) use ($request) {
                $q->where('compte_debit', $request->compte)
                  ->orWhere('compte_credit', $request->compte);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $ecritures = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        
        $totalDebit = $ecritures->sum('montant_debit');
        $totalCredit = $ecritures->sum('montant_credit');
        
        $comptesListe = PlanComptable::pluck('nom', 'numero')->toArray();
        
        $ecrituresParCompte = [];
        
        foreach ($ecritures as $ecriture) {
            if (!isset($ecrituresParCompte[$ecriture->compte_debit])) {
                $ecrituresParCompte[$ecriture->compte_debit] = [
                    'compte' => $ecriture->compte_debit,
                    'nom_compte' => $comptesListe[$ecriture->compte_debit] ?? 'Compte inconnu',
                    'lignes' => [],
                ];
            }
            
            if (!isset($ecrituresParCompte[$ecriture->compte_credit])) {
                $ecrituresParCompte[$ecriture->compte_credit] = [
                    'compte' => $ecriture->compte_credit,
                    'nom_compte' => $comptesListe[$ecriture->compte_credit] ?? 'Compte inconnu',
                    'lignes' => [],
                ];
            }
        }
        
        foreach ($ecritures as $ecriture) {
            $ecrituresParCompte[$ecriture->compte_debit]['lignes'][] = [
                'date' => $ecriture->date,
                'libelle' => $ecriture->libelle,
                'numero_piece' => $ecriture->numero_piece,
                'debit' => $ecriture->montant_debit,
                'credit' => 0,
                'compte_correspondant' => $ecriture->compte_credit
            ];
            
            $ecrituresParCompte[$ecriture->compte_credit]['lignes'][] = [
                'date' => $ecriture->date,
                'libelle' => $ecriture->libelle,
                'numero_piece' => $ecriture->numero_piece,
                'debit' => 0,
                'credit' => $ecriture->montant_credit,
                'compte_correspondant' => $ecriture->compte_debit
            ];
        }
        
        foreach ($ecrituresParCompte as &$data) {
            usort($data['lignes'], function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
            
            $solde = 0;
            foreach ($data['lignes'] as &$ligne) {
                $solde += $ligne['debit'] - $ligne['credit'];
                $ligne['solde'] = $solde;
            }
            $data['solde_final'] = $solde;
        }
        
        return Excel::download(new GrandLivreExport($ecrituresParCompte, $totalDebit, $totalCredit), 'grand_livre_' . date('Y-m-d') . '.xlsx');
    }
    
    public function exportBalanceExcel()
    {
        $ecritures = Ecriture::where('statut', 'valide')->get();
        
        $comptesAvecMouvements = [];
        
        foreach ($ecritures as $ecriture) {
            if (!in_array($ecriture->compte_debit, $comptesAvecMouvements)) {
                $comptesAvecMouvements[] = $ecriture->compte_debit;
            }
            if (!in_array($ecriture->compte_credit, $comptesAvecMouvements)) {
                $comptesAvecMouvements[] = $ecriture->compte_credit;
            }
        }
        
        $balance = [];
        $totalSoldeDebitOuverture = 0;
        $totalSoldeCreditOuverture = 0;
        $totalMouvementDebit = 0;
        $totalMouvementCredit = 0;
        $totalSoldeDebitCloture = 0;
        $totalSoldeCreditCloture = 0;
        
        foreach ($comptesAvecMouvements as $compteNum) {
            $planCompte = PlanComptable::where('numero', $compteNum)->first();
            
            $soldeOuverture = $planCompte->solde_initial ?? 0;
            $soldeDebitOuverture = 0;
            $soldeCreditOuverture = 0;
            
            if ($soldeOuverture > 0) {
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeDebitOuverture = $soldeOuverture;
                } else {
                    $soldeCreditOuverture = $soldeOuverture;
                }
            }
            
            $totalDebit = Ecriture::where('statut', 'valide')
                ->where('compte_debit', $compteNum)
                ->sum('montant_debit');
            
            $totalCredit = Ecriture::where('statut', 'valide')
                ->where('compte_credit', $compteNum)
                ->sum('montant_credit');
            
            $soldeCloture = $soldeOuverture + $totalDebit - $totalCredit;
            $soldeDebitCloture = 0;
            $soldeCreditCloture = 0;
            
            if ($soldeCloture > 0) {
                if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                    $soldeDebitCloture = $soldeCloture;
                } else {
                    $soldeCreditCloture = $soldeCloture;
                }
            }
            
            $totalSoldeDebitOuverture += $soldeDebitOuverture;
            $totalSoldeCreditOuverture += $soldeCreditOuverture;
            $totalMouvementDebit += $totalDebit;
            $totalMouvementCredit += $totalCredit;
            $totalSoldeDebitCloture += $soldeDebitCloture;
            $totalSoldeCreditCloture += $soldeCreditCloture;
            
            $balance[] = (object)[
                'compte' => $compteNum,
                'nom_compte' => $planCompte->nom ?? 'Compte inconnu',
                'solde_debit_ouverture' => $soldeDebitOuverture,
                'solde_credit_ouverture' => $soldeCreditOuverture,
                'mouvement_debit' => $totalDebit,
                'mouvement_credit' => $totalCredit,
                'solde_debit_cloture' => $soldeDebitCloture,
                'solde_credit_cloture' => $soldeCreditCloture,
            ];
        }
        
        usort($balance, function($a, $b) {
            return strcmp($a->compte, $b->compte);
        });
        
        return Excel::download(new BalanceExport($balance, $totalSoldeDebitOuverture, $totalSoldeCreditOuverture, $totalMouvementDebit, $totalMouvementCredit, $totalSoldeDebitCloture, $totalSoldeCreditCloture), 'balance_' . date('Y-m-d') . '.xlsx');
    }
    
    public function exportBilanExcel(Request $request)
    {
        $dateBilan = $request->date ?: now()->toDateString();
        
        $ecritures = Ecriture::where('statut', 'valide')
            ->whereDate('date', '<=', $dateBilan)
            ->get();
        
        $comptes = PlanComptable::all();
        $soldesParCompte = [];
        foreach ($comptes as $compte) {
            $soldesParCompte[$compte->numero] = $compte->solde_initial;
        }
        
        foreach ($ecritures as $ecriture) {
            $soldesParCompte[$ecriture->compte_debit] += $ecriture->montant_debit;
            $soldesParCompte[$ecriture->compte_credit] -= $ecriture->montant_credit;
        }
        
        // ACTIF
        $actifImmobiliseDetails = [];
        $totalActifImmobilise = 0;
        $comptesClasse2 = PlanComptable::where('classe', '2')->where('type', 'Actif')->get();
        foreach ($comptesClasse2 as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde != 0) {
                $actifImmobiliseDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalActifImmobilise += $solde;
            }
        }
        
        $actifCirculantDetails = [];
        $totalActifCirculant = 0;
        $comptesActifCirculant = PlanComptable::where(function($q) {
            $q->where('classe', '3')->where('type', 'Actif')
              ->orWhere(function($q2) {
                  $q2->where('classe', '4')->where('type', 'Actif');
              });
        })->get();
        
        foreach ($comptesActifCirculant as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde > 0) {
                $actifCirculantDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalActifCirculant += $solde;
            }
        }
        
        $tresorerieActifDetails = [];
        $totalTresorerieActif = 0;
        $comptesClasse5Actif = PlanComptable::where('classe', '5')->where('type', 'Actif')->get();
        foreach ($comptesClasse5Actif as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde > 0) {
                $tresorerieActifDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalTresorerieActif += $solde;
            }
        }
        
        $totalGeneralActif = $totalActifImmobilise + $totalActifCirculant + $totalTresorerieActif;
        
        // PASSIF
        $capitauxProporesDetails = [];
        $totalCapitauxPropores = 0;
        $comptesClasse1 = PlanComptable::where('classe', '1')->where('type', 'Passif')->get();
        foreach ($comptesClasse1 as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde != 0) {
                $capitauxProporesDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalCapitauxPropores += $solde;
            }
        }
        
        $passifCirculantDetails = [];
        $totalPassifCirculant = 0;
        $comptesPassifCirculant = PlanComptable::where(function($q) {
            $q->where('classe', '4')->where('type', 'Passif')
              ->orWhere(function($q2) {
                  $q2->where('classe', '5')->where('type', 'Passif');
              });
        })->get();
        
        foreach ($comptesPassifCirculant as $compte) {
            $solde = $soldesParCompte[$compte->numero] ?? 0;
            if ($solde > 0) {
                $passifCirculantDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'solde' => $solde
                ];
                $totalPassifCirculant += $solde;
            }
        }
        
        $totalGeneralPassif = $totalCapitauxPropores + $totalPassifCirculant;
        $resultatNet = $totalGeneralActif - $totalGeneralPassif;
        
        return Excel::download(new BilanExport(
            $dateBilan,
            $actifImmobiliseDetails, $totalActifImmobilise,
            $actifCirculantDetails, $totalActifCirculant,
            $tresorerieActifDetails, $totalTresorerieActif,
            $totalGeneralActif,
            $capitauxProporesDetails, $totalCapitauxPropores,
            $passifCirculantDetails, $totalPassifCirculant,
            $totalGeneralPassif,
            $resultatNet
        ), 'bilan_' . date('Y-m-d') . '.xlsx');
    }
    
    public function exportResultatExcel(Request $request)
    {
        $dateDebut = $request->date_debut ?: date('Y-m-01');
        $dateFin = $request->date_fin ?: date('Y-m-d');
        
        $ecritures = Ecriture::where('statut', 'valide')
            ->whereDate('date', '>=', $dateDebut)
            ->whereDate('date', '<=', $dateFin)
            ->get();
        
        // CHARGES
        $chargesExploitationDetails = [];
        $totalChargesExploitation = 0;
        $comptesChargesExploitation = PlanComptable::where('classe', '6')
            ->where(function($q) {
                $q->where('numero', 'like', '60%')
                  ->orWhere('numero', 'like', '61%')
                  ->orWhere('numero', 'like', '62%')
                  ->orWhere('numero', 'like', '63%')
                  ->orWhere('numero', 'like', '64%')
                  ->orWhere('numero', 'like', '65%');
            })->get();
        
        foreach ($comptesChargesExploitation as $compte) {
            $montant = $ecritures->where('compte_debit', $compte->numero)->sum('montant_debit');
            if ($montant > 0) {
                $chargesExploitationDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalChargesExploitation += $montant;
            }
        }
        
        $chargesFinancieresDetails = [];
        $totalChargesFinancieres = 0;
        $comptesChargesFinancieres = PlanComptable::where('classe', '6')->where('numero', 'like', '67%')->get();
        foreach ($comptesChargesFinancieres as $compte) {
            $montant = $ecritures->where('compte_debit', $compte->numero)->sum('montant_debit');
            if ($montant > 0) {
                $chargesFinancieresDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalChargesFinancieres += $montant;
            }
        }
        
        $chargesExceptionnellesDetails = [];
        $totalChargesExceptionnelles = 0;
        $comptesChargesExceptionnelles = PlanComptable::where('classe', '6')
            ->where(function($q) {
                $q->where('numero', 'like', '68%')->orWhere('numero', 'like', '69%');
            })->get();
        foreach ($comptesChargesExceptionnelles as $compte) {
            $montant = $ecritures->where('compte_debit', $compte->numero)->sum('montant_debit');
            if ($montant > 0) {
                $chargesExceptionnellesDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalChargesExceptionnelles += $montant;
            }
        }
        
        $totalGeneralCharges = $totalChargesExploitation + $totalChargesFinancieres + $totalChargesExceptionnelles;
        
        // PRODUITS
        $produitsExploitationDetails = [];
        $totalProduitsExploitation = 0;
        $comptesProduitsExploitation = PlanComptable::where('classe', '7')
            ->where(function($q) {
                $q->where('numero', 'like', '70%')
                  ->orWhere('numero', 'like', '71%')
                  ->orWhere('numero', 'like', '72%')
                  ->orWhere('numero', 'like', '73%')
                  ->orWhere('numero', 'like', '74%')
                  ->orWhere('numero', 'like', '75%');
            })->get();
        foreach ($comptesProduitsExploitation as $compte) {
            $montant = $ecritures->where('compte_credit', $compte->numero)->sum('montant_credit');
            if ($montant > 0) {
                $produitsExploitationDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalProduitsExploitation += $montant;
            }
        }
        
        $produitsFinanciersDetails = [];
        $totalProduitsFinancieres = 0;
        $comptesProduitsFinanciers = PlanComptable::where('classe', '7')->where('numero', 'like', '77%')->get();
        foreach ($comptesProduitsFinanciers as $compte) {
            $montant = $ecritures->where('compte_credit', $compte->numero)->sum('montant_credit');
            if ($montant > 0) {
                $produitsFinanciersDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalProduitsFinancieres += $montant;
            }
        }
        
        $produitsExceptionnelsDetails = [];
        $totalProduitsExceptionnels = 0;
        $comptesProduitsExceptionnels = PlanComptable::where('classe', '7')
            ->where(function($q) {
                $q->where('numero', 'like', '78%')->orWhere('numero', 'like', '79%');
            })->get();
        foreach ($comptesProduitsExceptionnels as $compte) {
            $montant = $ecritures->where('compte_credit', $compte->numero)->sum('montant_credit');
            if ($montant > 0) {
                $produitsExceptionnelsDetails[] = (object)[
                    'compte' => $compte->numero,
                    'nom_compte' => $compte->nom,
                    'montant' => $montant
                ];
                $totalProduitsExceptionnels += $montant;
            }
        }
        
        $totalGeneralProduits = $totalProduitsExploitation + $totalProduitsFinancieres + $totalProduitsExceptionnels;
        $resultatNet = $totalGeneralProduits - $totalGeneralCharges;
        
        return Excel::download(new ResultatExport(
            $dateDebut, $dateFin,
            $chargesExploitationDetails, $totalChargesExploitation,
            $chargesFinancieresDetails, $totalChargesFinancieres,
            $chargesExceptionnellesDetails, $totalChargesExceptionnelles,
            $totalGeneralCharges,
            $produitsExploitationDetails, $totalProduitsExploitation,
            $produitsFinanciersDetails, $totalProduitsFinancieres,
            $produitsExceptionnelsDetails, $totalProduitsExceptionnels,
            $totalGeneralProduits,
            $resultatNet
        ), 'compte_resultat_' . date('Y-m-d') . '.xlsx');
    }
    
    // ==================== MÉTHODES PRIVÉES ====================
    private function updateJournal($ecriture)
    {
        Journal::create([
            'date' => $ecriture->date,
            'compte' => $ecriture->compte_debit,
            'libelle' => $ecriture->libelle,
            'debit' => $ecriture->montant_debit,
            'credit' => 0,
            'reference' => $ecriture->numero_piece,
            'ecriture_id' => $ecriture->id,
        ]);
        
        Journal::create([
            'date' => $ecriture->date,
            'compte' => $ecriture->compte_credit,
            'libelle' => $ecriture->libelle,
            'debit' => 0,
            'credit' => $ecriture->montant_credit,
            'reference' => $ecriture->numero_piece,
            'ecriture_id' => $ecriture->id,
        ]);
    }
    
    private function updateGrandLivre()
    {
        $comptes = PlanComptable::all();
        
        foreach ($comptes as $compte) {
            $total_debit = Journal::where('compte', $compte->numero)->sum('debit');
            $total_credit = Journal::where('compte', $compte->numero)->sum('credit');
            $solde_final = $compte->solde_initial + $total_debit - $total_credit;
            
            GrandLivre::updateOrCreate(
                ['compte' => $compte->numero, 'date_arrete' => now()->toDateString()],
                [
                    'solde_initial' => $compte->solde_initial,
                    'total_debit' => $total_debit,
                    'total_credit' => $total_credit,
                    'solde_final' => $solde_final,
                ]
            );
        }
    }
    
    private function updateBalance()
    {
        $comptes = PlanComptable::all();
        
        foreach ($comptes as $compte) {
            $total_debit = Journal::where('compte', $compte->numero)->sum('debit');
            $total_credit = Journal::where('compte', $compte->numero)->sum('credit');
            
            if ($total_debit > $total_credit) {
                $solde_debiteur = $total_debit - $total_credit;
                $solde_crediteur = 0;
            } else {
                $solde_debiteur = 0;
                $solde_crediteur = $total_credit - $total_debit;
            }
            
            Balance::updateOrCreate(
                ['compte' => $compte->numero, 'date_balance' => now()->toDateString()],
                [
                    'solde_debiteur' => $solde_debiteur,
                    'solde_crediteur' => $solde_crediteur,
                ]
            );
        }
    }
    
    private function updateBilanEtResultat()
    {
        $actif_immobilise = $this->getSoldeComptes(['2']);
        $actif_circulant = $this->getSoldeComptes(['3']);
        $tresorerie = $this->getSoldeComptes(['5']);
        $total_actif = $actif_immobilise + $actif_circulant + $tresorerie;
        
        $capitaux_propres = $this->getSoldeComptes(['1']);
        $dettes = $this->getSoldeComptes(['4']);
        $total_passif = $capitaux_propres + $dettes;
        
        Bilan::create([
            'date' => now(),
            'actif_immobilise' => $actif_immobilise,
            'actif_circulant' => $actif_circulant,
            'tresorerie' => $tresorerie,
            'total_actif' => $total_actif,
            'capitaux_propres' => $capitaux_propres,
            'dettes' => $dettes,
            'total_passif' => $total_passif,
        ]);
        
        $total_produits = $this->getSoldeComptes(['7']);
        $total_charges = $this->getSoldeComptes(['6']);
        $resultat_net = $total_produits - $total_charges;
        $type = $resultat_net >= 0 ? 'benefice' : 'perte';
        
        Resultat::create([
            'date' => now(),
            'total_produits' => $total_produits,
            'total_charges' => $total_charges,
            'resultat_net' => abs($resultat_net),
            'type' => $type,
        ]);
    }
    
    private function getSoldeComptes($classes)
    {
        $total = 0;
        $comptes = PlanComptable::whereIn('classe', $classes)->pluck('numero');
        
        foreach ($comptes as $compte) {
            $grandLivre = GrandLivre::where('compte', $compte)->latest()->first();
            if ($grandLivre) {
                $total += $grandLivre->solde_final;
            }
        }
        return $total;
    }
    
    private function getEvolutionEcritures()
    {
        $mois = [];
        $counts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            $counts[] = Ecriture::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->count();
        }
        
        return compact('mois', 'counts');
    }
    
    private function getEvolutionJournal()
    {
        $mois = [];
        $debits = [];
        $credits = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            $debits[] = Journal::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('debit');
            $credits[] = Journal::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('credit');
        }
        
        return compact('mois', 'debits', 'credits');
    }
    
    private function getTopComptes()
    {
        return Journal::select('compte', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('compte')
            ->orderByRaw('(SUM(debit) + SUM(credit)) DESC')
            ->limit(10)
            ->get();
    }
    
    private function getRepartitionChargesProduits()
    {
        $charges = Journal::whereIn('compte', function($query) {
            $query->select('numero')->from('plan_comptable')->where('type', 'Charge');
        })->sum('debit');
        
        $produits = Journal::whereIn('compte', function($query) {
            $query->select('numero')->from('plan_comptable')->where('type', 'Produit');
        })->sum('credit');
        
        return compact('charges', 'produits');
    }
    
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