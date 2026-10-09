<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultat extends Model
{
    protected $table = 'resultat';
    
    protected $fillable = [
        'date',
        'chiffre_affaires',
        'autres_produits',
        'total_produits',
        'achats',
        'charges_personnel',
        'charges_externes',
        'dotations_amortissements',
        'autres_charges',
        'total_charges',
        'resultat_net',
        'type'
    ];
    
    protected $casts = [
        'date' => 'date',
        'chiffre_affaires' => 'decimal:2',
        'autres_produits' => 'decimal:2',
        'total_produits' => 'decimal:2',
        'achats' => 'decimal:2',
        'charges_personnel' => 'decimal:2',
        'charges_externes' => 'decimal:2',
        'dotations_amortissements' => 'decimal:2',
        'autres_charges' => 'decimal:2',
        'total_charges' => 'decimal:2',
        'resultat_net' => 'decimal:2'
    ];
}