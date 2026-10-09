<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['entree', 'sortie']);
            $table->string('libelle');
            $table->decimal('montant', 15, 2);
            $table->string('reference')->nullable();
            $table->foreignId('enregistre_par')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('caisses'); }
};