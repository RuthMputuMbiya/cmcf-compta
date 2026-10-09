<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrandLivre extends Model
{
    protected $table = 'grand_livre';
    
    protected $fillable = [
        'compte',
        'solde_initial',
        'total_debit',
        'total_credit',
        'solde_final',
        'date_arrete'
    ];
    
    protected $casts = [
        'solde_initial' => 'decimal:2',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'solde_final' => 'decimal:2',
        'date_arrete' => 'date'
    ];
}