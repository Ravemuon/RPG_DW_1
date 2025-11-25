<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origem;
use App\Models\Sistema;

class OrigensTableSeederOutros extends Seeder
{
    public function run(): void
    {
        $sistemas = Sistema::whereIn('nome', ['Ordem Paranormal', 'Call of Cthulhu'])->get()->keyBy('nome');

        $origensData = [
            'Ordem Paranormal' => [
                ['nome'=>'Acadêmico','descricao'=>'Estudioso dedicado, passou anos em bibliotecas e laboratórios.','pagina'=>'OP 42','bonus_pericias_data'=>['Ocultismo'=>1],'recursos_adicionais_data'=>['Poder de Origem'=>'Saber é Poder']],
                ['nome'=>'Agente da ORDO','descricao'=>'Treinado para lidar com o paranormal.','pagina'=>'OP 77','bonus_pericias_data'=>['Luta'=>1],'recursos_adicionais_data'=>['Poder de Origem'=>'Treinamento de Combate']],
                ['nome'=>'Investigador Independente','descricao'=>'Decidiu enfrentar o desconhecido por conta própria.','pagina'=>'OP 55','bonus_pericias_data'=>['Investigação'=>1],'recursos_adicionais_data'=>['Poder de Origem'=>'Vínculo com o Povo']],
                ['nome'=>'Médico','descricao'=>'Formado em medicina, acostumado a lidar com emergências.','pagina'=>'OP 61','bonus_pericias_data'=>['Medicina'=>1],'recursos_adicionais_data'=>['Poder de Origem'=>'Conhecimento de Anatomia']],
                ['nome'=>'Tecnólogo','descricao'=>'Especialista em equipamentos e ciências modernas.','pagina'=>'OP 65','bonus_pericias_data'=>['Tecnologia'=>1],'recursos_adicionais_data'=>['Poder de Origem'=>'Conhecimento Técnico Avançado']],
            ],
            'Call of Cthulhu' => [
                ['nome'=>'Detetive','descricao'=>'Investigador experiente, habituado a estudar pistas.','pagina'=>'CoC 48','bonus_pericias_data'=>['Investigar'=>10,'Ouvir'=>10],'recursos_adicionais_data'=>['Equipamento'=>'Lanterna, distintivo, revólver']],
                ['nome'=>'Professor','descricao'=>'Acadêmico com profundo conhecimento teórico.','pagina'=>'CoC 50','bonus_pericias_data'=>['História'=>15,'Contabilidade'=>10],'recursos_adicionais_data'=>['Equipamento'=>'Livros, óculos, papel']],
                ['nome'=>'Artista','descricao'=>'Sensível e intuitivo, capaz de perceber nuances que outros ignoram.','pagina'=>'CoC 53','bonus_pericias_data'=>['Encanto'=>10,'Primeiros Socorros'=>5],'recursos_adicionais_data'=>['Equipamento'=>'Ferramentas de arte, vestimentas finas']],
                ['nome'=>'Militar','descricao'=>'Soldado treinado, experiente em combate.','pagina'=>'CoC 61','bonus_pericias_data'=>['Luta (Soco/Chute)'=>15,'Dirigir Automóvel'=>10],'recursos_adicionais_data'=>['Equipamento'=>'Uniforme, faca de combate']],
                ['nome'=>'Jornalista','descricao'=>'Acostumado a investigar e publicar informações.','pagina'=>'CoC 65','bonus_pericias_data'=>['Persuasão'=>10,'Esconderijo'=>5],'recursos_adicionais_data'=>['Equipamento'=>'Caderno, caneta, máquina fotográfica']],
            ],
        ];

        foreach ($origensData as $sistemaNome => $origens) {
            $sistema = $sistemas->get($sistemaNome);

            if (!$sistema) {
                $this->command->error("Sistema {$sistemaNome} não encontrado.");
                continue;
            }

            foreach ($origens as $origem) {
                $bonusPericiasJson = json_encode($origem['bonus_pericias_data'] ?? []);
                unset($origem['bonus_pericias_data']);

                $recursosAdicionaisJson = json_encode($origem['recursos_adicionais_data'] ?? []);
                unset($origem['recursos_adicionais_data']);

                $origem['sistema_id'] = $sistema->id;

                Origem::updateOrCreate(
                    ['nome' => $origem['nome'], 'sistema_id' => $sistema->id],
                    [
                        'descricao' => $origem['descricao'],
                        'pagina' => $origem['pagina'],
                        'sistema_id' => $sistema->id,
                        'bonus_pericias' => $bonusPericiasJson,
                        'recursos_adicionais' => $recursosAdicionaisJson,
                    ]
                );
            }
        }

        $this->command->info('Origens de outros sistemas populadas!');
    }
}
