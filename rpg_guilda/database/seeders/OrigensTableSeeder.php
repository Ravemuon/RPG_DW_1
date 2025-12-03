<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origem;
use App\Models\Sistema;

class OrigensTableSeeder extends Seeder
{
    public function run(): void
    {
        $dd5 = Sistema::where('nome', 'D&D 5e')->first();

        if (!$dd5) {
            $this->command->error("Sistema D&D 5e não encontrado.");
            return;
        }

        $origensDD = [
            [
                'nome'=>'Aldeão',
                'descricao'=>'Criado em uma vila simples, habituado ao trabalho braçal.',
                'pagina'=>'PHB 123',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Atletismo','Sobrevivência'],
                    'escolha'=>2
                ],
            ],
            [
                'nome'=>'Nobre',
                'descricao'=>'Pertence a uma família influente.',
                'pagina'=>'PHB 135',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['História','Persuasão'],
                    'escolha'=>2
                ],
            ],
            [
                'nome'=>'Criminoso',
                'descricao'=>'Cresceu entre ladrões e contrabandistas.',
                'pagina'=>'PHB 129',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Furtividade','Enganação'],
                    'escolha'=>2
                ],
            ],
            [
                'nome'=>'Artista',
                'descricao'=>'Intérprete acostumado a apresentações.',
                'pagina'=>'PHB 134',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Atuação','Acrobacia'],
                    'escolha'=>2
                ],
            ],
            [
                'nome'=>'Soldado',
                'descricao'=>'Treinado nas artes da guerra.',
                'pagina'=>'PHB 140',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Intimidação','Atletismo'],
                    'escolha'=>2
                ],
            ],
                [
                    'nome'=>'Marinheiro',
                    'descricao'=>'Experiente em navegação e vida no mar.',
                    'pagina'=>'PHB 142',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Atletismo','Sobrevivência'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Ermitão',
                    'descricao'=>'Viveu isolado, em meditação ou estudo.',
                    'pagina'=>'PHB 143',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Medicina','Intuição'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Mercador',
                    'descricao'=>'Viajante acostumado ao comércio.',
                    'pagina'=>'PHB 144',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Persuasão','História'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Forasteiro',
                    'descricao'=>'Habituado às regiões selvagens.',
                    'pagina'=>'PHB 146',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Sobrevivência','Percepção'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Sacerdote',
                    'descricao'=>'Treinado em ritos religiosos.',
                    'pagina'=>'PHB 148',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Religião','Persuasão'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Acólito',
                    'descricao'=>'Criado em templos ou monastérios.',
                    'pagina'=>'PHB 149',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Religião','Intuição'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Explorador',
                    'descricao'=>'Acostumado a trilhas desconhecidas.',
                    'pagina'=>'PHB 150',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Percepção','Sobrevivência'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Charlatão',
                    'descricao'=>'Vive de truques e mentiras.',
                    'pagina'=>'PHB 151',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Enganação','Persuasão'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Aventureiro',
                    'descricao'=>'Acostumado a viagens e perigos.',
                    'pagina'=>'PHB 152',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Atletismo','Percepção'],
                        'escolha'=>2
                    ],
                ],
                [
                    'nome'=>'Místico',
                    'descricao'=>'Estudioso de magia e ocultismo.',
                    'pagina'=>'PHB 153',
                    'pericias_iniciais'=>[
                        'fixas'=>[],
                        'lista'=>['Arcanismo','Intuição'],
                        'escolha'=>2
                    ],
                ],
            ];

        foreach ($origensDD as $data) {
            Origem::updateOrCreate(
                ['nome'=>$data['nome'], 'sistema_id'=>$dd5->id],
                [
                    'descricao'=>$data['descricao'],
                    'pagina'=>$data['pagina'],
                    'pericias_iniciais'=>$data['pericias_iniciais'],
                ]
            );
        }

        $this->command->info('Origens D&D 5e populadas!');
    }
}
