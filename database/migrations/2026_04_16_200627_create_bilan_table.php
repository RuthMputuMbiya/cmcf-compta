<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bilan', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            // ACTIF
            $table->decimal('actif_immobilise', 15, 2)->default(0);
            $table->decimal('actif_circulant', 15, 2)->default(0);
            $table->decimal('tresorerie', 15, 2)->default(0);
            $table->decimal('total_actif', 15, 2)->default(0);
            // PASSIF
            $table->decimal('capitaux_propres', 15, 2)->default(0);
            $table->decimal('dettes', 15, 2)->default(0);
            $table->decimal('total_passif', 15, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('bilan'); }
};