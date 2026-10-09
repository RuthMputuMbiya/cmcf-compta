<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecritures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_piece', 50)->unique();
            $table->date('date');
            $table->text('libelle');
            $table->string('compte_debit', 20);
            $table->decimal('montant_debit', 15, 2);
            $table->string('compte_credit', 20);
            $table->decimal('montant_credit', 15, 2);
            $table->foreignId('client_id')->nullable()->constrained();
            $table->foreignId('fournisseur_id')->nullable()->constrained();
            $table->enum('statut', ['brouillon', 'valide'])->default('brouillon');
            $table->foreignId('saisi_par')->constrained('users');
            $table->foreignId('valide_par')->nullable()->constrained('users');
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ecritures'); }
};