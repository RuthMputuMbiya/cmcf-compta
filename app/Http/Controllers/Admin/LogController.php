<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    // Supprimer le constructeur avec middleware
    // Les middlewares sont déjà dans les routes
    
    public function index(Request $request)
    {
        $query = Log::with('user');
        
        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        
        if ($request->filled('table')) {
            $query->where('table', $request->table);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(50);
        $users = User::all();
        $actions = Log::select('action')->distinct()->pluck('action');
        $tables = Log::select('table')->whereNotNull('table')->distinct()->pluck('table');
        
        return view('admin.logs.index', compact('logs', 'users', 'actions', 'tables'));
    }
    
    public function show($id)
    {
        $log = Log::with('user')->findOrFail($id);
        return view('admin.logs.show', compact('log'));
    }
    
    public function statistiques()
    {
        $stats = [
            'total_logs' => Log::count(),
            'logs_aujourdhui' => Log::whereDate('created_at', today())->count(),
            'logs_semaine' => Log::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'logs_mois' => Log::whereMonth('created_at', now()->month)->count(),
            'top_utilisateurs' => $this->getTopUtilisateurs(),
            'top_actions' => $this->getTopActions(),
            'activite_par_jour' => $this->getActiviteParJour(),
            'activite_par_heure' => $this->getActiviteParHeure(),
        ];
        
        return view('admin.logs.statistiques', $stats);
    }
    
    public function clear()
    {
        $count = Log::count();
        
        Log::truncate();
        
        $this->logAction('Nettoyage logs', 'logs', null, "{$count} logs supprimés");
        
        return redirect()->to('/admin/logs')->with('success', "{$count} logs ont été supprimés");
    }
    
    public function clearOld()
    {
        $date = now()->subDays(30);
        $count = Log::where('created_at', '<', $date)->delete();
        
        $this->logAction('Nettoyage anciens logs', 'logs', null, "{$count} logs antérieurs au {$date->format('d/m/Y')} supprimés");
        
        return redirect()->to('/admin/logs')->with('success', "{$count} anciens logs ont été supprimés");
    }
    
    public function export(Request $request)
    {
        $query = Log::with('user');
        
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'logs_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');
        
        // En-têtes CSV
        fputcsv($handle, ['ID', 'Utilisateur', 'Action', 'Table', 'Détails', 'IP', 'Date']);
        
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->user ? $log->user->email : 'Système',
                $log->action,
                $log->table,
                $log->details,
                $log->ip,
                $log->created_at->format('d/m/Y H:i:s'),
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        $this->logAction('Export logs', 'logs', null, "Export CSV des logs");
        
        return response($content, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    private function getTopUtilisateurs()
    {
        return Log::select('user_id', DB::raw('count(*) as total'))
            ->with('user')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function getTopActions()
    {
        return Log::select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function getActiviteParJour()
    {
        $jours = [];
        $counts = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $jours[] = $date->format('d/m');
            $counts[] = Log::whereDate('created_at', $date)->count();
        }
        
        return compact('jours', 'counts');
    }
    
    private function getActiviteParHeure()
    {
        $heures = [];
        $counts = [];
        
        for ($i = 0; $i < 24; $i++) {
            $heures[] = $i . 'h';
            $counts[] = Log::whereHour('created_at', $i)->count();
        }
        
        return compact('heures', 'counts');
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