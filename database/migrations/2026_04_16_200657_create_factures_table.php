<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->date('date');
            $table->foreignId('client_id')->constrained();
            $table->decimal('montant_ht', 15, 2);
            $table->decimal('tva', 15, 2)->default(0);
            $table->decimal('montant_ttc', 15, 2);
            $table->enum('statut', ['brouillon', 'payee', 'impayee'])->default('brouillon');
            $table->foreignId('cree_par')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('factures'); }
};