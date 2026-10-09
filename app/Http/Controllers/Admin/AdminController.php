<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ecriture;
use App\Models\Journal;
use App\Models\Bilan;
use App\Models\Resultat;
use App\Models\Log;
use App\Models\PlanComptable;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Afficher le tableau de bord administrateur
     */
    public function dashboard()
    {
        // Gestion des erreurs pour les tables vides
        try {
            $dernierBilan = Bilan::latest()->first();
        } catch (\Exception $e) {
            $dernierBilan = null;
        }
        
        try {
            $dernierResultat = Resultat::latest()->first();
        } catch (\Exception $e) {
            $dernierResultat = null;
        }
        
        try {
            $usersByRole = User::select('role_id', DB::raw('count(*) as count'))
                ->with('role')
                ->groupBy('role_id')
                ->get();
        } catch (\Exception $e) {
            $usersByRole = collect();
        }
        
        try {
            $derniersLogs = Log::with('user')->orderBy('created_at', 'desc')->limit(10)->get();
        } catch (\Exception $e) {
            $derniersLogs = collect();
        }
        
        $stats = [
            'total_users' => User::count(),
            'users_actifs' => User::where('actif', true)->count(),
            'users_inactifs' => User::where('actif', false)->count(),
            'total_ecritures' => Ecriture::count(),
            'ecritures_validees' => Ecriture::where('statut', 'valide')->count(),
            'ecritures_brouillon' => Ecriture::where('statut', 'brouillon')->count(),
            'total_journal' => Journal::count(),
            'total_logs' => Log::count(),
            'total_comptes' => PlanComptable::count(),
            'total_roles' => Role::count(),
            'dernier_bilan' => $dernierBilan,
            'dernier_resultat' => $dernierResultat,
            'users_by_role' => $usersByRole,
            'derniers_logs' => $derniersLogs,
            'activite_recente' => $this->getActiviteRecente(),
            'logs_aujourdhui' => Log::whereDate('created_at', today())->count(),
            'evolution_recente' => $this->getEvolutionRecente(),
            'taille_base' => $this->getDatabaseSize(),
            'activite_aujourdhui' => $this->getActiviteAujourdhui(),
            'evolution_mois' => $this->getEvolutionMois(),
            'evolution_users' => $this->getEvolutionUsers(),
            'role_labels' => $this->getRoleLabels(),
            'role_data' => $this->getRoleData(),
        ];
        
        return view('admin.dashboard', $stats);
    }
    
    /**
     * Afficher les statistiques avancées
     */
    public function statistiques()
    {
        $stats = [
            'evolution_utilisateurs' => $this->getEvolutionUtilisateurs(),
            'evolution_ecritures' => $this->getEvolutionEcritures(),
            'evolution_logs' => $this->getEvolutionLogs(),
            'top_actions' => $this->getTopActions(),
            'activite_par_heure' => $this->getActiviteParHeure(),
            'taille_base_donnees' => $this->getDatabaseSize(),
            'total_users' => User::count(),
            'total_ecritures' => Ecriture::count(),
            'total_logs' => Log::count(),
            'role_labels' => $this->getRoleLabels(),
            'role_data' => $this->getRoleData(),
            'evolution_mois' => $this->getEvolutionMois(),
            'evolution_users' => $this->getEvolutionUsers(),
        ];
        
        return view('admin.statistiques', $stats);
    }
    
    /**
     * Exporter les statistiques en PDF
     */
    public function exportStatistiquesPDF()
    {
        $stats = [
            'evolution_utilisateurs' => $this->getEvolutionUtilisateurs(),
            'evolution_ecritures' => $this->getEvolutionEcritures(),
            'evolution_logs' => $this->getEvolutionLogs(),
            'top_actions' => $this->getTopActions(),
            'total_users' => User::count(),
            'total_ecritures' => Ecriture::count(),
            'total_logs' => Log::count(),
            'taille_base_donnees' => $this->getDatabaseSize(),
        ];
        
        $pdf = Pdf::loadView('exports.pdf.statistiques-admin', compact('stats'));
        return $pdf->download('statistiques_admin_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Obtenir l'activité récente
     */
    private function getActiviteRecente()
    {
        return [
            'users_aujourdhui' => User::whereDate('created_at', today())->count(),
            'ecritures_aujourdhui' => Ecriture::whereDate('created_at', today())->count(),
            'logs_aujourdhui' => Log::whereDate('created_at', today())->count(),
        ];
    }
    
    /**
     * Évolution récente en pourcentage
     */
    private function getEvolutionRecente()
    {
        $moisPrecedent = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $moisActuel = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        if ($moisPrecedent == 0) {
            return $moisActuel > 0 ? 100 : 0;
        }
        
        return round((($moisActuel - $moisPrecedent) / $moisPrecedent) * 100);
    }
    
    /**
     * Activité aujourd'hui
     */
    private function getActiviteAujourdhui()
    {
        return Ecriture::whereDate('created_at', today())->count() + 
               Log::whereDate('created_at', today())->count() +
               User::whereDate('created_at', today())->count();
    }
    
    /**
     * Évolution des utilisateurs (12 derniers mois)
     */
    private function getEvolutionUtilisateurs()
    {
        $mois = [];
        $counts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            $counts[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        return compact('mois', 'counts');
    }
    
    /**
     * Évolution des écritures (12 derniers mois)
     */
    private function getEvolutionEcritures()
    {
        $mois = [];
        $counts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            $counts[] = Ecriture::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        return compact('mois', 'counts');
    }
    
    /**
     * Évolution des logs (12 derniers mois)
     */
    private function getEvolutionLogs()
    {
        $mois = [];
        $counts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('M Y');
            $counts[] = Log::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        return compact('mois', 'counts');
    }
    
    /**
     * Top 10 des actions les plus fréquentes
     */
    private function getTopActions()
    {
        return Log::select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * Activité par heure de la journée (CORRIGÉ)
     */
    private function getActiviteParHeure()
    {
        $heures = [];
        $counts = [];
        
        for ($i = 0; $i < 24; $i++) {
            $heures[] = $i . 'h';
            $counts[] = Log::whereRaw('HOUR(created_at) = ?', [$i])->count();
        }
        
        return compact('heures', 'counts');
    }
    
    /**
     * Taille de la base de données en MB
     */
    private function getDatabaseSize()
    {
        try {
            $databaseName = env('DB_DATABASE');
            $result = DB::select("SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = ?", [$databaseName]);
            $size = $result[0]->size ?? 0;
            return round($size / 1024 / 1024, 2) . ' MB';
        } catch (\Exception $e) {
            return '0 MB';
        }
    }
    
    /**
     * Labels pour le graphique des rôles
     */
    private function getRoleLabels()
    {
        $roles = Role::withCount('users')->get();
        return $roles->pluck('nom')->toArray();
    }
    
    /**
     * Données pour le graphique des rôles
     */
    private function getRoleData()
    {
        $roles = Role::withCount('users')->get();
        return $roles->pluck('users_count')->toArray();
    }
    
    /**
     * Mois pour l'évolution
     */
    private function getEvolutionMois()
    {
        $mois = [];
        for ($i = 11; $i >= 0; $i--) {
            $mois[] = now()->subMonths($i)->format('M Y');
        }
        return $mois;
    }
    
    /**
     * Données d'évolution des utilisateurs
     */
    private function getEvolutionUsers()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        return $data;
    }
}