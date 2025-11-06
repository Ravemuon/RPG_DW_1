<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pericia;

class PericiasSeeder extends Seeder
{
    public function run(): void
    {
        $pericias = [
            // ===== D&D =====
            [
                'nome' => 'Acrobacia',
                'sistemaRPG' => 'D&D',
                'automatica' => true,
                'formula' => json_encode(['destreza' => 1]),
                'descricao' => 'Testa a destreza em movimentos acrobáticos',
            ],
            [
                'nome' => 'Arcanismo',
                'sistemaRPG' => 'D&D',
                'automatica' => true,
                'formula' => json_encode(['inteligencia' => 1]),
                'descricao' => 'Conhecimento de magias e fenômenos arcanos',
            ],
            [
                'nome' => 'Intimidação',
                'sistemaRPG' => 'D&D',
                'automatica' => true,
                'formula' => json_encode(['carisma' => 1]),
                'descricao' => 'Tentar amedrontar alguém',
            ],
            // Adicione todas as perícias de D&D aqui

            // ===== Ordem Paranormal =====
            [
                'nome' => 'Percepção',
                'sistemaRPG' => 'Ordem Paranormal',
                'automatica' => true,
                'formula' => json_encode(['intelecto' => 1, 'presenca' => 1]),
                'descricao' => 'Capacidade de perceber o ambiente e detectar anomalias',
            ],
            [
                'nome' => 'Investigação',
                'sistemaRPG' => 'Ordem Paranormal',
                'automatica' => true,
                'formula' => json_encode(['intelecto' => 1]),
                'descricao' => 'Investigar pistas e evidências',
            ],
            // Adicione mais perícias do sistema

            // ===== Call of Cthulhu =====
            [
                'nome' => 'Biblioteca',
                'sistemaRPG' => 'Call of Cthulhu',
                'automatica' => true,
                'formula' => json_encode(['educacao' => 1]),
                'descricao' => 'Buscar conhecimento em livros e documentos',
            ],
            [
                'nome' => 'Ocultismo',
                'sistemaRPG' => 'Call of Cthulhu',
                'automatica' => true,
                'formula' => json_encode(['poder' => 1, 'inteligencia_cth' => 1]),
                'descricao' => 'Conhecimento sobre magia e rituais ocultos',
            ],

            // ===== Fate Core =====
            [
                'nome' => 'Luta',
                'sistemaRPG' => 'Fate Core',
                'automatica' => false,
                'formula' => null,
                'descricao' => 'Perícia definida pelo jogador',
            ],
            [
                'nome' => 'Furtividade',
                'sistemaRPG' => 'Fate Core',
                'automatica' => false,
                'formula' => null,
                'descricao' => 'Perícia definida pelo jogador',
            ],

            // ===== Cypher System / Apocalypse World / Cyberpunk 2093 =====
            [
                'nome' => 'Tecnologia',
                'sistemaRPG' => 'Cyberpunk 2093 - Arkana-RPG',
                'automatica' => false,
                'formula' => null,
                'descricao' => 'Perícia definida pelo jogador',
            ],
            [
                'nome' => 'Pilotagem',
                'sistemaRPG' => 'Apocalypse World',
                'automatica' => false,
                'formula' => null,
                'descricao' => 'Perícia definida pelo jogador',
            ],
        ];

        foreach ($pericias as $p) {
            Pericia::updateOrCreate(
                ['nome' => $p['nome'], 'sistemaRPG' => $p['sistemaRPG']],
                [
                    'automatica' => $p['automatica'],
                    'formula' => $p['formula'],
                    'descricao' => $p['descricao'],
                ]
            );
        }

        $this->command->info('Seeder de perícias executado com sucesso!');
    }
}
