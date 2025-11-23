<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sistema;
use App\Models\Classe;
use App\Models\Pericia;

class ClassesTableSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------
        // D&D 5e CLASSES
        // -----------------------------
        $dnd = Sistema::where('nome', 'D&D 5e')->first();

        if ($dnd) {
            $classesDnd = [
                [
                    'nome' => 'Guerreiro',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['forca' => 2, 'constituicao' => 1]),
                    'pericias' => ['Atletismo', 'Intimidação', 'Percepção', 'Sobrevivência']
                ],
                [
                    'nome' => 'Mago',
                    'usa_magia' => true,
                    'atributos_bonus' => json_encode(['inteligencia' => 3]),
                    'pericias' => ['Arcanismo', 'História', 'Investigação']
                ],
                [
                    'nome' => 'Ladino',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['destreza' => 3]),
                    'pericias' => ['Furtividade', 'Prestidigitação', 'Investigação', 'Enganação']
                ],
                [
                    'nome' => 'Clérigo',
                    'usa_magia' => true,
                    'atributos_bonus' => json_encode(['sabedoria' => 3]),
                    'pericias' => ['Religião', 'Intuição', 'Medicina']
                ],
            ];

            foreach ($classesDnd as $c) {
                $classe = Classe::updateOrCreate(
                    ['nome' => $c['nome'], 'sistema_id' => $dnd->id],
                    [
                        'usa_magia' => $c['usa_magia'],
                        'atributos_bonus' => $c['atributos_bonus']
                    ]
                );

                $pericias = Pericia::where('sistema_id', $dnd->id)
                                   ->whereIn('nome', $c['pericias'])
                                   ->pluck('id');

                $classe->pericias()->sync($pericias);
            }
        }

        // -----------------------------
        // ORDEM PARANORMAL CLASSES
        // -----------------------------
        $op = Sistema::where('nome', 'Ordem Paranormal')->first();

        if ($op) {
            $classesOP = [
                [
                    'nome' => 'Combatente',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['forca' => 2, 'agilidade' => 1]),
                    'pericias' => ['Atletismo', 'Pontaria', 'Luta', 'Intimidação']
                ],
                [
                    'nome' => 'Especialista',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['intelecto' => 2, 'agilidade' => 1]),
                    'pericias' => ['Tecnologia', 'Investigação', 'Furtividade', 'Reflexos']
                ],
                [
                    'nome' => 'Ocultista',
                    'usa_magia' => true,
                    'atributos_bonus' => json_encode(['vontade' => 2]),
                    'pericias' => ['Ocultismo', 'Misticismo', 'Religião']
                ],
            ];

            foreach ($classesOP as $c) {
                $classe = Classe::updateOrCreate(
                    ['nome' => $c['nome'], 'sistema_id' => $op->id],
                    [
                        'usa_magia' => $c['usa_magia'],
                        'atributos_bonus' => $c['atributos_bonus']
                    ]
                );

                $pericias = Pericia::where('sistema_id', $op->id)
                                   ->whereIn('nome', $c['pericias'])
                                   ->pluck('id');

                $classe->pericias()->sync($pericias);
            }
        }

        // -----------------------------
        // CALL OF CTHULHU CLASSES
        // -----------------------------
        $cth = Sistema::where('nome', 'Call of Cthulhu')->first();

        if ($cth) {
            $classesCth = [
                [
                    'nome' => 'Investigador Particular',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['inteligencia' => 1]),
                    'pericias' => ['Ocultismo', 'Psicologia', 'Arqueologia', 'Furtividade']
                ],
                [
                    'nome' => 'Professor',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['inteligencia' => 2]),
                    'pericias' => ['História', 'Biblioteca', 'Antropologia']
                ],
                [
                    'nome' => 'Artista',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['carisma' => 2]),
                    'pericias' => ['Charme', 'Persuasão']
                ],
            ];

            foreach ($classesCth as $c) {
                $classe = Classe::updateOrCreate(
                    ['nome' => $c['nome'], 'sistema_id' => $cth->id],
                    [
                        'usa_magia' => $c['usa_magia'],
                        'atributos_bonus' => $c['atributos_bonus']
                    ]
                );

                $pericias = Pericia::where('sistema_id', $cth->id)
                                   ->whereIn('nome', $c['pericias'])
                                   ->pluck('id');

                $classe->pericias()->sync($pericias);
            }
        }

        // -----------------------------
        // SAVAGE WORLDS CLASSES
        // -----------------------------
        $sw = Sistema::where('nome', 'Savage Worlds')->first();

        if ($sw) {
            $classesSW = [
                [
                    'nome' => 'Combatente',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['lutar' => 1]),
                    'pericias' => ['Lutar', 'Atletismo']
                ],
                [
                    'nome' => 'Atirador',
                    'usa_magia' => false,
                    'atributos_bonus' => json_encode(['atirar' => 1]),
                    'pericias' => ['Atirar', 'Furtividade']
                ],
                [
                    'nome' => 'Místico',
                    'usa_magia' => true,
                    'atributos_bonus' => json_encode(['espirito' => 2]),
                    'pericias' => ['Curar', 'Investigar']
                ],
            ];

            foreach ($classesSW as $c) {
                $classe = Classe::updateOrCreate(
                    ['nome' => $c['nome'], 'sistema_id' => $sw->id],
                    [
                        'usa_magia' => $c['usa_magia'],
                        'atributos_bonus' => $c['atributos_bonus']
                    ]
                );

                $pericias = Pericia::where('sistema_id', $sw->id)
                                   ->whereIn('nome', $c['pericias'])
                                   ->pluck('id');

                $classe->pericias()->sync($pericias);
            }
        }
    }
}
