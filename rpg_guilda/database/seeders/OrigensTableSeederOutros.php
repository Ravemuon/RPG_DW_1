<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origem;
use App\Models\Sistema;

class OrigensTableSeederOutros extends Seeder
{
    public function run(): void
    {
        $sistemas = Sistema::whereIn('nome', ['Ordem Paranormal', 'Call of Cthulhu'])
            ->get()
            ->keyBy('nome');
            
        $origens = [

        [
            'nome' => 'Acadêmico',
            'sistema_id' => 2,
            'descricao' => 'Estudioso dedicado, passou anos em bibliotecas e laboratórios.',
            'pagina' => 'OP 42',
            'bonus_pericias' => ['Ocultismo' => 1],
            'recursos_adicionais' => ['Poder de Origem' => 'Saber é Poder'],
        ],

        [
            'nome' => 'Inventor',
            'sistema_id' => 2,
            'descricao' => 'Criador de dispositivos engenhosos, mistura ciência e improviso.',
            'pagina' => 'OP 43',
            'bonus_pericias' => ['Tecnologia' => 1, 'Ofícios' => 1],
            'recursos_adicionais' => ['Gadget Inicial' => 'Ferramenta experimental única'],
        ],

        [
            'nome' => 'Mercador',
            'sistema_id' => 2,
            'descricao' => 'Especialista em negociação, rotas comerciais e obter o melhor preço.',
            'pagina' => 'OP 44',
            'bonus_pericias' => ['Diplomacia' => 1, 'Intuição' => 1],
            'recursos_adicionais' => ['Habilidade Especial' => 'Negociador Nato'],
        ],

        [
            'nome' => 'Sobrevivente',
            'sistema_id' => 2,
            'descricao' => 'Acostumado a situações extremas, aprendeu a sobreviver onde outros falham.',
            'pagina' => 'OP 45',
            'bonus_pericias' => ['Fortitude' => 1, 'Percepção' => 1],
            'recursos_adicionais' => ['Talento' => 'Sangue Frio'],
        ],

        [
            'nome' => 'Oráculo',
            'sistema_id' => 2,
            'descricao' => 'Portador de visões misteriosas e fragmentos do destino.',
            'pagina' => 'OP 46',
            'bonus_pericias' => ['Ocultismo' => 1, 'Intuição' => 1],
            'recursos_adicionais' => ['Dom' => 'Vislumbre do Futuro'],
        ],

        [
            'nome' => 'Andarilho',
            'sistema_id' => 2,
            'descricao' => 'Viajante de longas estradas, aprendeu sobre povos, rotas e perigos.',
            'pagina' => 'OP 47',
            'bonus_pericias' => ['Percepção' => 1, 'Investigação' => 1],
            'recursos_adicionais' => ['Equipamento' => 'Mapa gasto de um lugar desconhecido'],
        ],

        [
            'nome' => 'Perseguidor',
            'sistema_id' => 2,
            'descricao' => 'Treinado para caçar pessoas, monstros ou segredos.',
            'pagina' => 'OP 48',
            'bonus_pericias' => ['Furtividade' => 1, 'Investigação' => 1],
            'recursos_adicionais' => ['Técnica Especial' => 'Marca do Alvo'],
        ],

        [
            'nome' => 'Charlatão',
            'sistema_id' => 2,
            'descricao' => 'Mestre das mentiras, truques e manipulação.',
            'pagina' => 'OP 49',
            'bonus_pericias' => ['Enganação' => 1, 'Atuação' => 1],
            'recursos_adicionais' => ['Truque' => 'Talento para Gambiarras'],
        ],

        [
            'nome' => 'Herborista',
            'sistema_id' => 2,
            'descricao' => 'Conhecedor de plantas medicinais e venenos naturais.',
            'pagina' => 'OP 50',
            'bonus_pericias' => ['Natureza' => 1, 'Medicina' => 1],
            'recursos_adicionais' => ['Kit' => 'Bolsa de ervas'],
        ],

        [
            'nome' => 'Coveiro',
            'sistema_id' => 2,
            'descricao' => 'Habituado à morte, conhece segredos que deveriam permanecer soterrados.',
            'pagina' => 'OP 51',
            'bonus_pericias' => ['Fortitude' => 1, 'Investigação' => 1],
            'recursos_adicionais' => ['Segredo' => 'A Voz do Além'],
        ],

    ];

        $origensData = [
            'Ordem Paranormal' => [
                [
                    'nome'=>'Acadêmico',
                    'descricao'=>'Estudioso dedicado, passou anos em bibliotecas e laboratórios.',
                    'pagina'=>'OP 42',
                    'pericias_iniciais'=>['Ocultismo' => 1],
                    'recursos_adicionais'=>['Poder de Origem' => 'Saber é Poder']
                ],
                [
                    'nome'=>'Agente da ORDO',
                    'descricao'=>'Treinado para lidar com o paranormal.',
                    'pagina'=>'OP 77',
                    'pericias_iniciais'=>['Luta'=>1],
                    'recursos_adicionais'=>['Poder de Origem'=>'Treinamento de Combate']
                ],
                [
                    'nome'=>'Investigador Independente',
                    'descricao'=>'Decidiu enfrentar o desconhecido por conta própria.',
                    'pagina'=>'OP 55',
                    'pericias_iniciais'=>['Investigação'=>1],
                    'recursos_adicionais'=>['Poder de Origem'=>'Vínculo com o Povo']
                ],
                [
                    'nome'=>'Médico',
                    'descricao'=>'Formado em medicina, acostumado a lidar com emergências.',
                    'pagina'=>'OP 61',
                    'pericias_iniciais'=>['Medicina'=>1],
                    'recursos_adicionais'=>['Poder de Origem'=>'Conhecimento de Anatomia']
                ],
                [
                    'nome'=>'Tecnólogo',
                    'descricao'=>'Especialista em equipamentos e ciências modernas.',
                    'pagina'=>'OP 65',
                    'pericias_iniciais'=>['Tecnologia'=>1],
                    'recursos_adicionais'=>['Poder de Origem'=>'Conhecimento Técnico Avançado']
                ],
            ],

            'Call of Cthulhu' => [
                [
                    'nome'=>'Detetive',
                    'descricao'=>'Investigador experiente, habituado a estudar pistas.',
                    'pagina'=>'CoC 48',
                    'pericias_iniciais'=>['Investigar'=>10,'Ouvir'=>10],
                    'recursos_adicionais'=>['Equipamento'=>'Lanterna, distintivo, revólver']
                ],
                [
                    'nome'=>'Professor',
                    'descricao'=>'Acadêmico com profundo conhecimento teórico.',
                    'pagina'=>'CoC 50',
                    'pericias_iniciais'=>['História'=>15,'Contabilidade'=>10],
                    'recursos_adicionais'=>['Equipamento'=>'Livros, óculos, papel']
                ],
                [
                    'nome'=>'Artista',
                    'descricao'=>'Sensível e intuitivo.',
                    'pagina'=>'CoC 53',
                    'pericias_iniciais'=>['Encanto'=>10,'Primeiros Socorros'=>5],
                    'recursos_adicionais'=>['Equipamento'=>'Ferramentas de arte']
                ],
                [
                    'nome'=>'Militar',
                    'descricao'=>'Soldado treinado.',
                    'pagina'=>'CoC 61',
                    'pericias_iniciais'=>['Luta (Soco/Chute)'=>15,'Dirigir Automóvel'=>10],
                    'recursos_adicionais'=>['Equipamento'=>'Uniforme, faca']
                ],
                [
                    'nome'=>'Jornalista',
                    'descricao'=>'Acostumado a investigar e publicar.',
                    'pagina'=>'CoC 65',
                    'pericias_iniciais'=>['Persuasão'=>10,'Esconderijo'=>5],
                    'recursos_adicionais'=>['Equipamento'=>'Caderno e câmera']
                ],
            ],
        ];

        foreach ($origensData as $sistemaNome => $origens) {

            $sistema = $sistemas->get($sistemaNome);

            if (!$sistema) {
                $this->command->error("Sistema {$sistemaNome} não encontrado.");
                continue;
            }

            foreach ($origens as $origem) {
                Origem::updateOrCreate(
                    ['nome' => $origem['nome'], 'sistema_id' => $sistema->id],
                    [
                        'descricao' => $origem['descricao'],
                        'pagina' => $origem['pagina'],
                        'pericias_iniciais' => $origem['pericias_iniciais'],
                        'recursos_adicionais' => $origem['recursos_adicionais'],
                    ]
                );
            }
        }

        $this->command->info('Origens de outros sistemas populadas!');
    }
}
