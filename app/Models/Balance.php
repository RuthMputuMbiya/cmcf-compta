<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'balance';
    
    protected $fillable = [
        'compte',
        'solde_debiteur',
        'solde_crediteur',
        'date_balance'
    ];
    
    protected $casts = [
        'solde_debiteur' => 'decimal:2',
        'solde_crediteur' => 'decimal:2',
        'date_balance' => 'date'
    ];
}