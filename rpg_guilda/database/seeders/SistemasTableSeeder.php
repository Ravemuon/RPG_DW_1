<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sistema;

class SistemasTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = [

            [
                'nome' => 'D&D 5e',
                'descricao' => 'Sistema de RPG de fantasia medieval.',
                'foco' => 'Fantasia / Aventura',
                'mecanica_principal' => 'd20',
                'complexidade' => 'Média',

                'atributos' => [
                    'forca' => 'Força',
                    'destreza' => 'Destreza',
                    'constituicao' => 'Constituição',
                    'inteligencia' => 'Inteligência',
                    'sabedoria' => 'Sabedoria',
                    'carisma' => 'Carisma',
                ],

                'usa_sanidade' => false,
                'formula_pontos_vida' => 'dado_da_classe + modificador_constituicao',

                'recursos' => [ 
                    ['nome' => 'Inspiração', 'max' => 1]
                ],

                'regras_opcionais' => ['Multi-classes', 'Regra de morte opcional'],
            ],

            [
                'nome' => 'Ordem Paranormal',
                'descricao' => 'Sistema brasileiro de investigação sobrenatural.',
                'foco' => 'Investigação / Sobrenatural',
                'mecanica_principal' => 'd6',
                'complexidade' => 'Baixa',

                'atributos' => [
                    'forca' => 'Força',
                    'agilidade' => 'Agilidade',
                    'intelecto' => 'Intelecto',
                    'percepcao' => 'Percepção',
                    'vontade' => 'Vontade',
                    'carisma' => 'Carisma',
                ],

                'usa_sanidade' => true,
                'formula_pontos_vida' => 'vida_por_classe + modificador_vontade',

                'recursos' => [
                    [
                        'nome' => 'NEX',
                        'escala' => '0-99',
                        'bonus_por_faixa' => true,
                    ]
                ],

                'regras_opcionais' => ['Itens sobrenaturais', 'Místicos'],
            ],

            [
                'nome' => 'Call of Cthulhu',
                'descricao' => 'Sistema investigativo de horror cósmico.',
                'foco' => 'Horror / Investigação',
                'mecanica_principal' => 'd100',
                'complexidade' => 'Média',

                'atributos' => [
                    'forca' => 'Força',
                    'destreza' => 'Destreza',
                    'constituicao' => 'Constituição',
                    'inteligencia' => 'Inteligência',
                    'poder' => 'Poder',
                    'carisma' => 'Carisma',
                ],

                'usa_sanidade' => true,
                'formula_pontos_vida' => '(constituicao + tamanho) / 10',

                'recursos' => [
                    ['nome' => 'Sorte', 'formula' => '3d6 * 5']
                ],

                'regras_opcionais' => ['Magia de Mythos'],
            ],

            [
                'nome' => 'Savage Worlds',
                'descricao' => 'Rápido, furioso e divertido.',
                'foco' => 'Ação / Aventura',
                'mecanica_principal' => 'd4-d12',
                'complexidade' => 'Baixa',

                'atributos' => [
                    'agilidade' => 'Agilidade',
                    'astucia' => 'Astúcia',
                    'espirito' => 'Espírito',
                    'forca' => 'Força',
                    'vigor' => 'Vigor',
                ],

                'usa_sanidade' => false,
                'formula_pontos_vida' => 'vigor + modificadores',

                'recursos' => [
                    ['nome' => 'Bennies', 'max' => 3]
                ],

                'regras_opcionais' => ['Cartas de ação'],
            ],

        ];

        foreach ($sistemas as $data) {

            // 🔥 Conversão obrigatória para JSON
            $data['atributos'] = json_encode($data['atributos']);
            $data['recursos'] = json_encode($data['recursos']);
            $data['regras_opcionais'] = json_encode($data['regras_opcionais']);

            Sistema::updateOrCreate(
                ['nome' => $data['nome']],
                $data
            );
        }
    }
}
