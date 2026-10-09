<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('matricule', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        
        if ($request->filled('actif')) {
            $query->where('actif', $request->actif);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(30);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }
    
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matricule' => 'required|string|max:20|unique:users',
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        $user = User::create([
            'matricule' => $request->matricule,
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'actif' => true,
        ]);
        
        $this->logAction('Création utilisateur', 'users', $user->id, "Utilisateur {$user->matricule} créé");
        
        return redirect()->to('/admin/users')->with('success', 'Utilisateur créé avec succès');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        
        $updateData = [
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'role_id' => $request->role_id,
            'actif' => $request->has('actif'),
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $user->update($updateData);
        
        $this->logAction('Modification utilisateur', 'users', $user->id, "Utilisateur {$user->matricule} modifié");
        
        return redirect()->to('/admin/users')->with('success', 'Utilisateur modifié avec succès');
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role && $user->role->code === 'AD') {
            return back()->with('error', 'Impossible de supprimer un administrateur');
        }
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }
        
        $this->logAction('Suppression utilisateur', 'users', $user->id, "Utilisateur {$user->matricule} supprimé");
        $user->delete();
        
        return redirect()->to('/admin/users')->with('success', 'Utilisateur supprimé avec succès');
    }
    
    public function toggleActif($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role && $user->role->code === 'AD' && $user->id !== auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver un autre administrateur');
        }
        
        $newStatus = !$user->actif;
        $user->update(['actif' => $newStatus]);
        
        $status = $newStatus ? 'activé' : 'désactivé';
        $this->logAction('Changement statut utilisateur', 'users', $user->id, "Utilisateur {$user->matricule} {$status}");
        
        return redirect()->to('/admin/users')->with('success', "Utilisateur {$status} avec succès");
    }
    
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        $activite = Log::where('user_id', $id)->orderBy('created_at', 'desc')->limit(50)->get();
        
        return view('admin.users.show', compact('user', 'activite'));
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