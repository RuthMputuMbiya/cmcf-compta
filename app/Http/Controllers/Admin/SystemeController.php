<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemeController extends Controller
{
    // Supprimer le constructeur avec middleware
    // Les middlewares sont déjà dans les routes
    
    public function index()
    {
        $info = [
            'version_app' => '1.0.0',
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'db_driver' => DB::connection()->getDriverName(),
            'db_name' => DB::connection()->getDatabaseName(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'N/A',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
        
        $cache = [
            'config' => Cache::has('config_cached'),
            'routes' => Cache::has('routes_cached'),
            'views' => Cache::has('views_cached'),
            'events' => Cache::has('events_cached'),
        ];
        
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_ecritures' => \App\Models\Ecriture::count(),
            'total_logs' => Log::count(),
            'db_size' => $this->getDatabaseSize(),
            'storage_size' => $this->getStorageSize(),
            'last_backup' => $this->getLastBackup(),
        ];
        
        $logs = Log::orderBy('created_at', 'desc')->limit(50)->get();
        
        return view('admin.systeme.index', compact('info', 'cache', 'stats', 'logs'));
    }
    
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            $this->logAction('Nettoyage cache système', 'systeme', null, 'Tous les caches ont été vidés');
            
            return redirect()->to('/admin/systeme')->with('success', 'Cache vidé avec succès');
        } catch (\Exception $e) {
            return redirect()->to('/admin/systeme')->with('error', 'Erreur lors du nettoyage du cache: ' . $e->getMessage());
        }
    }
    
    public function optimize()
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            $this->logAction('Optimisation système', 'systeme', null, 'Optimisation des caches');
            
            return redirect()->to('/admin/systeme')->with('success', 'Optimisation terminée avec succès');
        } catch (\Exception $e) {
            return redirect()->to('/admin/systeme')->with('error', 'Erreur lors de l\'optimisation: ' . $e->getMessage());
        }
    }
    
    public function backup()
    {
        try {
            $backupDir = storage_path('backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }
            
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupDir . '/' . $filename;
            
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');
            
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );
            
            exec($command);
            
            if (File::exists($filepath)) {
                $this->logAction('Backup base de données', 'systeme', null, "Backup créé: {$filename}");
                return redirect()->to('/admin/systeme/backups')->with('success', "Backup créé avec succès: {$filename}");
            } else {
                throw new \Exception('Erreur lors de la création du backup');
            }
        } catch (\Exception $e) {
            return redirect()->to('/admin/systeme/backups')->with('error', 'Erreur lors du backup: ' . $e->getMessage());
        }
    }
    
    public function downloadBackup($filename)
    {
        $filepath = storage_path('backups/' . $filename);
        
        if (File::exists($filepath)) {
            $this->logAction('Téléchargement backup', 'systeme', null, "Backup téléchargé: {$filename}");
            return response()->download($filepath);
        }
        
        return redirect()->to('/admin/systeme/backups')->with('error', 'Fichier non trouvé');
    }
    
    public function backups()
    {
        $backupDir = storage_path('backups');
        $backups = [];
        
        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatSize($file->getSize()),
                    'date' => date('d/m/Y H:i:s', $file->getMTime()),
                ];
            }
            $backups = array_reverse($backups);
        }
        
        return view('admin.systeme.backups', compact('backups'));
    }
    
    public function deleteBackup($filename)
    {
        $filepath = storage_path('backups/' . $filename);
        
        if (File::exists($filepath)) {
            File::delete($filepath);
            $this->logAction('Suppression backup', 'systeme', null, "Backup supprimé: {$filename}");
            return redirect()->to('/admin/systeme/backups')->with('success', 'Backup supprimé avec succès');
        }
        
        return redirect()->to('/admin/systeme/backups')->with('error', 'Fichier non trouvé');
    }
    
    public function info()
    {
        $phpInfo = [
            'version' => phpversion(),
            'extensions' => get_loaded_extensions(),
            'disabled_functions' => ini_get('disable_functions'),
        ];
        
        $dbInfo = [
            'driver' => DB::connection()->getDriverName(),
            'host' => DB::connection()->getConfig('host'),
            'database' => DB::connection()->getDatabaseName(),
            'charset' => DB::connection()->getConfig('charset'),
        ];
        
        $envInfo = [
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'APP_URL' => env('APP_URL'),
        ];
        
        return view('admin.systeme.info', compact('phpInfo', 'dbInfo', 'envInfo'));
    }
    
    public function maintenance(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->has('enable')) {
                Artisan::call('down', ['--secret' => $request->secret ?? null]);
                $message = 'Mode maintenance activé';
            } else {
                Artisan::call('up');
                $message = 'Mode maintenance désactivé';
            }
            
            $this->logAction('Mode maintenance', 'systeme', null, $message);
            
            return redirect()->to('/admin/systeme/maintenance')->with('success', $message);
        }
        
        $maintenanceMode = app()->isDownForMaintenance();
        
        return view('admin.systeme.maintenance', compact('maintenanceMode'));
    }
    
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $lines = explode("\n", $content);
            $lines = array_reverse($lines);
            
            foreach ($lines as $line) {
                if (preg_match('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                    $logs[] = $line;
                }
            }
        }
        
        return view('admin.systeme.logs', compact('logs'));
    }
    
    private function getDatabaseSize()
    {
        $databaseName = env('DB_DATABASE');
        $result = DB::select("SELECT SUM(data_length + index_length) as size FROM information_schema.tables WHERE table_schema = ?", [$databaseName]);
        return $this->formatSize($result[0]->size ?? 0);
    }
    
    private function getStorageSize()
    {
        $storagePath = storage_path();
        $size = $this->getDirectorySize($storagePath);
        return $this->formatSize($size);
    }
    
    private function getDirectorySize($path)
    {
        $size = 0;
        foreach (File::glob($path . '/*') as $file) {
            $size += File::isFile($file) ? File::size($file) : $this->getDirectorySize($file);
        }
        return $size;
    }
    
    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    private function getLastBackup()
    {
        $backupDir = storage_path('backups');
        $lastBackup = null;
        
        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            if (count($files) > 0) {
                $latest = collect($files)->sortByDesc(function ($file) {
                    return $file->getMTime();
                })->first();
                $lastBackup = date('d/m/Y H:i:s', $latest->getMTime());
            }
        }
        
        return $lastBackup ?? 'Aucun backup';
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