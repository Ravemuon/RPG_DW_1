<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Raca;
use App\Models\Sistema;

class RacasTableSeeder extends Seeder
{
    public function run(): void
    {
        // Busca o sistema D&D 5e
        $sistema = Sistema::where('nome', 'D&D 5e')->first();

        if (!$sistema) {
            $this->command->error("Sistema D&D 5e não encontrado. Execute primeiro o seeder de sistemas.");
            return;
        }

        // Dados das raças
        $racas = [
            ['nome' => 'Humano', 'descricao' => 'Versáteis e adaptáveis.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 29', 'modificadores' => ['forca'=>1,'destreza'=>1,'constituicao'=>1,'inteligencia'=>1,'sabedoria'=>1,'carisma'=>1]],
            ['nome' => 'Elfo', 'descricao' => 'Ágeis e místicos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 21', 'modificadores' => ['destreza'=>2]],
            ['nome' => 'Elfo Alto', 'descricao' => 'Inteligentes e talentosos em magia.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 22', 'modificadores' => ['destreza'=>2,'inteligencia'=>1]],
            ['nome' => 'Elfo da Floresta', 'descricao' => 'Ágeis e conectados com a natureza.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 22', 'modificadores' => ['destreza'=>2,'sabedoria'=>1]],
            ['nome' => 'Anão', 'descricao' => 'Robustos e resistentes.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 17', 'modificadores' => ['constituicao'=>2]],
            ['nome' => 'Anão da Colina', 'descricao' => 'Resistentes e com sabedoria.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 18', 'modificadores' => ['constituicao'=>2,'sabedoria'=>1]],
            ['nome' => 'Anão da Montanha', 'descricao' => 'Fortes e resistentes.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 18', 'modificadores' => ['constituicao'=>2,'forca'=>2]],
            ['nome' => 'Halfling', 'descricao' => 'Pequenos e sortudos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 21', 'modificadores' => ['destreza'=>2]],
            ['nome' => 'Halfling Leve', 'descricao' => 'Ágeis e sorrateiros.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 21', 'modificadores' => ['destreza'=>2,'carisma'=>1]],
            ['nome' => 'Halfling Robusto', 'descricao' => 'Fortes para sua estatura.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 21', 'modificadores' => ['destreza'=>2,'constituicao'=>1]],
            ['nome' => 'Meio-Orc', 'descricao' => 'Fortes e intimidadoras.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 41', 'modificadores' => ['forca'=>2,'constituicao'=>1]],
            ['nome' => 'Tiefling', 'descricao' => 'Descendentes de linhagens infernais.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 42', 'modificadores' => ['inteligencia'=>1,'carisma'=>2]],
            ['nome' => 'Gnomo', 'descricao' => 'Curiosos e inventivos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 35', 'modificadores' => ['inteligencia'=>2]],
            ['nome' => 'Gnomo da Floresta', 'descricao' => 'Astutos e discretos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 35', 'modificadores' => ['inteligencia'=>2,'destreza'=>1]],
            ['nome' => 'Gnomo das Rochas', 'descricao' => 'Inteligentes e resistentes.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 35', 'modificadores' => ['inteligencia'=>2,'constituicao'=>1]],
            ['nome' => 'Dragonato', 'descricao' => 'Orgulhosos e poderosos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'PHB 32', 'modificadores' => ['forca'=>2,'carisma'=>1]],
            ['nome' => 'Firbolg', 'descricao' => 'Conectados com a natureza e fortes.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'VGtM 15', 'modificadores' => ['forca'=>1,'sabedoria'=>2]],
            ['nome' => 'Tabaxi', 'descricao' => 'Felinos ágeis e curiosos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => 'VGtM 21', 'modificadores' => ['destreza'=>2,'carisma'=>1]],
        ];

        foreach ($racas as $racaData) {
            $racaData['sistema_id'] = $sistema->id;
            $racaData['modificadores_atributos'] = json_encode($racaData['modificadores']);
            unset($racaData['modificadores']);

            Raca::updateOrCreate(
                ['nome' => $racaData['nome'], 'sistema_id' => $sistema->id],
                $racaData
            );
        }

        $this->command->info('Raças D&D 5e populadas com modificadores de atributos.');
    }
}
