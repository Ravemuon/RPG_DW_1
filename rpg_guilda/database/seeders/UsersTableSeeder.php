<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // Usuários iniciais
            [
                'nome' => 'Admin Exemplo',
                'username' => 'admin',
                'email' => 'admin@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Administrador do sistema',
                'papel' => 'administrador',
                'tema' => 'cyberpunk', // válido no enum
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Jogador Exemplo',
                'username' => 'jogador1',
                'email' => 'jogador@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Jogador teste',
                'papel' => 'jogador',
                'tema' => 'sobrenatural',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mestres da Ordem Paranormal
            [
                'nome' => 'Kaiser',
                'username' => 'kaiser01',
                'email' => 'kaiser@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Mestre da Ordem Paranormal',
                'papel' => 'mestre',
                'tema' => 'sobrenatural',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Joui',
                'username' => 'joui02',
                'email' => 'joui@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Mestre da Ordem Paranormal',
                'papel' => 'mestre',
                'tema' => 'fantasia',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Jogadores de D&D
            [
                'nome' => 'Filhian',
                'username' => 'filhian03',
                'email' => 'filhian@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Jogador D&D',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Mordomo Menta',
                'username' => 'mordomo04',
                'email' => 'mordomo@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Jogador D&D',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Shrek',
                'username' => 'shrek05',
                'email' => 'shrek@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Jogador D&D',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Investigadores Call of Cthulhu / memes
            [
                'nome' => 'Sans',
                'username' => 'sans06',
                'email' => 'sans@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Investigador Call of Cthulhu',
                'papel' => 'jogador',
                'tema' => 'oceano',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Máskara',
                'username' => 'maskara07',
                'email' => 'maskara@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Investigador Call of Cthulhu',
                'papel' => 'jogador',
                'tema' => 'sobrenatural',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Homem Aranha',
                'username' => 'spiderman08',
                'email' => 'spiderman@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Investigador Call of Cthulhu',
                'papel' => 'jogador',
                'tema' => 'sobrenatural',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Deadpool',
                'username' => 'deadpool09',
                'email' => 'deadpool@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Investigador Call of Cthulhu',
                'papel' => 'jogador',
                'tema' => 'sobrenatural',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Lobo',
                'username' => 'lobo10',
                'email' => 'lobo@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Investigador Call of Cthulhu',
                'papel' => 'jogador',
                'tema' => 'sobrenatural',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
