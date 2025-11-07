<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampanhasTableSeeder extends Seeder
{
    public function run(): void
    {
        // ==============================
        // Buscar IDs dos sistemas
        // ==============================
        $sistemas = DB::table('sistemas')->pluck('id', 'nome')->toArray();
        // Exemplo: $sistemas['D&D 5e'] => 1

        // ==============================
        // Buscar IDs dos usuários
        // ==============================
        $users = DB::table('users')->pluck('id', 'username')->toArray();
        // Exemplo: $users['jogador1'] => 2

        // ==============================
        // Criar campanhas
        // ==============================
        $campanhas = [
            [
                'nome' => 'Aventura Fantástica',
                'descricao' => 'Campanha de D&D 5e com personagens clássicos.',
                'sistema_id' => $sistemas['D&D 5e'],
                'criador_id' => $users['admin'], // Admin como mestre
                'status' => 'ativa',
                'privada' => false,
            ],
            [
                'nome' => 'Ordem Paranormal',
                'descricao' => 'Investigações sobrenaturais com mestres Kaiser e Joui.',
                'sistema_id' => $sistemas['Ordem Paranormal'],
                'criador_id' => $users['kaiser01'],
                'status' => 'ativa',
                'privada' => true,
            ],
            [
                'nome' => 'Caçada ao Dragão',
                'descricao' => 'Uma aventura épica em D&D 5e.',
                'sistema_id' => $sistemas['D&D 5e'],
                'criador_id' => $users['joui02'],
                'status' => 'ativa',
                'privada' => false,
            ],
            [
                'nome' => 'Pesadelos Cósmicos',
                'descricao' => 'Campanha de Call of Cthulhu com personagens icônicos.',
                'sistema_id' => $sistemas['Call of Cthulhu'],
                'criador_id' => $users['kaiser01'],
                'status' => 'ativa',
                'privada' => true,
            ],
        ];

        $campanhaIds = [];
        foreach ($campanhas as $campanha) {
            $campanhaId = DB::table('campanhas')->insertGetId(array_merge($campanha, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $campanhaIds[$campanha['nome']] = $campanhaId;
        }

        // ==============================
        // Associar jogadores às campanhas
        // ==============================
        $campanhaJogadores = [
            'Aventura Fantástica' => ['jogador1', 'filhian03', 'mordomo04', 'shrek05'],
            'Ordem Paranormal' => ['kaiser01', 'joui02'],
            'Caçada ao Dragão' => ['filhian03', 'mordomo04', 'shrek05'],
            'Pesadelos Cósmicos' => ['sans06', 'maskara07', 'spiderman08', 'deadpool09', 'lobo10'],
        ];

        foreach ($campanhaJogadores as $nomeCampanha => $jogadores) {
            $campanhaId = $campanhaIds[$nomeCampanha];
            foreach ($jogadores as $username) {
                DB::table('campanha_usuario')->insert([
                    'campanha_id' => $campanhaId,
                    'user_id' => $users[$username],
                    'status' => 'ativo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
