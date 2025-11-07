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
            [
                'nome' => 'Admin Exemplo',
                'username' => 'admin',
                'email' => 'admin@teste.com',
                'password' => Hash::make('password'),
                'bio' => 'Administrador do sistema',
                'papel' => 'administrador',
                'tema' => 'medieval',
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
                'tema' => 'fantasia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
