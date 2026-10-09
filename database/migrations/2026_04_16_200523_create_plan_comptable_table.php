<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_comptable', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->string('nom');
            $table->string('classe'); // 1-7
            $table->enum('type', ['Actif', 'Passif', 'Charge', 'Produit']);
            $table->decimal('solde_initial', 15, 2)->default(0);
            $table->decimal('solde_actuel', 15, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('plan_comptable'); }
};