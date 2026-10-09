<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ecriture extends Model
{
    protected $table = 'ecritures';
    
    protected $fillable = [
        'numero_piece',
        'date',
        'libelle',
        'compte_debit',
        'montant_debit',
        'compte_credit',
        'montant_credit',
        'client_id',
        'fournisseur_id',
        'statut',
        'saisi_par',
        'valide_par',
        'valide_le'
    ];
    
    protected $casts = [
        'date' => 'date',
        'montant_debit' => 'decimal:2',
        'montant_credit' => 'decimal:2',
        'valide_le' => 'datetime'
    ];
    
    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    // Relation avec le fournisseur
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
    
    // Relation avec l'utilisateur qui a saisi l'écriture
    public function saisiPar()
    {
        return $this->belongsTo(User::class, 'saisi_par');
    }
    
    // Relation avec l'utilisateur qui a validé l'écriture
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
    
    // ============ NOUVELLES RELATIONS POUR LE GRAND LIVRE ============
    
    // Relation avec le compte débiteur (PlanComptable)
    public function compteDebiteur()
    {
        return $this->belongsTo(PlanComptable::class, 'compte_debit', 'numero');
    }
    
    // Relation avec le compte créditeur (PlanComptable)
    public function compteCrediteur()
    {
        return $this->belongsTo(PlanComptable::class, 'compte_credit', 'numero');
    }
    
    // ============ ACCESSOIRES ============
    
    // Vérifier si l'écriture est validée
    public function isValide()
    {
        return $this->statut === 'valide';
    }
    
    // Vérifier si l'écriture est un brouillon
    public function isBrouillon()
    {
        return $this->statut === 'brouillon';
    }
    
    // Obtenir le libellé complet de l'écriture
    public function getLibelleCompletAttribute()
    {
        return $this->numero_piece . ' - ' . $this->libelle;
    }
    
    // Obtenir le montant formaté
    public function getMontantDebitFormateAttribute()
    {
        return number_format($this->montant_debit, 0, ',', ' ') . ' FCFA';
    }
    
    public function getMontantCreditFormateAttribute()
    {
        return number_format($this->montant_credit, 0, ',', ' ') . ' FCFA';
    }
}