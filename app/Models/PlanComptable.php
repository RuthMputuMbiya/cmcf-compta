<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanComptable extends Model
{
    protected $table = 'plan_comptable';
    
    protected $fillable = [
        'numero',
        'nom',
        'classe',
        'type',
        'solde_initial',
        'solde_actuel'
    ];
    
    protected $casts = [
        'solde_initial' => 'decimal:2',
        'solde_actuel' => 'decimal:2'
    ];
}