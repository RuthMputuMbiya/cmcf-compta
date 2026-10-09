<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('compte', 20);
            $table->string('libelle');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('reference');
            $table->foreignId('ecriture_id')->constrained();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('journal'); }
};