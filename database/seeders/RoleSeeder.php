<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'nom' => 'Directeur General', 'code' => 'DR', 'description' => 'Consulte tous les rapports'],
            ['id' => 2, 'nom' => 'Directrice Adjointe', 'code' => 'DA', 'description' => 'Valide certaines opérations'],
            ['id' => 3, 'nom' => 'Comptable', 'code' => 'CO', 'description' => 'Saisit les écritures comptables'],
            ['id' => 4, 'nom' => 'Caissiere', 'code' => 'CA', 'description' => 'Gère la caisse'],
            ['id' => 5, 'nom' => 'Vendeur', 'code' => 'VE', 'description' => 'Gère les ventes'],
            ['id' => 6, 'nom' => 'Administrateur', 'code' => 'AD', 'description' => 'Gère le système'],
        ];
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']],
                ['nom' => $role['nom'], 'code' => $role['code'], 'description' => $role['description'], 'created_at' => now()]
            );
        }
    }
}