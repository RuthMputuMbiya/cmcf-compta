<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PlanComptableImport;
use App\Models\Log;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:AD']);
    }
    
    public function importPlanComptable(Request $request)
    {
        $request->validate([
            'fichier' => 'required|mimes:xlsx,xls,csv'
        ]);
        
        try {
            Excel::import(new PlanComptableImport, $request->file('fichier'));
            
            Log::create([
                'user_id' => auth()->id(),
                'action' => 'Import plan comptable',
                'table' => 'plan_comptable',
                'details' => 'Import réussi',
                'ip' => $request->ip(),
            ]);
            
            return redirect()->to('/admin/plan-comptable')->with('success', 'Plan comptable importé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }
    
    public function importClients(Request $request)
    {
        $request->validate([
            'fichier' => 'required|mimes:xlsx,xls,csv'
        ]);
        
        try {
            Excel::import(new ClientsImport, $request->file('fichier'));
            return redirect()->to('/admin/clients')->with('success', 'Clients importés avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import des clients: ' . $e->getMessage());
        }
    }
    
    public function importFournisseurs(Request $request)
    {
        $request->validate([
            'fichier' => 'required|mimes:xlsx,xls,csv'
        ]);
        
        try {
            Excel::import(new FournisseursImport, $request->file('fichier'));
            return redirect()->to('/admin/fournisseurs')->with('success', 'Fournisseurs importés avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import des fournisseurs: ' . $e->getMessage());
        }
    }
}