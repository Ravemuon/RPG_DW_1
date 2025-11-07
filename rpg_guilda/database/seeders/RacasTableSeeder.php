<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Raca;
use App\Models\Sistema;

class RacasTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = Sistema::all();

        foreach ($sistemas as $sistema) {
            // Raças comuns para todos os sistemas
            $racas = [
                ['nome' => 'Humano', 'descricao' => 'Pessoa comum, versátil.'],
                ['nome' => 'Monstro', 'descricao' => 'Criatura poderosa ou sobrenatural.'],
            ];

            // Raças específicas para D&D
            if ($sistema->nome === 'D&D 5e') {
                $racas[] = ['nome' => 'Elfo', 'descricao' => 'Seres mágicos com grande destreza.'];
                $racas[] = ['nome' => 'Anão', 'descricao' => 'Robustos e resistentes.'];
            }

            // Raças específicas para Call of Cthulhu
            if ($sistema->nome === 'Call of Cthulhu') {
                $racas[] = ['nome' => 'Investigador', 'descricao' => 'Pessoa comum em busca de mistérios.'];
            }

            foreach ($racas as $raca) {
                Raca::firstOrCreate(
                    ['nome' => $raca['nome'], 'sistema_id' => $sistema->id],
                    [
                        'descricao' => $raca['descricao'],
                        'forca_bonus' => 0,
                        'destreza_bonus' => 0,
                        'constituicao_bonus' => 0,
                        'inteligencia_bonus' => 0,
                        'sabedoria_bonus' => 0,
                        'carisma_bonus' => 0,
                    ]
                );
            }
        }
    }
}
