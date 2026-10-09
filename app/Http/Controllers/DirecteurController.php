<?php
namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\GrandLivre;
use App\Models\Balance;
use App\Models\Bilan;
use App\Models\Resultat;
use App\Models\Caisse;
use App\Models\Banque;
use App\Models\Ecriture;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\PlanComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GrandLivreExport;

class DirecteurController extends Controller
{
    /**
     * Afficher le tableau de bord du directeur
     */
    public function dashboard()
    {
        $chiffre_affaires = Journal::where('compte', '701')->sum('credit');
        $total_charges = Journal::whereIn('compte', ['601', '602', '611', '612', '613', '614', '631'])->sum('debit');
        $benefice_net = $chiffre_affaires - $total_charges;
        
        $stats = [
            'chiffre_affaires' => $chiffre_affaires,
            'total_charges' => $total_charges,
            'benefice_net' => $benefice_net,
            'croissance_ca' => $this->getCroissanceCA(),
            'croissance_benefice' => $this->getCroissanceBenefice(),
            'tresorerie' => $this->getTresorerie(),
            'rentabilite' => $this->getRatioRentabilite(),
            'total_ecritures' => Ecriture::count(),
            'clients_actifs' => Client::count(),
            'fournisseurs' => Fournisseur::count(),
            'marge_brute' => $this->getMargeBrute(),
            'bfr' => $this->getBFR(),
            'delai_paiement' => $this->getDelaiPaiementClient(),
            'roe' => $this->getROE(),
            'total_banque' => Banque::sum('montant'),
            'solde_caisse' => Caisse::where('type', 'entree')->sum('montant') - Caisse::where('type', 'sortie')->sum('montant'),
            'nb_ecritures' => Ecriture::count(),
            'nb_clients' => Client::count(),
            'dernier_bilan' => Bilan::latest()->first(),
            'dernier_resultat' => Resultat::latest()->first(),
            'resultat_net' => Resultat::latest()->first()->resultat_net ?? 0,
            'type_resultat' => Resultat::latest()->first()->type ?? 'non_defini',
            'graphiques' => $this->getGraphiquesData(),
            'ca_mois' => $this->getCAMois(),
            'ca_data' => $this->getCAData(),
            'dernieres_ecritures' => Ecriture::with('client')->orderBy('date', 'desc')->limit(5)->get(),
            'top_clients' => $this->getTopClients(),
        ];
        
        return view('directeur.dashboard', $stats);
    }
    
    /**
     * Afficher les statistiques détaillées
     */
    public function statistiques()
    {
        $mois = [];
        $ca_data = [];
        $charges_data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            $ca_data[] = Journal::where('compte', '701')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('credit');
            $charges_data[] = Journal::whereIn('compte', ['601', '602', '611', '612', '613', '614', '631'])
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('debit');
        }
        
        $charges_labels = ['Achats', 'Frais transport', 'Publicité', 'Entretien', 'Assurances', 'Salaires'];
        $charges_values = [
            Journal::where('compte', '601')->sum('debit'),
            Journal::where('compte', '602')->sum('debit'),
            Journal::where('compte', '611')->sum('debit'),
            Journal::where('compte', '612')->sum('debit'),
            Journal::where('compte', '613')->sum('debit'),
            Journal::where('compte', '631')->sum('debit'),
        ];
        
        $top_clients = $this->getTopClients();
        $ratio_rentabilite = $this->getRatioRentabilite();
        $tresorerie = $this->getTresorerie();
        $fdr = $this->getFondsRoulement();
        $rotation_stocks = $this->getRotationStocks();
        
        $stats = [
            'mois' => $mois,
            'ca_data' => $ca_data,
            'charges_data' => $charges_data,
            'charges_labels' => $charges_labels,
            'charges_values' => $charges_values,
            'top_clients' => $top_clients,
            'ratio_rentabilite' => $ratio_rentabilite,
            'tresorerie' => $tresorerie,
            'fdr' => $fdr,
            'rotation_stocks' => $rotation_stocks,
            'evolution_ventes' => $this->getEvolutionVentes(),
            'evolution_charges' => $this->getEvolutionCharges(),
        ];
        
        return view('directeur.statistiques', $stats);
    }
    
    /**
     * Afficher le journal comptable (avec regroupement)
     */
    public function journal(Request $request)
    {
        $query = Journal::with('ecriture');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $journal = $query->orderBy('date', 'desc')->paginate(50);
        $comptes = PlanComptable::orderBy('numero')->get();
        
        $total_debit = $journal->sum('debit');
        $total_credit = $journal->sum('credit');
        
        return view('directeur.journal', compact('journal', 'comptes', 'total_debit', 'total_credit'));
    }
    
    /**
     * Exporter le journal en PDF
     */
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
        $comptes = PlanComptable::all();
        
        $pdf = Pdf::loadView('exports.pdf.journal', compact('journal', 'total_debit', 'total_credit', 'comptes'));
        return $pdf->download('journal_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Exporter le Grand Livre en PDF
     */
    public function exportGrandLivrePDF(Request $request)
{
    $ecritures = Ecriture::where('statut', 'valide');
    
    if ($request->filled('compte')) {
        $ecritures->where(function($q) use ($request) {
            $q->where('compte_debit', $request->compte)
              ->orWhere('compte_credit', $request->compte);
        });
    }
    
    if ($request->filled('date_debut')) {
        $ecritures->whereDate('date', '>=', $request->date_debut);
    }
    if ($request->filled('date_fin')) {
        $ecritures->whereDate('date', '<=', $request->date_fin);
    }
    
    $ecritures = $ecritures->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
    
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
        // Pour le compte débit
        $ecrituresParCompte[$ecriture->compte_debit]['lignes'][] = [
            'date' => $ecriture->date,
            'libelle' => $ecriture->libelle,
            'numero_piece' => $ecriture->numero_piece,
            'debit' => $ecriture->montant_debit,
            'credit' => 0,
            'compte_correspondant' => $ecriture->compte_credit,  // ⚠️ AJOUTER CETTE CLÉ
        ];
        
        // Pour le compte crédit
        $ecrituresParCompte[$ecriture->compte_credit]['lignes'][] = [
            'date' => $ecriture->date,
            'libelle' => $ecriture->libelle,
            'numero_piece' => $ecriture->numero_piece,
            'debit' => 0,
            'credit' => $ecriture->montant_credit,
            'compte_correspondant' => $ecriture->compte_debit,  // ⚠️ AJOUTER CETTE CLÉ
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
        }
        $data['solde_final'] = $solde;
        $data['nature_final'] = $solde > 0 ? 'Débiteur' : ($solde < 0 ? 'Créditeur' : 'Équilibré');
    }
    
    // Récupérer la liste des comptes pour le filtre
    $comptes = PlanComptable::orderBy('numero')->get();
    
    $pdf = Pdf::loadView('exports.pdf.grand-livre', [
        'ecrituresParCompte' => $ecrituresParCompte,
        'totalDebit' => $totalDebit,
        'totalCredit' => $totalCredit,
        'comptes' => $comptes,
    ]);
    
    return $pdf->download('grand_livre_' . date('Y-m-d') . '.pdf');
}
    /**
     * Exporter la balance en PDF
     */
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
        
        $totalDebit = Ecriture::where('statut', 'valide')
            ->where('compte_debit', $compteNum)
            ->sum('montant_debit');
        
        $totalCredit = Ecriture::where('statut', 'valide')
            ->where('compte_credit', $compteNum)
            ->sum('montant_credit');
        
        if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
            $soldeReel = $soldeOuverture + $totalDebit - $totalCredit;
        } else {
            $soldeReel = $soldeOuverture - $totalDebit + $totalCredit;
        }
        
        $soldeDebitCloture = 0;
        $soldeCreditCloture = 0;
        
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
        
        $totalSoldeDebitOuverture += $soldeDebitOuverture;
        $totalSoldeCreditOuverture += $soldeCreditOuverture;
        $totalMouvementDebit += $totalDebit;
        $totalMouvementCredit += $totalCredit;
        $totalSoldeDebitCloture += $soldeDebitCloture;
        $totalSoldeCreditCloture += $soldeCreditCloture;
        
        if ($totalDebit > 0 || $totalCredit > 0 || $soldeOuverture != 0 || $soldeReel != 0) {
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
    }
    
    usort($balance, function($a, $b) {
        return strcmp($a->compte, $b->compte);
    });
    
    // ⚠️ IMPORTANT: Passer TOUTES les variables à la vue
    $pdf = Pdf::loadView('exports.pdf.balance', [
        'balance' => $balance,
        'totalSoldeDebitOuverture' => $totalSoldeDebitOuverture,
        'totalSoldeCreditOuverture' => $totalSoldeCreditOuverture,
        'totalMouvementDebit' => $totalMouvementDebit,
        'totalMouvementCredit' => $totalMouvementCredit,
        'totalSoldeDebitCloture' => $totalSoldeDebitCloture,
        'totalSoldeCreditCloture' => $totalSoldeCreditCloture,
    ]);
    
    return $pdf->download('balance_' . date('Y-m-d') . '.pdf');
}
    /**
     * Exporter le bilan en PDF
     */
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
            'totalGeneralPassif',
            'resultatNet'
        ));
        
        return $pdf->download('bilan_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Exporter le résultat en PDF
     */
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
        $chargesFinancieresDetails = [];
        $totalChargesFinancieres = 0;
        $chargesExceptionnellesDetails = [];
        $totalChargesExceptionnelles = 0;
        
        // PRODUITS
        $produitsExploitationDetails = [];
        $totalProduitsExploitation = 0;
        $produitsFinanciersDetails = [];
        $totalProduitsFinancieres = 0;
        $produitsExceptionnelsDetails = [];
        $totalProduitsExceptionnels = 0;
        
        foreach ($ecritures as $ecriture) {
            $compteDebit = PlanComptable::where('numero', $ecriture->compte_debit)->first();
            if ($compteDebit && $compteDebit->classe == '6') {
                $montant = $ecriture->montant_debit;
                $numero = $compteDebit->numero;
                $nom = $compteDebit->nom;
                
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
        
        return $pdf->download('resultat_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Exporter les statistiques en PDF
     */
    public function exportStatistiquesPDF(Request $request)
    {
        $pdf = Pdf::loadView('exports.pdf.statistiques');
        return $pdf->download('statistiques_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Afficher le Grand Livre
     */
    public function grandLivre(Request $request)
    {
        $ecritures = Ecriture::where('statut', 'valide');
        
        if ($request->filled('compte')) {
            $ecritures->where(function($q) use ($request) {
                $q->where('compte_debit', $request->compte)
                  ->orWhere('compte_credit', $request->compte);
            });
        }
        
        if ($request->filled('date_debut')) {
            $ecritures->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $ecritures->whereDate('date', '<=', $request->date_fin);
        }
        
        $ecritures = $ecritures->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        
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
        
        return view('directeur.grand-livre', compact('ecrituresParCompte', 'totalDebit', 'totalCredit', 'comptes'));
    }
    
    /**
     * Détail d'un compte pour le Grand Livre
     */
    public function grandLivreDetail(Request $request)
    {
        $compte = $request->get('compte');
        
        $details = Journal::where('compte', $compte)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->date->format('d/m/Y'),
                    'libelle' => $item->libelle,
                    'reference' => $item->reference,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            });
        
        return response()->json($details);
    }
    
    /**
     * Afficher la balance de vérification
     */
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
            
            $totalDebit = Ecriture::where('statut', 'valide')
                ->where('compte_debit', $compteNum)
                ->sum('montant_debit');
            
            $totalCredit = Ecriture::where('statut', 'valide')
                ->where('compte_credit', $compteNum)
                ->sum('montant_credit');
            
            if ($planCompte->type == 'Actif' || $planCompte->type == 'Charge') {
                $soldeReel = $soldeOuverture + $totalDebit - $totalCredit;
            } else {
                $soldeReel = $soldeOuverture - $totalDebit + $totalCredit;
            }
            
            $soldeDebitCloture = 0;
            $soldeCreditCloture = 0;
            
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
            
            $totalSoldeDebitOuverture += $soldeDebitOuverture;
            $totalSoldeCreditOuverture += $soldeCreditOuverture;
            $totalMouvementDebit += $totalDebit;
            $totalMouvementCredit += $totalCredit;
            $totalSoldeDebitCloture += $soldeDebitCloture;
            $totalSoldeCreditCloture += $soldeCreditCloture;
            
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
        
        usort($balance, function($a, $b) {
            return strcmp($a->compte, $b->compte);
        });
        
        $equilibre = ($totalSoldeDebitCloture == $totalSoldeCreditCloture);
        
        return view('directeur.balance', compact(
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
    
    /**
     * Afficher le bilan comptable
     */
    public function bilan(Request $request)
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
        
        return view('directeur.bilan', compact(
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
            'totalGeneralPassif',
            'resultatNet'
        ));
    }
    
    /**
     * Afficher le compte de résultat
     */
    public function resultat(Request $request)
    {
        $dateDebut = $request->date_debut ?: date('Y-m-01');
        $dateFin = $request->date_fin ?: date('Y-m-d');
        
        $ecritures = Ecriture::where('statut', 'valide')
            ->whereDate('date', '>=', $dateDebut)
            ->whereDate('date', '<=', $dateFin)
            ->get();
        
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
        
        foreach ($ecritures as $ecriture) {
            $compteDebit = PlanComptable::where('numero', $ecriture->compte_debit)->first();
            if ($compteDebit && $compteDebit->classe == '6') {
                $montant = $ecriture->montant_debit;
                $numero = $compteDebit->numero;
                $nom = $compteDebit->nom;
                
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
        $resultatNet = $totalGeneralProduits - $totalGeneralCharges;
        
        return view('directeur.resultat', compact(
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
    
    /**
     * Afficher la situation bancaire
     */
    public function banque()
    {
        $operations = Banque::with('enregistrePar')->orderBy('date', 'desc')->paginate(50);
        $solde = Banque::sum('montant');
        $depots = Banque::where('type', 'depot')->sum('montant');
        $retraits = Banque::where('type', 'retrait')->sum('montant');
        
        return view('directeur.banque', compact('operations', 'solde', 'depots', 'retraits'));
    }
    
    /**
     * Afficher la situation de caisse
     */
    public function caisse()
    {
        $operations = Caisse::with('enregistrePar')->orderBy('date', 'desc')->paginate(50);
        $entrees = Caisse::where('type', 'entree')->sum('montant');
        $sorties = Caisse::where('type', 'sortie')->sum('montant');
        $solde = $entrees - $sorties;
        
        return view('directeur.caisse', compact('operations', 'solde', 'entrees', 'sorties'));
    }
    
    // ==================== MÉTHODES PRIVÉES ====================
    
    private function getCroissanceCA()
    {
        $caMoisPrecedent = Journal::where('compte', '701')
            ->whereBetween('date', [now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()])
            ->sum('credit');
        
        $caMoisActuel = Journal::where('compte', '701')
            ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('credit');
        
        if ($caMoisPrecedent == 0) return 0;
        return round((($caMoisActuel - $caMoisPrecedent) / $caMoisPrecedent) * 100, 1);
    }
    
    private function getCroissanceBenefice()
    {
        $resultatPrecedent = Resultat::where('date', '<', now()->subMonth())->latest()->first();
        $resultatActuel = Resultat::latest()->first();
        
        if (!$resultatPrecedent || $resultatPrecedent->resultat_net == 0) return 0;
        return round((($resultatActuel->resultat_net - $resultatPrecedent->resultat_net) / abs($resultatPrecedent->resultat_net)) * 100, 1);
    }
    
    private function getMargeBrute()
    {
        $ca = Journal::where('compte', '701')->sum('credit');
        $achats = Journal::where('compte', '601')->sum('debit');
        
        if ($ca == 0) return 0;
        return round((($ca - $achats) / $ca) * 100, 1);
    }
    
    private function getBFR()
    {
        $actifCirculant = $this->getSoldeByClasse('3');
        $dettes = $this->getSoldeByClasse('4');
        return $actifCirculant - $dettes;
    }
    
    private function getDelaiPaiementClient()
    {
        $creances = Journal::where('compte', '411')->sum('debit') - Journal::where('compte', '411')->sum('credit');
        $ca = Journal::where('compte', '701')->sum('credit');
        
        if ($ca == 0) return 0;
        return round(($creances / $ca) * 365, 0);
    }
    
    private function getROE()
    {
        $resultat = Resultat::latest()->first();
        $capitauxPropres = $this->getSoldeByClasse('1');
        
        if ($capitauxPropres == 0) return 0;
        return round(($resultat->resultat_net / $capitauxPropres) * 100, 1);
    }
    
    private function getSoldeByClasse($classe)
    {
        $comptes = PlanComptable::where('classe', $classe)->pluck('numero');
        $total = 0;
        
        foreach ($comptes as $compte) {
            $grandLivre = GrandLivre::where('compte', $compte)->latest()->first();
            if ($grandLivre) {
                $total += $grandLivre->solde_final;
            }
        }
        return $total;
    }
    
    private function getRatioRentabilite()
    {
        $resultat = Resultat::latest()->first();
        if (!$resultat || $resultat->total_produits == 0) return 0;
        return round(($resultat->resultat_net / $resultat->total_produits) * 100, 1);
    }
    
    private function getTresorerie()
    {
        $banque = Banque::sum('montant');
        $caisse = Caisse::where('type', 'entree')->sum('montant') - Caisse::where('type', 'sortie')->sum('montant');
        return $banque + $caisse;
    }
    
    private function getFondsRoulement()
    {
        $capitauxPropres = $this->getSoldeByClasse('1');
        $actifImmobilise = $this->getSoldeByClasse('2');
        return $capitauxPropres - $actifImmobilise;
    }
    
    private function getRotationStocks()
    {
        $stocks = Journal::where('compte', '31')->sum('debit') - Journal::where('compte', '31')->sum('credit');
        $achats = Journal::where('compte', '601')->sum('debit');
        
        if ($achats == 0) return 0;
        return round(($stocks / $achats) * 365, 0);
    }
    
    private function getGraphiquesData()
    {
        $mois = [];
        $ventes = [];
        $charges = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            
            $ventes[] = Journal::where('compte', '701')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('credit');
            
            $charges[] = Journal::whereIn('compte', ['601', '602', '611', '612', '613', '614', '631'])
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('debit');
        }
        
        return compact('mois', 'ventes', 'charges');
    }
    
    private function getCAMois()
    {
        $mois = [];
        for ($i = 11; $i >= 0; $i--) {
            $mois[] = now()->subMonths($i)->format('M Y');
        }
        return $mois;
    }
    
    private function getCAData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = Journal::where('compte', '701')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('credit');
        }
        return $data;
    }
    
    private function getEvolutionVentes()
    {
        return Journal::where('compte', '701')
            ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as mois'), DB::raw('SUM(credit) as total'))
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->limit(12)
            ->get();
    }
    
    private function getEvolutionCharges()
    {
        return Journal::whereIn('compte', ['601', '602', '611', '612', '613', '614', '631'])
            ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as mois'), DB::raw('SUM(debit) as total'))
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->limit(12)
            ->get();
    }
    
    private function getTopClients()
    {
        $topClients = Client::select('clients.*')
            ->leftJoin('ecritures', 'clients.id', '=', 'ecritures.client_id')
            ->selectRaw('COALESCE(SUM(ecritures.montant_debit), 0) as total')
            ->groupBy('clients.id', 'clients.nom', 'clients.email', 'clients.telephone', 'clients.adresse', 'clients.solde', 'clients.created_at', 'clients.updated_at')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        return $topClients->map(function($client) {
            $client->total = $client->total ?? 0;
            return $client;
        });
    }
}