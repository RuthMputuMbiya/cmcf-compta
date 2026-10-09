<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        
        $user = $this->create($request->all());
        
        event(new Registered($user));
        
        auth()->login($user);
        
        return $this->redirectBasedOnRole($user);
    }
    
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // ✅ On garde uniquement les matricules DR, CO, AD
            'matricule' => ['required', 'string', 'max:20', 'unique:users', 'regex:/^(DR|CO|AD)[0-9]{3}$/'],
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'matricule.regex' => 'Le matricule doit être DR001, CO001 ou AD001',
            'matricule.unique' => 'Ce matricule existe déjà',
            'email.unique' => 'Cet email existe déjà',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
        ]);
    }

    protected function create(array $data)
    {
        $prefix = substr($data['matricule'], 0, 2);
        $roleMap = ['DR' => 1, 'CO' => 3, 'AD' => 6];
        $roleId = $roleMap[$prefix] ?? null;
        
        return User::create([
            'matricule' => strtoupper($data['matricule']),
            'name' => ucfirst($data['name']),
            'prenom' => $data['prenom'] ?? null,
            'email' => strtolower($data['email']), // ✅ L'email est converti en minuscules ici
            'telephone' => $data['telephone'] ?? null,
            'adresse' => $data['adresse'] ?? null,
            'password' => Hash::make($data['password']),
            'role_id' => $roleId,
            'actif' => true,
        ]);
    }
    
    private function redirectBasedOnRole($user)
    {
        $role = Role::where('id', $user->role_id)->first();
        
        if (!$role) {
            return redirect()->route('login');
        }
        
        $routes = [
            'DR' => 'directeur.dashboard',
            'CO' => 'comptable.dashboard',
            'AD' => 'admin.dashboard',
        ];
        
        $route = $routes[$role->code] ?? 'login';
        
        return redirect()->route($route)->with('success', 'Bienvenue !');
    }
}