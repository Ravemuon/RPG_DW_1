<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemasTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = [
            [
                'nome' => 'D&D 5e',
                'descricao' => 'Sistema de RPG de fantasia medieval com foco em narrativa e combate tático.',
                'foco' => 'Fantasia / Aventura',
                'mecanica_principal' => 'd20 + modificador',
                'complexidade' => 'Média',
                'regras_opcionais' => json_encode(['Multi-classes', 'Feitiços opcionais', 'Regra de morte opcional']),
                'max_atributos' => 6,
                'atributo1_nome' => 'Força',
                'atributo2_nome' => 'Destreza',
                'atributo3_nome' => 'Constituição',
                'atributo4_nome' => 'Inteligência',
                'atributo5_nome' => 'Sabedoria',
                'atributo6_nome' => 'Carisma',
                'pagina' => 'PHB'
            ],
            [
                'nome' => 'Pathfinder 2e',
                'descricao' => 'Sistema de RPG de fantasia inspirado em D&D, com regras detalhadas para combate e progressão.',
                'foco' => 'Fantasia / Estratégia',
                'mecanica_principal' => 'd20 + modificador',
                'complexidade' => 'Alta',
                'regras_opcionais' => json_encode(['Regras de herança', 'Aprimoramentos opcionais']),
                'max_atributos' => 6,
                'atributo1_nome' => 'Força',
                'atributo2_nome' => 'Destreza',
                'atributo3_nome' => 'Constituição',
                'atributo4_nome' => 'Inteligência',
                'atributo5_nome' => 'Sabedoria',
                'atributo6_nome' => 'Carisma',
                'pagina' => 'Core Rulebook'
            ],
            [
                'nome' => 'Savage Worlds',
                'descricao' => 'Sistema genérico de RPG rápido, focado em ação e narrativa cinematográfica.',
                'foco' => 'Aventura / Ação',
                'mecanica_principal' => 'Dados de múltiplos tipos (d4-d12)',
                'complexidade' => 'Baixa',
                'regras_opcionais' => json_encode(['Cartas de ação', 'Bennies']),
                'max_atributos' => 6,
                'atributo1_nome' => 'Agilidade',
                'atributo2_nome' => 'Astúcia',
                'atributo3_nome' => 'Força',
                'atributo4_nome' => 'Espírito',
                'atributo5_nome' => 'Vigor',
                'atributo6_nome' => 'Percepção',
                'pagina' => 'Manual Básico'
            ],
            [
                'nome' => 'GURPS',
                'descricao' => 'Sistema universal de RPG, adaptável a qualquer cenário e gênero.',
                'foco' => 'Universal / Estratégia',
                'mecanica_principal' => '3d6 + modificadores',
                'complexidade' => 'Alta',
                'regras_opcionais' => json_encode(['Pontos de personagem', 'Perícias detalhadas']),
                'max_atributos' => 6,
                'atributo1_nome' => 'Força',
                'atributo2_nome' => 'Destreza',
                'atributo3_nome' => 'Inteligência',
                'atributo4_nome' => 'Saúde',
                'atributo5_nome' => 'Carisma',
                'atributo6_nome' => 'Vontade',
                'pagina' => 'Core Rules'
            ],
            [
                'nome' => 'Call of Cthulhu',
                'descricao' => 'Sistema de horror investigativo, com foco em sanidade e investigação.',
                'foco' => 'Horror / Investigação',
                'mecanica_principal' => 'd100 / porcentagem',
                'complexidade' => 'Média',
                'regras_opcionais' => json_encode(['Sanidade opcional', 'Magia de Mythos']),
                'max_atributos' => 6,
                'atributo1_nome' => 'Força',
                'atributo2_nome' => 'Destreza',
                'atributo3_nome' => 'Constituição',
                'atributo4_nome' => 'Inteligência',
                'atributo5_nome' => 'Poder',
                'atributo6_nome' => 'Carisma',
                'pagina' => 'Manual Básico'
            ],
            [
                'nome' => 'Ordem Paranormal',
                'descricao' => 'Sistema de RPG brasileiro de investigação sobrenatural com foco em narrativa.',
                'foco' => 'Investigação / Sobrenatural',
                'mecanica_principal' => 'd6 + modificadores',
                'complexidade' => 'Baixa',
                'regras_opcionais' => json_encode(['Místicos', 'Itens sobrenaturais']),
                'max_atributos' => 6,
                'atributo1_nome' => 'Força',
                'atributo2_nome' => 'Agilidade',
                'atributo3_nome' => 'Intelecto',
                'atributo4_nome' => 'Percepção',
                'atributo5_nome' => 'Vontade',
                'atributo6_nome' => 'Carisma',
                'pagina' => 'Manual Básico'
            ]
        ];

        DB::table('sistemas')->insert($sistemas);
    }
}
