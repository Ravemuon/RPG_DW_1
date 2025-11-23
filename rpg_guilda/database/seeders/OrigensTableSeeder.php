<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origem;
use App\Models\Sistema;

class OrigensTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = Sistema::all();

        foreach ($sistemas as $sistema) {

            $origens = [];

            /*
            |--------------------------------------------------------------------------
            | ORIGENS PARA D&D 5e
            |--------------------------------------------------------------------------
            */
            if ($sistema->nome === 'D&D 5e') {
                $origens = [
                    [
                        'nome' => 'Aldeão',
                        'descricao' => 'Criado em uma vila simples, habituado ao trabalho braçal e rotina rústica.',
                        'pagina' => 'PHB 123'
                    ],
                    [
                        'nome' => 'Nobre',
                        'descricao' => 'Pertencente a uma família influente, treinado em etiqueta e política.',
                        'pagina' => 'PHB 135'
                    ],
                    [
                        'nome' => 'Criminoso',
                        'descricao' => 'Cresceu entre ladrões, contrabandistas e marginais.',
                        'pagina' => 'PHB 129'
                    ],
                    [
                        'nome' => 'Artista',
                        'descricao' => 'Viajante ou intérprete, acostumado ao encanto das apresentações.',
                        'pagina' => 'PHB 134'
                    ],
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | ORIGENS PARA ORDEM PARANORMAL
            |--------------------------------------------------------------------------
            */
            if ($sistema->nome === 'Ordem Paranormal') {
                $origens = [
                    [
                        'nome' => 'Acadêmico',
                        'descricao' => 'Estudioso dedicado que passou anos em bibliotecas e laboratórios.',
                        'pagina' => 'OP - Criação 42'
                    ],
                    [
                        'nome' => 'Agente da ORDO',
                        'descricao' => 'Profissional treinado para lidar diretamente com o paranormal.',
                        'pagina' => 'OP - Criação 77'
                    ],
                    [
                        'nome' => 'Investigador Independente',
                        'descricao' => 'Pessoa que decidiu por conta própria enfrentar o desconhecido.',
                        'pagina' => 'OP - Criação 55'
                    ],
                    [
                        'nome' => 'Médico',
                        'descricao' => 'Formado em medicina, acostumado a lidar com ferimentos e emergências.',
                        'pagina' => 'OP - Criação 61'
                    ],
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | ORIGENS PARA CALL OF CTHULHU
            |--------------------------------------------------------------------------
            */
            if ($sistema->nome === 'Call of Cthulhu') {
                $origens = [
                    [
                        'nome' => 'Detetive',
                        'descricao' => 'Investigador experiente, habituado a interrogar e estudar pistas.',
                        'pagina' => 'CoC 48'
                    ],
                    [
                        'nome' => 'Professor',
                        'descricao' => 'Acadêmico com profundo conhecimento teórico em diversas áreas.',
                        'pagina' => 'CoC 50'
                    ],
                    [
                        'nome' => 'Artista',
                        'descricao' => 'Sensível e intuitivo, capaz de perceber nuances que outros ignoram.',
                        'pagina' => 'CoC 53'
                    ],
                    [
                        'nome' => 'Militar',
                        'descricao' => 'Soldado treinado, experiente em combate e sobrevivência.',
                        'pagina' => 'CoC 61'
                    ],
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | ORIGENS PARA QUALQUER SISTEMA (GENÉRICAS)
            |--------------------------------------------------------------------------
            */
            if (empty($origens)) {
                $origens = [
                    [
                        'nome' => 'Sobrevivente',
                        'descricao' => 'Cresceu em meio ao caos e aprendeu a se virar sozinho.',
                        'pagina' => null
                    ],
                    [
                        'nome' => 'Mercador',
                        'descricao' => 'Negociador habilidoso, sempre em busca de oportunidades.',
                        'pagina' => null
                    ],
                    [
                        'nome' => 'Viajante',
                        'descricao' => 'Aventureiro que rodou o mundo em busca de histórias e conhecimento.',
                        'pagina' => null
                    ],
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | INSERE / ATUALIZA ORIGENS NO BANCO
            |--------------------------------------------------------------------------
            */
            foreach ($origens as $origem) {
                Origem::updateOrCreate(
                    [
                        'nome'       => $origem['nome'],
                        'sistema_id' => $sistema->id,
                    ],
                    [
                        'descricao'  => $origem['descricao'],
                        'pagina'     => $origem['pagina'],
                        'sistema_id' => $sistema->id,
                    ]
                );
            }
        }
    }
}
