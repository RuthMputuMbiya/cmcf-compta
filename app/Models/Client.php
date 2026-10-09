<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    
    protected $fillable = [
        'nom',        // ← AJOUTEZ CECI
        'email',
        'telephone',
        'adresse',
    ];
    
    // Relation avec les factures (si existantes)
    public function factures()
    {
        return $this->hasMany(Facture::class);
    }
    
    // Relation avec les écritures
    public function ecritures()
    {
        return $this->hasMany(Ecriture::class);
    }
}