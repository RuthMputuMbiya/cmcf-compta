<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $table = 'fournisseurs';
    
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
    ];
    
    // Relation avec les écritures
    public function ecritures()
    {
        return $this->hasMany(Ecriture::class);
    }
}