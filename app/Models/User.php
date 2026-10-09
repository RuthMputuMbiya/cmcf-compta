<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'matricule',
        'name',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'password',
        'role_id',
        'actif',
        'derniere_connexion'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'actif' => 'boolean',
        'derniere_connexion' => 'datetime'
    ];
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    public function isAdmin()
    {
        return $this->role->code === 'AD';
    }
}