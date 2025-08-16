<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un admin par défaut
        User::create([
            'name' => 'Administrateur Root',
            'email' => 'root@teamtask.com',
            'password' => Hash::make('root123456'), // À changer après la première connexion
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        // Créer quelques utilisateurs de test
        User::factory()->count(10)->create();
    }
}
