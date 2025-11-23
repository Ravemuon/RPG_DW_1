<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonagemSeeder extends Seeder
{
    public function run(): void
    {
        // Carregando IDs existentes
        $users      = DB::table('users')->pluck('id', 'username')->toArray();
        $campanhas  = DB::table('campanhas')->pluck('id', 'nome')->toArray();
        $racas      = DB::table('racas')->pluck('id', 'nome')->toArray();
        $classes    = DB::table('classes')->pluck('id', 'nome')->toArray();
        $origens    = DB::table('origens')->pluck('id', 'nome')->toArray();
        $sistemas   = DB::table('sistemas')->pluck('id', 'nome')->toArray();

        // Função segura para pegar ID ou null
        $get = function($array, $key) {
            return $array[$key] ?? null;
        };

        // Função para pegar primeiro valor caso não exista o nome
        $fallback = function($array) {
            return $array ? array_values($array)[0] : null;
        };

        // Função para pegar ID do sistema pelo nome
        $sistema = function($sistemas, $nome) {
            foreach ($sistemas as $id => $n) {
                if ($n === $nome) return $id;
            }
            return null;
        };

        $personagens = [

            // ============================
            // D&D 5E — AVENTURA FANTÁSTICA
            // ============================
            [
                'nome'        => 'Ayla Stormblade',
                'user_id'     => $get($users, 'ayla_storm'),
                'campanha_id' => $get($campanhas, 'Aventura Fantástica'),
                'raca_id'     => $get($racas, 'Humano') ?? $fallback($racas),
                'classe_id'   => $get($classes, 'Guerreiro') ?? $fallback($classes),
                'origem_id'   => $get($origens, 'Aldeão') ?? $fallback($origens),
                'sistema_id'  => $sistema($sistemas, 'D&D 5e'),
                'atributos'   => json_encode([
                    'Força' => 16, 'Destreza' => 14, 'Constituição' => 15,
                    'Inteligência' => 10, 'Sabedoria' => 12, 'Carisma' => 11
                ]),
                'descricao'   => 'Uma espadachim talentosa em busca de glória.',
                'ativo'       => true,
            ],

            [
                'nome'        => 'Luna Fairwind',
                'user_id'     => $get($users, 'luna_fw'),
                'campanha_id' => $get($campanhas, 'Aventura Fantástica'),
                'raca_id'     => $get($racas, 'Elfo') ?? $fallback($racas),
                'classe_id'   => $get($classes, 'Mago') ?? $fallback($classes),
                'origem_id'   => $get($origens, 'Acadêmico') ?? $fallback($origens),
                'sistema_id'  => $sistema($sistemas, 'D&D 5e'),
                'atributos'   => json_encode([
                    'Força' => 8, 'Destreza' => 14, 'Constituição' => 12,
                    'Inteligência' => 18, 'Sabedoria' => 13, 'Carisma' => 12
                ]),
                'descricao'   => 'Feiticeira apaixonada por magia arcana.',
                'ativo'       => true,
            ],

            // ============================
            // ORDEM PARANORMAL
            // ============================
            [
                'nome'        => 'Damian Crowscar',
                'user_id'     => $get($users, 'damian_cs'),
                'campanha_id' => $get($campanhas, 'Ordem Paranormal – Ecos da Escuridão'),
                'raca_id'     => $get($racas, 'Humano') ?? $fallback($racas),
                'classe_id'   => $get($classes, 'Investigador') ?? $fallback($classes),
                'origem_id'   => $get($origens, 'Acadêmico') ?? $fallback($origens),
                'sistema_id'  => $sistema($sistemas, 'Ordem Paranormal'),
                'atributos'   => json_encode([
                    'Força' => 11, 'Agilidade' => 13, 'Intelecto' => 16,
                    'Percepção' => 15, 'Vontade' => 14, 'Carisma' => 10
                ]),
                'descricao'   => 'Detetive especializado em eventos sobrenaturais.',
                'ativo'       => true,
            ],

            [
                'nome'        => 'Nina Greymoon',
                'user_id'     => $get($users, 'nina_gm'),
                'campanha_id' => $get($campanhas, 'Ordem Paranormal – Ecos da Escuridão'),
                'raca_id'     => $get($racas, 'Humano') ?? $fallback($racas),
                'classe_id'   => $get($classes, 'Medium') ?? $fallback($classes),
                'origem_id'   => $get($origens, 'Artista') ?? $fallback($origens),
                'sistema_id'  => $sistema($sistemas, 'Ordem Paranormal'),
                'atributos'   => json_encode([
                    'Força' => 9, 'Agilidade' => 12, 'Intelecto' => 14,
                    'Percepção' => 17, 'Vontade' => 18, 'Carisma' => 13
                ]),
                'descricao'   => 'Sensitiva capaz de ouvir ecos do Outro Lado.',
                'ativo'       => true,
            ],

            // ============================
            // CALL OF CTHULHU
            // ============================
            [
                'nome'        => 'Theo Redcliff',
                'user_id'     => $get($users, 'theo_r'),
                'campanha_id' => $get($campanhas, 'Pesadelos Cósmicos'),
                'raca_id'     => $get($racas, 'Humano') ?? $fallback($racas),
                'classe_id'   => $get($classes, 'Investigador') ?? $fallback($classes),
                'origem_id'   => $get($origens, 'Acadêmico') ?? $fallback($origens),
                'sistema_id'  => $sistema($sistemas, 'Call of Cthulhu'),
                'atributos'   => json_encode([
                    'Força' => 11, 'Destreza' => 13, 'Constituição' => 10,
                    'Inteligência' => 15, 'Poder' => 12, 'Carisma' => 10
                ]),
                'descricao'   => 'Sobrevivente nato agora caçando horrores cósmicos.',
                'ativo'       => true,
            ],

            [
                'nome'        => 'Seraphine Vale',
                'user_id'     => $get($users, 'sera_vale'),
                'campanha_id' => $get($campanhas, 'Pesadelos Cósmicos'),
                'raca_id'     => $get($racas, 'Humano') ?? $fallback($racas),
                'classe_id'   => $get($classes, 'Explorador') ?? $fallback($classes),
                'origem_id'   => $get($origens, 'Artista') ?? $fallback($origens),
                'sistema_id'  => $sistema($sistemas, 'Call of Cthulhu'),
                'atributos'   => json_encode([
                    'Força' => 10, 'Destreza' => 15, 'Constituição' => 11,
                    'Inteligência' => 14, 'Poder' => 13, 'Carisma' => 12
                ]),
                'descricao'   => 'Arqueira agora enfrentando entidades cósmicas.',
                'ativo'       => true,
            ],
        ];

        // Filtra personagens inválidos antes de inserir
        $validos = array_filter($personagens, function ($p) {
            return
                $p['user_id'] &&
                $p['campanha_id'] &&
                $p['raca_id'] &&
                $p['classe_id'] &&
                $p['origem_id'] &&
                $p['sistema_id'];
        });

        foreach ($validos as &$p) {
            $p['created_at'] = now();
            $p['updated_at'] = now();
        }

        DB::table('personagens')->insert($validos);

        echo "✔ Personagens gerados: " . count($validos) . "\n";
        echo "⚠ Ignorados: " . (count($personagens) - count($validos)) . "\n";
    }
}
