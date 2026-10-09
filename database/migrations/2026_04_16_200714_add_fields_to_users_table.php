<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('matricule')->unique()->after('id');
            $table->string('prenom')->after('name');
            $table->string('telephone')->after('email');
            $table->string('adresse')->nullable()->after('telephone');
            $table->foreignId('role_id')->after('adresse')->constrained('roles');
            $table->boolean('actif')->default(true);
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['matricule', 'prenom', 'telephone', 'adresse', 'actif']);
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};