<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [

            // =====================
            // ADMINISTRADOR
            // =====================
            [
                'nome' => 'Administrador Geral',
                'username' => 'admin',
                'email' => 'admin@teste.com',
                'papel' => 'administrador',
                'tema' => 'cyberpunk',
                'bio' => 'Responsável pela Guilda.',
                'password' => Hash::make('admin123'),
            ],
            [
                'nome' => 'Joador Teste',
                'username' => 'User',
                'email' => 'jogador@teste.com',
                'papel' => 'jogador',
                'tema' => 'medieval',
                'bio' => 'Responsável pela Guilda.',
                'password' => Hash::make('jogador'),
            ],

            // =====================
            // MESTRES
            // =====================
            [
                'nome' => 'Marcus Steelblade',
                'username' => 'mestre_marcus',
                'email' => 'marcus@example.com',
                'papel' => 'mestre',
                'tema' => 'fantasia',
                'bio' => 'Narrador experiente de mundos fantásticos.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Evelyn Ravenspell',
                'username' => 'mestre_evelyn',
                'email' => 'evelyn@example.com',
                'papel' => 'mestre',
                'tema' => 'sobrenatural',
                'bio' => 'Exploradora de histórias sombrias e ocultistas.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Dorian Gearsmith',
                'username' => 'mestre_dorian',
                'email' => 'dorian@example.com',
                'papel' => 'mestre',
                'tema' => 'steampunk',
                'bio' => 'Inventor e narrador do caos mecânico.',
                'password' => Hash::make('senha123'),
            ],

            // =====================
            // JOGADORES
            // =====================
            [
                'nome' => 'Luna Fairwind',
                'username' => 'luna_fw',
                'email' => 'luna@example.com',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'bio' => 'Aventureira apaixonada por magia.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Rafael Hawthorne',
                'username' => 'rafael_h',
                'email' => 'rafael@example.com',
                'papel' => 'jogador',
                'tema' => 'sobrenatural',
                'bio' => 'Caçador de criaturas desconhecidas.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Ayla Stormborn',
                'username' => 'ayla_storm',
                'email' => 'ayla@example.com',
                'papel' => 'jogador',
                'tema' => 'medieval',
                'bio' => 'Espadachim em busca de glória.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Theo Redcliff',
                'username' => 'theo_r',
                'email' => 'theo@example.com',
                'papel' => 'jogador',
                'tema' => 'apocaliptico',
                'bio' => 'Sobrevivente nato do caos.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Nina Greymoon',
                'username' => 'nina_gm',
                'email' => 'nina@example.com',
                'papel' => 'jogador',
                'tema' => 'oceano',
                'bio' => 'Exploradora dos mares profundos.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Oliver Brightshield',
                'username' => 'oliver_bs',
                'email' => 'oliver@example.com',
                'papel' => 'jogador',
                'tema' => 'medieval',
                'bio' => 'Escudeiro determinado a se provar.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Maya Emberfall',
                'username' => 'maya_ember',
                'email' => 'maya@example.com',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'bio' => 'Feiticeira aprendiz com grande potencial.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Damian Crowscar',
                'username' => 'damian_cs',
                'email' => 'damian@example.com',
                'papel' => 'jogador',
                'tema' => 'sobrenatural',
                'bio' => 'Detective de fenômenos incomuns.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Seraphine Vale',
                'username' => 'sera_vale',
                'email' => 'seraphine@example.com',
                'papel' => 'jogador',
                'tema' => 'floresta',
                'bio' => 'Arqueira protetora da natureza.',
                'password' => Hash::make('senha123'),
            ],
            [
                'nome' => 'Kai Ironshade',
                'username' => 'kai_iron',
                'email' => 'kai@example.com',
                'papel' => 'jogador',
                'tema' => 'steampunk',
                'bio' => 'Aventureiro urbano amante de máquinas.',
                'password' => Hash::make('senha123'),
            ],
        ];

        // =====================
        // INSERIR OU ATUALIZAR
        // =====================
        foreach ($usuarios as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                $user
            );
        }
    }
}
