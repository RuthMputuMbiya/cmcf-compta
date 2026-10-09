<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    // Supprimer le constructeur avec middleware
    // Les middlewares sont déjà dans les routes
    
    public function index()
    {
        $roles = Role::withCount('users')->get();
        
        $permissions = [
            'DR' => [
                'journal' => true,
                'grand_livre' => true,
                'balance' => true,
                'bilan' => true,
                'resultat' => true,
                'banque' => true,
                'caisse' => true,
                'statistiques' => true,
                'export' => true,
            ],
            'DA' => [
                'journal' => true,
                'balance' => true,
                'bilan' => true,
                'resultat' => true,
                'banque' => true,
                'caisse' => true,
                'statistiques' => true,
                'validation' => true,
            ],
            'CO' => [
                'ecritures' => true,
                'journal' => true,
                'grand_livre' => true,
                'balance' => true,
                'bilan' => true,
                'resultat' => true,
                'clients' => true,
                'fournisseurs' => true,
                'statistiques' => true,
            ],
            'CA' => [
                'caisse_entree' => true,
                'caisse_sortie' => true,
                'banque_depot' => true,
                'banque_retrait' => true,
                'consultation' => true,
            ],
            'VE' => [
                'factures' => true,
                'ventes' => true,
                'encaissements' => true,
                'clients' => true,
            ],
            'AD' => [
                'all' => true,
            ],
        ];
        
        return view('admin.permissions.index', compact('roles', 'permissions'));
    }
    
    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $users = User::where('role_id', $id)->get();
        
        return view('admin.permissions.edit-role', compact('role', 'users'));
    }
    
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $role->update($validated);
        
        $this->logAction('Modification rôle', 'roles', $role->id, "Rôle {$role->nom} modifié");
        
        return redirect()->to('/admin/permissions')->with('success', 'Rôle modifié avec succès');
    }
    
    public function createRole()
    {
        return view('admin.permissions.create-role');
    }
    
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:roles',
            'code' => 'required|string|max:10|unique:roles',
            'description' => 'nullable|string',
        ]);
        
        $role = Role::create($validated);
        
        $this->logAction('Création rôle', 'roles', $role->id, "Rôle {$role->nom} créé");
        
        return redirect()->to('/admin/permissions')->with('success', 'Rôle créé avec succès');
    }
    
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->code === 'AD') {
            return back()->with('error', 'Impossible de supprimer le rôle Administrateur');
        }
        
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Ce rôle a des utilisateurs associés. Veuillez d\'abord réaffecter ces utilisateurs.');
        }
        
        $role->delete();
        
        $this->logAction('Suppression rôle', 'roles', $role->id, "Rôle {$role->nom} supprimé");
        
        return redirect()->to('/admin/permissions')->with('success', 'Rôle supprimé avec succès');
    }
    
    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);
        
        $user = User::findOrFail($validated['user_id']);
        $oldRole = $user->role->nom;
        
        $user->update(['role_id' => $validated['role_id']]);
        
        $this->logAction('Changement rôle utilisateur', 'users', $user->id, "Utilisateur {$user->matricule} passe de {$oldRole} à {$user->role->nom}");
        
        return redirect()->to('/admin/users')->with('success', 'Rôle attribué avec succès');
    }
    
    public function permissionsMatrix()
    {
        $roles = Role::all();
        $modules = [
            'Dashboard' => ['view'],
            'Journal' => ['view', 'export'],
            'Grand Livre' => ['view', 'export'],
            'Balance' => ['view', 'export'],
            'Bilan' => ['view', 'export'],
            'Compte Résultat' => ['view', 'export'],
            'Écritures' => ['view', 'create', 'edit', 'delete', 'validate'],
            'Clients' => ['view', 'create', 'edit', 'delete'],
            'Fournisseurs' => ['view', 'create', 'edit', 'delete'],
            'Caisse' => ['view', 'entree', 'sortie'],
            'Banque' => ['view', 'depot', 'retrait'],
            'Factures' => ['view', 'create', 'pay'],
            'Utilisateurs' => ['view', 'create', 'edit', 'delete'],
            'Logs' => ['view', 'export', 'delete'],
            'Plan Comptable' => ['view', 'create', 'edit', 'delete', 'import'],
            'Statistiques' => ['view', 'export'],
            'Système' => ['view', 'manage'],
        ];
        
        return view('admin.permissions.matrix', compact('roles', 'modules'));
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