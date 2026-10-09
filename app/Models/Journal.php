<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $table = 'journal';
    
    protected $fillable = [
        'date',
        'compte',
        'libelle',
        'debit',
        'credit',
        'reference',
        'ecriture_id'
    ];
    
    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2'
    ];
    
    public function ecriture()
    {
        return $this->belongsTo(Ecriture::class);
    }
}