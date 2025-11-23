<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Raca;

class RacasTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistemaId = 1;
        $racas = [
            [
                'nome' => 'Humano',
                'descricao' => 'Versáteis e adaptáveis, humanos recebem bônus equilibrados.',
                'forca_bonus' => 1,
                'destreza_bonus' => 1,
                'constituicao_bonus' => 1,
                'inteligencia_bonus' => 1,
                'sabedoria_bonus' => 1,
                'carisma_bonus' => 1,
                'tipo_bonus' => 'flat',
                'bonus_livre' => 0,
                'pagina' => 'PHB 29',
                'sistema_id' => $sistemaId,
            ],
            [
                'nome' => 'Elfo',
                'descricao' => 'Conhecidos por sua graça, agilidade e forte conexão mística.',
                'forca_bonus' => 0,
                'destreza_bonus' => 2,
                'constituicao_bonus' => 0,
                'inteligencia_bonus' => 1,
                'sabedoria_bonus' => 0,
                'carisma_bonus' => 0,
                'tipo_bonus' => 'flat',
                'bonus_livre' => 0,
                'pagina' => 'PHB 21',
                'sistema_id' => $sistemaId,
            ],
            [
                'nome' => 'Anão',
                'descricao' => 'Robustos, resistentes e com uma longa tradição guerreira.',
                'forca_bonus' => 0,
                'destreza_bonus' => 0,
                'constituicao_bonus' => 2,
                'inteligencia_bonus' => 0,
                'sabedoria_bonus' => 1,
                'carisma_bonus' => 0,
                'tipo_bonus' => 'flat',
                'bonus_livre' => 0,
                'pagina' => 'PHB 17',
                'sistema_id' => $sistemaId,
            ],
            [
                'nome' => 'Tiefling',
                'descricao' => 'Descendentes de linhagens infernais, dotados de carisma e magia.',
                'forca_bonus' => 0,
                'destreza_bonus' => 0,
                'constituicao_bonus' => 0,
                'inteligencia_bonus' => 1,
                'sabedoria_bonus' => 0,
                'carisma_bonus' => 2,
                'tipo_bonus' => 'flat',
                'bonus_livre' => 0,
                'pagina' => 'PHB 42',
                'sistema_id' => $sistemaId,
            ],
            [
                'nome' => 'Meio-Orc',
                'descricao' => 'Fortes, intimidadoras e incríveis em combate corpo a corpo.',
                'forca_bonus' => 2,
                'destreza_bonus' => 0,
                'constituicao_bonus' => 1,
                'inteligencia_bonus' => 0,
                'sabedoria_bonus' => 0,
                'carisma_bonus' => 0,
                'tipo_bonus' => 'flat',
                'bonus_livre' => 0,
                'pagina' => 'PHB 41',
                'sistema_id' => $sistemaId,
            ],
            [
                'nome' => 'Personalizada',
                'descricao' => 'Raça customizada para sistemas que permitem escolhas livres.',
                'forca_bonus' => 0,
                'destreza_bonus' => 0,
                'constituicao_bonus' => 0,
                'inteligencia_bonus' => 0,
                'sabedoria_bonus' => 0,
                'carisma_bonus' => 0,
                'tipo_bonus' => 'escolha',
                'bonus_livre' => 2,
                'pagina' => null,
                'sistema_id' => $sistemaId,
            ],
        ];

        foreach ($racas as $raca) {
            Raca::updateOrCreate(
                ['nome' => $raca['nome'], 'sistema_id' => $sistemaId],
                $raca
            );
        }
    }
}
