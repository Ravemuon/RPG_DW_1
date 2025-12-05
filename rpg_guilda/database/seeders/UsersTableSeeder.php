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
                'tema' => 'steampunk',
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
            // =====================
            // PERSONAGENS DE ANIME
            // =====================
            [
                'nome' => 'Goku',
                'username' => 'goku_ssj',
                'email' => 'goku@anime.com',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'bio' => 'Saiyajin sempre buscando se superar.',
                'password' => Hash::make('dragonball'),
                'avatar' => '/imagens/anime/goku_avatar.png',
                'banner' => '/imagens/anime/goku_banner.png',
            ],
            [
                'nome' => 'Naruto Uzumaki',
                'username' => 'naruto_ninja',
                'email' => 'naruto@anime.com',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'bio' => 'Ninja da Vila da Folha com sonhos grandes.',
                'password' => Hash::make('naruto'),
                'avatar' => '/imagens/anime/naruto_avatar.png',
                'banner' => '/imagens/anime/naruto_banner.png',
            ],
            [
                'nome' => 'Luffy',
                'username' => 'luffy_pirata',
                'email' => 'luffy@anime.com',
                'papel' => 'jogador',
                'tema' => 'oceano',
                'bio' => 'Pirata que busca o tesouro mais grandioso.',
                'password' => Hash::make('onepiece'),
                'avatar' => '/imagens/anime/luffy_avatar.png',
                'banner' => '/imagens/anime/luffy_banner.png',
            ],

            // =====================
            // PERSONAGENS DE DESENHOS
            // =====================
            [
                'nome' => 'Rick Sanchez',
                'username' => 'rick_morty',
                'email' => 'rick@desenho.com',
                'papel' => 'jogador',
                'tema' => 'cyberpunk',
                'bio' => 'Cientista louco que viaja entre dimensões.',
                'password' => Hash::make('rickandmorty'),
                'avatar' => '/imagens/desenho/rick_avatar.png',
                'banner' => '/imagens/desenho/rick_banner.png',
            ],
            [
                'nome' => 'Morty Smith',
                'username' => 'morty_nervoso',
                'email' => 'morty@desenho.com',
                'papel' => 'jogador',
                'tema' => 'cyberpunk',
                'bio' => 'Acompanhante relutante de aventuras perigosas.',
                'password' => Hash::make('morty123'),
                'avatar' => '/imagens/desenho/morty_avatar.png',
                'banner' => '/imagens/desenho/morty_banner.png',
            ],
            [
                'nome' => 'Finn',
                'username' => 'finn_adventure',
                'email' => 'finn@desenho.com',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'bio' => 'Aventureiro corajoso do mundo de OOO.',
                'password' => Hash::make('adventuretime'),
                'avatar' => '/imagens/desenho/finn_avatar.png',
                'banner' => '/imagens/desenho/finn_banner.png',
            ],
            [
                'nome' => 'Jake',
                'username' => 'jake_dog',
                'email' => 'jake@desenho.com',
                'papel' => 'jogador',
                'tema' => 'fantasia',
                'bio' => 'Cão com poderes elásticos, melhor amigo de Finn.',
                'password' => Hash::make('adventuretime'),
                'avatar' => '/imagens/desenho/jake_avatar.png',
                'banner' => '/imagens/desenho/jake_banner.png',
            ],

            // =====================
            // MEMES FAMOSOS
            // =====================
            [
                'nome' => 'Doge',
                'username' => 'doge_memes',
                'email' => 'doge@memes.com',
                'papel' => 'jogador',
                'tema' => 'deserto',
                'bio' => 'Muito wow, muito RPG, muito aventureiro.',
                'password' => Hash::make('doge123'),
                'avatar' => '/imagens/memes/doge_avatar.png',
                'banner' => '/imagens/memes/doge_banner.png',
            ],
            [
                'nome' => 'Pepe the Frog',
                'username' => 'pepe_frog',
                'email' => 'pepe@memes.com',
                'papel' => 'jogador',
                'tema' => 'floresta',
                'bio' => 'Frog triste ou feliz, sempre com estilo.',
                'password' => Hash::make('pepe123'),
                'avatar' => '/imagens/memes/pepe_avatar.png',
                'banner' => '/imagens/memes/pepe_banner.png',
            ],
            [
                'nome' => 'Shrek',
                'username' => 'shrek_ogro',
                'email' => 'shrek@memes.com',
                'papel' => 'jogador',
                'tema' => 'floresta',
                'bio' => 'O ogro favorito de todos, com temperamento forte.',
                'password' => Hash::make('shrek123'),
                'avatar' => '/imagens/memes/shrek_avatar.png',
                'banner' => '/imagens/memes/shrek_banner.png',
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
