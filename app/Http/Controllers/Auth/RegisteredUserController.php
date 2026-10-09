<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Log;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'matricule' => ['required', 'string', 'max:20', 'unique:users', 'regex:/^(DR|DA|CO|CA|VE)[0-9]{3}$/'],
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'matricule.regex' => 'Le matricule doit commencer par DR, DA, CO, CA ou VE suivi de 3 chiffres (ex: CO001)',
            'matricule.unique' => 'Ce matricule est déjà utilisé',
            'email.unique' => 'Cet email est déjà utilisé',
        ]);

        // Déterminer le rôle selon le matricule
        $prefix = substr($request->matricule, 0, 2);
        $roleMap = ['DR' => 1, 'DA' => 2, 'CO' => 3, 'CA' => 4, 'VE' => 5];
        $roleId = $roleMap[$prefix] ?? 6; // Par défaut rôle Utilisateur (id=6)

        $user = User::create([
            'matricule' => strtoupper($request->matricule),
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'actif' => true,
        ]);

        // Journaliser l'inscription
        Log::create([
            'user_id' => $user->id,
            'action' => 'Inscription',
            'table' => 'users',
            'details' => "Nouvel utilisateur inscrit: {$user->matricule} - {$user->email}",
            'ip' => $request->ip(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Rediriger selon le rôle
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user): RedirectResponse
    {
        $roleCode = $user->role->code ?? null;

        switch($roleCode) {
            case 'DR':
                return redirect()->route('directeur.dashboard')->with('success', 'Bienvenue Directeur Général !');
            case 'DA':
                return redirect()->route('directrice.dashboard')->with('success', 'Bienvenue Directrice Générale Adjointe !');
            case 'CO':
                return redirect()->route('comptable.dashboard')->with('success', 'Bienvenue Comptable !');
            case 'CA':
                return redirect()->route('caissier.dashboard')->with('success', 'Bienvenue Caissière !');
            case 'VE':
                return redirect()->route('vendeur.dashboard')->with('success', 'Bienvenue Vendeur !');
            case 'AD':
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenue Administrateur !');
            default:
                return redirect()->route('dashboard')->with('success', 'Inscription réussie !');
        }
    }
}