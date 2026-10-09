<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bilan extends Model
{
    protected $table = 'bilan';
    
    protected $fillable = [
        'date',
        'actif_immobilise',
        'actif_circulant',
        'tresorerie',
        'total_actif',
        'capitaux_propres',
        'dettes',
        'total_passif'
    ];
    
    protected $casts = [
        'date' => 'date',
        'actif_immobilise' => 'decimal:2',
        'actif_circulant' => 'decimal:2',
        'tresorerie' => 'decimal:2',
        'total_actif' => 'decimal:2',
        'capitaux_propres' => 'decimal:2',
        'dettes' => 'decimal:2',
        'total_passif' => 'decimal:2'
    ];
}