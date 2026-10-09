<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SEUL L'ADMINISTRATEUR EST DANS LE SEEDER
        DB::table('users')->insert([
            'matricule' => 'AD001',
            'name' => 'Admin',
            'prenom' => 'Système',
            'email' => 'ruthmputu13@gmail.com',
            'telephone' => '+243 997 185 648',
            'adresse' => 'Lubumbashi',
            'password' => Hash::make('admin123'),
            'role_id' => 6, // Administrateur
            'actif' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
        ]);
    }
}