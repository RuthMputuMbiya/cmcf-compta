<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('action');
            $table->string('table')->nullable();
            $table->text('details')->nullable();
            $table->string('ip');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('logs'); }
};