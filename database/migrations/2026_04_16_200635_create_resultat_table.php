<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultat', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('total_produits', 15, 2)->default(0);
            $table->decimal('total_charges', 15, 2)->default(0);
            $table->decimal('resultat_net', 15, 2)->default(0);
            $table->enum('type', ['benefice', 'perte'])->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('resultat'); }
};