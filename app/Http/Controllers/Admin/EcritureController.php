<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ecriture;
use App\Models\Journal;
use App\Models\PlanComptable;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EcritureController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:AD']);
    }

    // ==================== LISTE DES ÉCRITURES ====================
    public function index(Request $request)
    {
        $query = Ecriture::with(['client', 'fournisseur', 'saisiPar', 'validePar', 'compteDebiteur', 'compteCrediteur']);
        
        // Filtres
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('compte')) {
            $query->where(function($q) use ($request) {
                $q->where('compte_debit', $request->compte)
                  ->orWhere('compte_credit', $request->compte);
            });
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('numero_piece', 'like', '%' . $request->search . '%')
                  ->orWhere('libelle', 'like', '%' . $request->search . '%');
            });
        }
        
        $ecritures = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);
        
        // Statistiques
        $stats = [
            'total' => Ecriture::count(),
            'validees' => Ecriture::where('statut', 'valide')->count(),
            'brouillons' => Ecriture::where('statut', 'brouillon')->count(),
            'total_debit' => Ecriture::sum('montant_debit'),
            'total_credit' => Ecriture::sum('montant_credit'),
        ];
        
        $comptes = PlanComptable::orderBy('numero')->get();
        
        return view('admin.ecritures.index', compact('ecritures', 'stats', 'comptes'));
    }
    
    // ==================== VOIR LE DÉTAIL ====================
    public function show($id)
    {
        $ecriture = Ecriture::with(['client', 'fournisseur', 'saisiPar', 'validePar', 'compteDebiteur', 'compteCrediteur'])->findOrFail($id);
        
        return view('admin.ecritures.show', compact('ecriture'));
    }
    
    // ==================== CONFIRMATION SUPPRESSION ====================
    public function confirmDelete($id)
    {
        $ecriture = Ecriture::with(['compteDebiteur', 'compteCrediteur'])->findOrFail($id);
        
        return view('admin.ecritures.confirm-delete', compact('ecriture'));
    }
    
    // ==================== SUPPRESSION ====================
    public function destroy($id)
    {
        $ecriture = Ecriture::findOrFail($id);
        
        DB::beginTransaction();
        try {
            Journal::where('ecriture_id', $ecriture->id)->delete();
            $ecriture->delete();
            
            DB::commit();
            
            Log::create([
                'user_id' => auth()->id(),
                'action' => 'Suppression écriture (Admin)',
                'table' => 'ecritures',
                'record_id' => $ecriture->id,
                'details' => "Écriture {$ecriture->numero_piece} supprimée par " . auth()->user()->name,
                'ip' => request()->ip(),
            ]);
            
            return redirect()->to('/admin/ecritures')->with('success', 'Écriture supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    // ==================== EXPORT PDF ====================
    public function exportPDF(Request $request)
    {
        $query = Ecriture::with(['client', 'fournisseur', 'compteDebiteur', 'compteCrediteur']);
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        $ecritures = $query->orderBy('date', 'desc')->get();
        $total_debit = $ecritures->sum('montant_debit');
        $total_credit = $ecritures->sum('montant_credit');
        
        $pdf = Pdf::loadView('exports.pdf.admin-ecritures', compact('ecritures', 'total_debit', 'total_credit'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('ecritures_' . date('Y-m-d') . '.pdf');
    }
}