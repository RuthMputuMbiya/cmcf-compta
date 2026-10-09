<?php
namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Client;
use App\Models\Caisse;
use App\Models\Ecriture;
use App\Models\Log;
use App\Models\PlanComptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class VendeurController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:VE']);
    }
    
    // ==================== DASHBOARD ====================
    public function dashboard()
    {
        $stats = [
            'total_factures' => Facture::count(),
            'factures_mois' => Facture::whereMonth('date', now()->month)->count(),
            'total_ventes' => Facture::sum('montant_ttc'),
            'ventes_mois' => Facture::whereMonth('date', now()->month)->sum('montant_ttc'),
            'factures_payees' => Facture::where('statut', 'payee')->count(),
            'factures_impayees' => Facture::where('statut', 'impayee')->count(),
            'total_encaisse' => Facture::where('statut', 'payee')->sum('montant_ttc'),
            'total_impaye' => Facture::where('statut', 'impayee')->sum('montant_ttc'),
            'taux_encaissement' => $this->getTauxEncaissement(),
            'dernieres_factures' => Facture::with('client')->orderBy('date', 'desc')->limit(10)->get(),
            'top_clients' => $this->getTopClients(),
        ];
        
        return view('vendeur.dashboard', $stats);
    }
    
    // ==================== STATISTIQUES ====================
    public function statistiques()
    {
        $stats = [
            'evolution_ventes' => $this->getEvolutionVentes(),
            'ventes_par_jour' => $this->getVentesParJour(),
            'top_produits' => $this->getTopProduits(),
            'repartition_paiements' => $this->getRepartitionPaiements(),
            'objectif_mensuel' => $this->getObjectifMensuel(),
        ];
        
        return view('vendeur.statistiques', $stats);
    }
    
    // ==================== FACTURES ====================
    public function factures(Request $request)
    {
        $query = Facture::with(['client', 'creePar']);
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        
        $factures = $query->orderBy('date', 'desc')->paginate(30);
        $total_ht = $factures->sum('montant_ht');
        $total_tva = $factures->sum('tva');
        $total_ttc = $factures->sum('montant_ttc');
        $clients = Client::orderBy('nom')->get();
        
        return view('vendeur.factures.index', compact('factures', 'total_ht', 'total_tva', 'total_ttc', 'clients'));
    }
    
    public function createFacture()
    {
        $clients = Client::orderBy('nom')->get();
        $dernierNumero = Facture::latest()->first();
        $numero = 'FACT-' . date('Ymd') . '-' . str_pad(($dernierNumero ? intval(substr($dernierNumero->numero, -4)) + 1 : 1), 4, '0', STR_PAD_LEFT);
        
        return view('vendeur.factures.create', compact('clients', 'numero'));
    }
    
    public function storeFacture(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|unique:factures',
            'date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $montant_ttc = $validated['montant_ht'] + ($validated['tva'] ?? 0);
        
        DB::beginTransaction();
        try {
            $facture = Facture::create([
                'numero' => $validated['numero'],
                'date' => $validated['date'],
                'client_id' => $validated['client_id'],
                'montant_ht' => $validated['montant_ht'],
                'tva' => $validated['tva'] ?? 0,
                'montant_ttc' => $montant_ttc,
                'description' => $validated['description'] ?? null,
                'statut' => 'impayee',
                'cree_par' => auth()->id(),
            ]);
            
            // Créer l'écriture comptable automatique
            $this->creerEcritureVente($facture);
            
            DB::commit();
            
            $this->logAction('Création facture', 'factures', $facture->id, "Facture {$facture->numero} créée");
            
            return redirect()->route('vendeur.factures')->with('success', 'Facture créée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function showFacture($id)
    {
        $facture = Facture::with(['client', 'creePar'])->findOrFail($id);
        
        $pdf = Pdf::loadView('vendeur.factures.pdf', compact('facture'));
        return $pdf->stream('facture_' . $facture->numero . '.pdf');
    }
    
    public function editFacture($id)
    {
        $facture = Facture::findOrFail($id);
        
        if ($facture->statut === 'payee') {
            return back()->with('error', 'Une facture payée ne peut pas être modifiée');
        }
        
        $clients = Client::orderBy('nom')->get();
        
        return view('vendeur.factures.edit', compact('facture', 'clients'));
    }
    
    public function updateFacture(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);
        
        if ($facture->statut === 'payee') {
            return back()->with('error', 'Une facture payée ne peut pas être modifiée');
        }
        
        $validated = $request->validate([
            'date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'montant_ht' => 'required|numeric|min:0',
            'tva' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $montant_ttc = $validated['montant_ht'] + ($validated['tva'] ?? 0);
        
        DB::beginTransaction();
        try {
            $facture->update([
                'date' => $validated['date'],
                'client_id' => $validated['client_id'],
                'montant_ht' => $validated['montant_ht'],
                'tva' => $validated['tva'] ?? 0,
                'montant_ttc' => $montant_ttc,
                'description' => $validated['description'] ?? null,
            ]);
            
            DB::commit();
            
            $this->logAction('Modification facture', 'factures', $facture->id, "Facture {$facture->numero} modifiée");
            
            return redirect()->route('vendeur.factures')->with('success', 'Facture modifiée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function destroyFacture($id)
    {
        $facture = Facture::findOrFail($id);
        
        if ($facture->statut === 'payee') {
            return back()->with('error', 'Une facture payée ne peut pas être supprimée');
        }
        
        DB::beginTransaction();
        try {
            // Supprimer l'écriture comptable associée
            Ecriture::where('numero_piece', 'LIKE', '%' . $facture->numero . '%')->delete();
            
            $facture->delete();
            
            DB::commit();
            
            $this->logAction('Suppression facture', 'factures', $facture->id, "Facture {$facture->numero} supprimée");
            
            return back()->with('success', 'Facture supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    // ==================== ENCAISSEMENTS ====================
    public function encaissements(Request $request)
    {
        $facturesImpayees = Facture::with('client')
            ->where('statut', 'impayee')
            ->orderBy('date', 'asc')
            ->get();
        
        $total_impaye = $facturesImpayees->sum('montant_ttc');
        
        $clients = Client::orderBy('nom')->get();
        
        return view('vendeur.encaissements.index', compact('facturesImpayees', 'total_impaye', 'clients'));
    }
    
    public function enregistrerEncaissement(Request $request)
    {
        $validated = $request->validate([
            'facture_id' => 'required|exists:factures,id',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'mode_paiement' => 'required|in:espece,banque,cheque',
            'reference' => 'nullable|string',
        ]);
        
        $facture = Facture::findOrFail($validated['facture_id']);
        
        if ($facture->statut === 'payee') {
            return back()->with('error', 'Cette facture est déjà payée');
        }
        
        if ($validated['montant'] > $facture->montant_ttc) {
            return back()->with('error', 'Le montant saisi dépasse le montant de la facture');
        }
        
        DB::beginTransaction();
        try {
            // Mettre à jour le statut de la facture
            $estPayee = $validated['montant'] >= $facture->montant_ttc;
            $facture->update([
                'statut' => $estPayee ? 'payee' : 'impayee',
                'date_paiement' => $validated['date'],
                'mode_paiement' => $validated['mode_paiement'],
            ]);
            
            // Enregistrer l'encaissement en caisse
            Caisse::create([
                'date' => $validated['date'],
                'type' => 'entree',
                'libelle' => 'Encaissement facture ' . $facture->numero,
                'montant' => $validated['montant'],
                'reference' => $facture->numero,
                'enregistre_par' => auth()->id(),
            ]);
            
            // Créer l'écriture d'encaissement
            $this->creerEcritureEncaissement($facture, $validated['montant']);
            
            DB::commit();
            
            $this->logAction('Encaissement facture', 'factures', $facture->id, "Facture {$facture->numero} encaissée de {$validated['montant']} FC");
            
            $message = $estPayee ? 'Facture entièrement payée' : 'Paiement partiel enregistré';
            return redirect()->route('vendeur.encaissements')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function historiqueEncaissements()
    {
        $encaissements = Caisse::where('libelle', 'like', '%Encaissement facture%')
            ->with('enregistrePar')
            ->orderBy('date', 'desc')
            ->paginate(50);
        
        $total_encaisse = $encaissements->sum('montant');
        
        return view('vendeur.encaissements.historique', compact('encaissements', 'total_encaisse'));
    }
    
    // ==================== VENTES ====================
    public function ventes(Request $request)
    {
        $query = Facture::with(['client', 'creePar'])
            ->where('statut', 'payee');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $ventes = $query->orderBy('date', 'desc')->paginate(50);
        $total_ventes = $ventes->sum('montant_ttc');
        
        // Statistiques des ventes
        $stats = [
            'ventes_jour' => Facture::whereDate('date', today())->where('statut', 'payee')->sum('montant_ttc'),
            'ventes_semaine' => Facture::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->where('statut', 'payee')->sum('montant_ttc'),
            'ventes_mois' => Facture::whereMonth('date', now()->month)->where('statut', 'payee')->sum('montant_ttc'),
            'nb_ventes_jour' => Facture::whereDate('date', today())->where('statut', 'payee')->count(),
        ];
        
        return view('vendeur.ventes.index', compact('ventes', 'total_ventes', 'stats'));
    }
    
    public function rapportVentes(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);
        
        $ventes = Facture::with('client')
            ->whereBetween('date', [$request->date_debut, $request->date_fin])
            ->where('statut', 'payee')
            ->get();
        
        $total_ht = $ventes->sum('montant_ht');
        $total_tva = $ventes->sum('tva');
        $total_ttc = $ventes->sum('montant_ttc');
        
        $pdf = Pdf::loadView('vendeur.rapports.ventes', compact('ventes', 'total_ht', 'total_tva', 'total_ttc', 'request'));
        return $pdf->download('rapport_ventes_' . date('Y-m-d') . '.pdf');
    }
    
    // ==================== CLIENTS (Vendeur) ====================
    public function clientsList(Request $request)
    {
        $query = Client::query();
        
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        $clients = $query->orderBy('nom')->paginate(30);
        
        return view('vendeur.clients.index', compact('clients'));
    }
    
    public function createClientVendeur()
    {
        return view('vendeur.clients.create');
    }
    
    public function storeClientVendeur(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string',
        ]);
        
        $client = Client::create($validated);
        
        $this->logAction('Création client (vendeur)', 'clients', $client->id, "Client {$client->nom} créé");
        
        return redirect()->route('vendeur.clients')->with('success', 'Client ajouté avec succès');
    }
    
    // ==================== MÉTHODES PRIVÉES ====================
    private function creerEcritureVente($facture)
    {
        $numeroPiece = 'VEN-' . $facture->numero;
        
        Ecriture::create([
            'numero_piece' => $numeroPiece,
            'date' => $facture->date,
            'libelle' => 'Vente facture ' . $facture->numero . ' - Client: ' . $facture->client->nom,
            'compte_debit' => '411', // Clients
            'montant_debit' => $facture->montant_ttc,
            'compte_credit' => '701', // Ventes
            'montant_credit' => $facture->montant_ht,
            'client_id' => $facture->client_id,
            'saisi_par' => auth()->id(),
            'statut' => 'valide',
        ]);
        
        // Écriture pour la TVA si applicable
        if ($facture->tva > 0) {
            Ecriture::create([
                'numero_piece' => $numeroPiece . '-TVA',
                'date' => $facture->date,
                'libelle' => 'TVA facture ' . $facture->numero,
                'compte_debit' => '411', // Clients
                'montant_debit' => $facture->tva,
                'compte_credit' => '445', // TVA collectée
                'montant_credit' => $facture->tva,
                'client_id' => $facture->client_id,
                'saisi_par' => auth()->id(),
                'statut' => 'valide',
            ]);
        }
    }
    
    private function creerEcritureEncaissement($facture, $montant)
    {
        $numeroPiece = 'ENC-' . $facture->numero . '-' . date('His');
        
        Ecriture::create([
            'numero_piece' => $numeroPiece,
            'date' => now(),
            'libelle' => 'Encaissement facture ' . $facture->numero,
            'compte_debit' => '512', // Caisse
            'montant_debit' => $montant,
            'compte_credit' => '411', // Clients
            'montant_credit' => $montant,
            'client_id' => $facture->client_id,
            'saisi_par' => auth()->id(),
            'statut' => 'valide',
        ]);
    }
    
    private function getTauxEncaissement()
    {
        $total = Facture::sum('montant_ttc');
        $paye = Facture::where('statut', 'payee')->sum('montant_ttc');
        
        if ($total == 0) return 0;
        return ($paye / $total) * 100;
    }
    
    private function getTopClients()
    {
        return Client::withSum('factures', 'montant_ttc')
            ->orderBy('factures_sum_montant_ttc', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function getEvolutionVentes()
    {
        $mois = [];
        $montants = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            $montants[] = Facture::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->where('statut', 'payee')
                ->sum('montant_ttc');
        }
        
        return compact('mois', 'montants');
    }
    
    private function getVentesParJour()
    {
        $jours = [];
        $montants = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $jours[] = $date->format('d/m');
            $montants[] = Facture::whereDate('date', $date)
                ->where('statut', 'payee')
                ->sum('montant_ttc');
        }
        
        return compact('jours', 'montants');
    }
    
    private function getTopProduits()
    {
        // À adapter selon votre structure de produits
        return Facture::select('description', DB::raw('SUM(montant_ttc) as total'))
            ->whereNotNull('description')
            ->groupBy('description')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function getRepartitionPaiements()
    {
        return [
            'espece' => Facture::where('mode_paiement', 'espece')->where('statut', 'payee')->count(),
            'banque' => Facture::where('mode_paiement', 'banque')->where('statut', 'payee')->count(),
            'cheque' => Facture::where('mode_paiement', 'cheque')->where('statut', 'payee')->count(),
        ];
    }
    
    private function getObjectifMensuel()
    {
        $objectif = 10000000; // Objectif mensuel de 10 millions
        $realise = Facture::whereMonth('date', now()->month)
            ->where('statut', 'payee')
            ->sum('montant_ttc');
        
        $pourcentage = $objectif > 0 ? ($realise / $objectif) * 100 : 0;
        
        return compact('objectif', 'realise', 'pourcentage');
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