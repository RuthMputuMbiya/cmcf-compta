<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance', function (Blueprint $table) {
            $table->id();
            $table->string('compte', 20);
            $table->decimal('solde_debiteur', 15, 2)->default(0);
            $table->decimal('solde_crediteur', 15, 2)->default(0);
            $table->date('date_balance');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('balance'); }
};