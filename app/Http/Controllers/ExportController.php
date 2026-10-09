<?php
namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Balance;
use App\Models\Bilan;
use App\Models\Resultat;
use App\Models\Ecriture;
use App\Models\GrandLivre;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JournalExport;
use App\Exports\BalanceExport;
use App\Exports\BilanExport;
use App\Exports\ResultatExport;
use App\Exports\GrandLivreExport;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // EXPORT PDF
    public function pdfJournal(Request $request)
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
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('journal_' . date('Y-m-d') . '.pdf');
    }
    
    public function pdfBalance(Request $request)
    {
        $balance = Balance::orderBy('compte')->get();
        $total_debiteur = $balance->sum('solde_debiteur');
        $total_crediteur = $balance->sum('solde_crediteur');
        
        $pdf = Pdf::loadView('exports.pdf.balance', compact('balance', 'total_debiteur', 'total_crediteur'));
        return $pdf->download('balance_' . date('Y-m-d') . '.pdf');
    }
    
    public function pdfBilan()
    {
        $bilan = Bilan::latest()->first();
        $pdf = Pdf::loadView('exports.pdf.bilan', compact('bilan'));
        return $pdf->download('bilan_' . date('Y-m-d') . '.pdf');
    }
    
    public function pdfResultat()
    {
        $resultat = Resultat::latest()->first();
        $pdf = Pdf::loadView('exports.pdf.resultat', compact('resultat'));
        return $pdf->download('compte_resultat_' . date('Y-m-d') . '.pdf');
    }
    
    public function pdfGrandLivre(Request $request)
    {
        $compte = $request->compte;
        $grandLivre = GrandLivre::when($compte, function($query, $compte) {
            return $query->where('compte', $compte);
        })->orderBy('compte')->get();
        
        $pdf = Pdf::loadView('exports.pdf.grand-livre', compact('grandLivre', 'compte'));
        return $pdf->download('grand_livre_' . date('Y-m-d') . '.pdf');
    }
    
    // EXPORT EXCEL
    public function excelJournal(Request $request)
    {
        return Excel::download(new JournalExport($request), 'journal_' . date('Y-m-d') . '.xlsx');
    }
    
    public function excelBalance()
    {
        return Excel::download(new BalanceExport, 'balance_' . date('Y-m-d') . '.xlsx');
    }
    
    public function excelBilan()
    {
        return Excel::download(new BilanExport, 'bilan_' . date('Y-m-d') . '.xlsx');
    }
    
    public function excelResultat()
    {
        return Excel::download(new ResultatExport, 'compte_resultat_' . date('Y-m-d') . '.xlsx');
    }
    
    public function excelGrandLivre(Request $request)
    {
        return Excel::download(new GrandLivreExport($request), 'grand_livre_' . date('Y-m-d') . '.xlsx');
    }
    
    // EXPORT CSV
    public function csvJournal(Request $request)
    {
        return Excel::download(new JournalExport($request), 'journal_' . date('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    // RAPPORTS PERSONNALISÉS
    public function rapportPeriodique(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'format' => 'required|in:pdf,excel',
            'type' => 'required|in:complet,ventes,charges,tresorerie'
        ]);
        
        $data = $this->getRapportData($request);
        
        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('exports.pdf.rapport-periodique', $data);
            return $pdf->download('rapport_' . $request->date_debut . '_au_' . $request->date_fin . '.pdf');
        } else {
            return Excel::download(new RapportPeriodiqueExport($data), 'rapport_' . $request->date_debut . '_au_' . $request->date_fin . '.xlsx');
        }
    }
    
    private function getRapportData($request)
    {
        $data = [
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'entreprise' => 'CM CONSULTING-FORMATION SASU',
        ];
        
        switch($request->type) {
            case 'complet':
                $data['journal'] = Journal::whereBetween('date', [$request->date_debut, $request->date_fin])->get();
                $data['total_ventes'] = Journal::where('compte', '701')->whereBetween('date', [$request->date_debut, $request->date_fin])->sum('credit');
                $data['total_achats'] = Journal::where('compte', '601')->whereBetween('date', [$request->date_debut, $request->date_fin])->sum('debit');
                break;
            case 'ventes':
                $data['ventes'] = Journal::where('compte', '701')->whereBetween('date', [$request->date_debut, $request->date_fin])->get();
                $data['total_ventes'] = $data['ventes']->sum('credit');
                break;
            case 'charges':
                $data['charges'] = Journal::whereIn('compte', ['601', '602', '611', '612', '613', '614', '631'])
                    ->whereBetween('date', [$request->date_debut, $request->date_fin])
                    ->get();
                $data['total_charges'] = $data['charges']->sum('debit');
                break;
            case 'tresorerie':
                $data['banque'] = Banque::whereBetween('date', [$request->date_debut, $request->date_fin])->get();
                $data['caisse'] = Caisse::whereBetween('date', [$request->date_debut, $request->date_fin])->get();
                $data['solde_banque'] = $data['banque']->sum('montant');
                $data['solde_caisse'] = $data['caisse']->where('type', 'entree')->sum('montant') - $data['caisse']->where('type', 'sortie')->sum('montant');
                break;
        }
        
        return $data;
    }
}