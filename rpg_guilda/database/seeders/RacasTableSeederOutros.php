<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Raca;
use App\Models\Sistema;

class RacasTableSeederOutros extends Seeder
{
    public function run(): void
    {
        $sistemas = Sistema::whereIn('nome', ['Ordem Paranormal', 'Call of Cthulhu'])->get()->keyBy('nome');

        $atributosPorSistema = [
            'Ordem Paranormal' => ['forca', 'agilidade', 'intelecto', 'percepcao', 'vontade', 'carisma'],
            'Call of Cthulhu' => ['forca', 'destreza', 'constituicao', 'inteligencia', 'poder', 'carisma'],
        ];

        $racasData = [
            'Ordem Paranormal' => [
                ['nome' => 'Humano', 'descricao' => 'Investigadores de fenômenos sobrenaturais.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => null],
            ],
            'Call of Cthulhu' => [
                ['nome' => 'Humano', 'descricao' => 'Investigadores enfrentando horrores cósmicos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => null],
            ],
        ];

        foreach ($racasData as $sistemaNome => $racas) {
            $sistema = $sistemas->get($sistemaNome);
            if (!$sistema) {
                $this->command->error("Sistema {$sistemaNome} não encontrado.");
                continue;
            }

            foreach ($racas as $raca) {
                $raca['sistema_id'] = $sistema->id;
                $modificadores = array_fill_keys($atributosPorSistema[$sistemaNome], 0);
                $raca['modificadores_atributos'] = json_encode($modificadores);

                Raca::updateOrCreate(
                    ['nome' => $raca['nome'], 'sistema_id' => $sistema->id],
                    $raca
                );
            }
        }

        $this->command->info('Raças de outros sistemas populadas!');
    }
}
