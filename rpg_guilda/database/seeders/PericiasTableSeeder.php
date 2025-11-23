<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pericia;
use App\Models\Sistema;

class PericiasTableSeeder extends Seeder
{
    public function run(): void
    {
        $listaPericias = [

            // -----------------------------------------
            //  D&D 5e
            // -----------------------------------------
            'D&D 5e' => [
                'Acrobacia', 'Adestrar Animais', 'Arcanismo', 'Atletismo',
                'Enganação', 'História', 'Intimidação', 'Intuição',
                'Investigação', 'Medicina', 'Natureza', 'Percepção',
                'Persuasão', 'Prestidigitação', 'Religião', 'Sobrevivência',
                'Furtividade'
            ],

            // -----------------------------------------
            //  Ordem Paranormal
            // -----------------------------------------
            'Ordem Paranormal' => [
                'Atletismo', 'Atualidades', 'Ciências', 'Diplomacia',
                'Enganação', 'Furtividade', 'Iniciativa', 'Intimidação',
                'Investigação', 'Luta', 'Medicina', 'Misticismo',
                'Ocultismo', 'Percepção', 'Pilotagem', 'Pontaria',
                'Profissão', 'Reflexos', 'Religião', 'Sobrevivência',
                'Tecnologia'
            ],

            // -----------------------------------------
            // Call of Cthulhu
            // -----------------------------------------
            'Call of Cthulhu' => [
                'Antropologia', 'Arqueologia', 'Charme', 'Persuasão',
                'Intimidação', 'Ocultismo', 'Psicologia', 'Primeiros Socorros',
                'História', 'Biblioteca', 'Furtividade', 'Rastrear',
                'Sobrevivência', 'Mecânica', 'Dirigir Automóveis',
                'Armas de Fogo', 'Condução', 'Natação'
            ],

            // -----------------------------------------
            // Savage Worlds
            // -----------------------------------------
            'Savage Worlds' => [
                'Lutar', 'Atirar', 'Intimidar', 'Investigar', 'Furtividade',
                'Perceber', 'Conduzir', 'Curar', 'Ofícios', 'Atletismo'
            ],
        ];

        foreach ($listaPericias as $nomeSistema => $pericias) {
            $sistema = Sistema::where('nome', $nomeSistema)->first();

            if (!$sistema) continue;

            foreach ($pericias as $nome) {
                Pericia::updateOrCreate(
                    [
                        'nome' => $nome,
                        'sistema_id' => $sistema->id
                    ],
                    []
                );
            }
        }
    }
}
