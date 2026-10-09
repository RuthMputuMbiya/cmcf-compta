<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grand_livre', function (Blueprint $table) {
            $table->id();
            $table->string('compte', 20);
            $table->decimal('solde_initial', 15, 2)->default(0);
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->decimal('solde_final', 15, 2)->default(0);
            $table->date('date_arrete');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('grand_livre'); }
};