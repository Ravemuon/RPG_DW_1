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
        $sistemas = Sistema::whereIn('nome', ['D&D 5e', 'Ordem Paranormal', 'Call of Cthulhu', 'Savage Worlds'])
                           ->get()->keyBy('nome');

        // -----------------------------
        // D&D 5e Classes
        // -----------------------------
        $classesDnd = [
            [
                'nome'=>'Guerreiro',
                'usa_magia'=>false,
                'atributos_bonus'=>['forca'=>2,'constituicao'=>1],
                'dado_vida'=>'d10',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Acrobacia','Adestrar Animais','Atletismo','História','Intimidação','Intuição','Percepção','Sobrevivência'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Pack de Explorador','Duas Armas Simples'],
                    'opcoes'=>[['Cota de Malha','Machado Grande'],['Armadura de Couro','Arco Longo','20 Flechas']]
                ],
                'pericias_relacionadas'=>['Atletismo','Intimidação','Percepção','Sobrevivência']
            ],
            [
                'nome'=>'Mago',
                'usa_magia'=>true,
                'atributos_bonus'=>['inteligencia'=>3],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Arcanismo','História','Investigação','Medicina','Religião'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Bolsa de Componentes','Livro de Magias'],
                    'opcoes'=>[['Bordão'],['Adaga']]
                ],
                'pericias_relacionadas'=>['Arcanismo','História','Investigação']
            ],
            [
                'nome'=>'Ladino',
                'usa_magia'=>false,
                'atributos_bonus'=>['destreza'=>3],
                'dado_vida'=>'d8',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Acrobacia','Atletismo','Atuação','Blefe','Furtividade','Intuição','Intimidação','Investigação','Percepção','Persuasão','Prestidigitação'],
                    'escolha'=>4
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Ferramentas de Ladrão','Pack de Explorador'],
                    'opcoes'=>[['Rapiera'],['Espada Curta']]
                ],
                'pericias_relacionadas'=>['Furtividade','Prestidigitação','Investigação','Enganação']
            ],
            [
                'nome'=>'Clérigo',
                'usa_magia'=>true,
                'atributos_bonus'=>['sabedoria'=>3],
                'dado_vida'=>'d8',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['História','Intuição','Medicina','Persuasão','Religião'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Escudo','Símbolo Sagrado','Armadura de Malha'],
                    'opcoes'=>[['Maça','Martelo']]
                ],
                'pericias_relacionadas'=>['Religião','Intuição','Medicina']
            ],
            [
                'nome'=>'Paladino',
                'usa_magia'=>true,
                'atributos_bonus'=>['forca'=>2,'carisma'=>1],
                'dado_vida'=>'d10',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Intimidação','Religião','Medicina','Persuasão'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Espada Longa','Escudo'],
                    'opcoes'=>[['Armadura Completa']]
                ],
                'pericias_relacionadas'=>['Intimidação','Religião']
            ],
            [
                'nome'=>'Bardo',
                'usa_magia'=>true,
                'atributos_bonus'=>['carisma'=>2],
                'dado_vida'=>'d8',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Atuação','Blefe','Persuasão','História','Intuição','Percepção','Prestidigitação'],
                    'escolha'=>3
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Instrumento Musical'],
                    'opcoes'=>[['Adaga']]
                ],
                'pericias_relacionadas'=>['Persuasão','Atuação']
            ],
            [
                'nome'=>'Druida',
                'usa_magia'=>true,
                'atributos_bonus'=>['sabedoria'=>2],
                'dado_vida'=>'d8',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Natureza','Intuição','Percepção','Sobrevivência'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Cajado','Foco Druídico'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Natureza','Sobrevivência']
            ],
            [
                'nome'=>'Monge',
                'usa_magia'=>false,
                'atributos_bonus'=>['destreza'=>2,'sabedoria'=>1],
                'dado_vida'=>'d8',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Acrobacia','Atletismo','Furtividade','Intuição'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Roupas Leves'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Acrobacia','Atletismo']
            ],
            [
                'nome'=>'Feiticeiro',
                'usa_magia'=>true,
                'atributos_bonus'=>['carisma'=>2],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Arcanismo','Intuição','Persuasão'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Adaga','Foco Arcano'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Arcanismo','Persuasão']
            ],
            [
                'nome'=>'Bruxo',
                'usa_magia'=>true,
                'atributos_bonus'=>['carisma'=>2],
                'dado_vida'=>'d8',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Arcanismo','Intimidação','Persuasão'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Adaga','Livro de Pactos'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Arcanismo','Intimidação']
            ],
            [
                'nome'=>'Barbaro',
                'usa_magia'=>false,
                'atributos_bonus'=>['forca'=>2,'constituicao'=>1],
                'dado_vida'=>'d12',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Atletismo','Intimidação','Sobrevivência','Percepção'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Machado','Armadura Leve'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Atletismo','Intimidação']
            ],
            [
                'nome'=>'Ranger',
                'usa_magia'=>true,
                'atributos_bonus'=>['destreza'=>2,'sabedoria'=>1],
                'dado_vida'=>'d10',
                'pericias_iniciais'=>[
                    'fixas'=>[],
                    'lista'=>['Acrobacia','Furtividade','Percepção','Sobrevivência','Intuição'],
                    'escolha'=>3
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Arco Longo','Espada Curta'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Furtividade','Percepção','Sobrevivência']
            ],
        ];

        $this->populateClasses($sistemas['D&D 5e']->id, $classesDnd);

        // -----------------------------
        // Ordem Paranormal Classes
        // -----------------------------
        $classesOP = [
            [
                'nome'=>'Combatente',
                'usa_magia'=>false,
                'atributos_bonus'=>['forca'=>2,'agilidade'=>1],
                'dado_vida'=>'d12',
                'pericias_iniciais'=>[
                    'fixas'=>['Luta','Pontaria','Fortitude'],
                    'lista'=>['Intimidação','Pilotagem','Tática'],
                    'escolha'=>1
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Colete Simples','Munição (1 caixa)'],
                    'opcoes'=>[['Arma Corpo a Corpo Pesada'],['Arma de Fogo Leve']]
                ],
                'pericias_relacionadas'=>['Luta','Pontaria','Atletismo','Intimidação']
            ],
            [
                'nome'=>'Especialista',
                'usa_magia'=>false,
                'atributos_bonus'=>['intelecto'=>2,'agilidade'=>1],
                'dado_vida'=>'d8',
                'pericias_iniciais'=>[
                    'fixas'=>['Investigação','Reflexos'],
                    'lista'=>['Ciências','Medicina','Tecnologia','Furtividade'],
                    'escolha'=>2
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Celular','Kit de Perícias','Munição (1 caixa)'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Tecnologia','Investigação','Furtividade','Reflexos']
            ],
            [
                'nome'=>'Ocultista',
                'usa_magia'=>true,
                'atributos_bonus'=>['vontade'=>2],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>['Ocultismo','Vontade'],
                    'lista'=>['Intuição','Religião','Iniciativa'],
                    'escolha'=>1
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Componentes Raros','Vestimentas'],
                    'opcoes'=>[]
                ],
                'pericias_relacionadas'=>['Ocultismo','Misticismo','Religião']
            ],
        ];

        $this->populateClasses($sistemas['Ordem Paranormal']->id, $classesOP);

        // -----------------------------
        // Call of Cthulhu Classes
        // -----------------------------
        $classesCth = [
            [
                'nome'=>'Investigador Particular',
                'usa_magia'=>false,
                'atributos_bonus'=>['inteligencia'=>1],
                'dado_vida'=>null,
                'pericias_iniciais'=>[
                    'pontos_livres'=>20,
                    'bonus_pericias'=>['Persuasão','Furtividade','Investigar','Psicologia']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Pistola (licença)','Lanterna','Caderno']
                ],
                'pericias_relacionadas'=>['Ocultismo','Psicologia','Arqueologia','Furtividade']
            ],
            [
                'nome'=>'Professor',
                'usa_magia'=>false,
                'atributos_bonus'=>['inteligencia'=>2],
                'dado_vida'=>null,
                'pericias_iniciais'=>[
                    'pontos_livres'=>20,
                    'bonus_pericias'=>['História','Arqueologia','Biblioteca','Antropologia']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Livros de Estudo','Óculos','Caneta Tinteiro']
                ],
                'pericias_relacionadas'=>['História','Biblioteca','Antropologia']
            ],
            [
                'nome'=>'Artista',
                'usa_magia'=>false,
                'atributos_bonus'=>['carisma'=>2],
                'dado_vida'=>null,
                'pericias_iniciais'=>[
                    'pontos_livres'=>20,
                    'bonus_pericias'=>['Arte/Ofício','Charme','Persuasão']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Kit de Pintura','Vestes Finas','Flauta']
                ],
                'pericias_relacionadas'=>['Charme','Persuasão']
            ],
        ];

        $this->populateClasses($sistemas['Call of Cthulhu']->id, $classesCth);

        // -----------------------------
        // Savage Worlds Classes
        // -----------------------------
        $classesSW = [
            [
                'nome'=>'Combatente',
                'usa_magia'=>false,
                'atributos_bonus'=>['lutar'=>1],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>['Lutar d6','Atletismo d6']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Arma Leve','Colete Simples','200$']
                ],
                'pericias_relacionadas'=>['Lutar','Atletismo']
            ],
            [
                'nome'=>'Atirador',
                'usa_magia'=>false,
                'atributos_bonus'=>['atirar'=>1],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>['Atirar d6','Furtividade d6']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Rifle de Caça','Munição (2 caixas)','200$']
                ],
                'pericias_relacionadas'=>['Atirar','Furtividade']
            ],
            [
                'nome'=>'Místico',
                'usa_magia'=>true,
                'atributos_bonus'=>['espirito'=>2],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>['Curar d4','Investigar d4','Arcanismo d6']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Livro de Magias','Talismã','150$']
                ],
                'pericias_relacionadas'=>['Curar','Investigar']
            ],
        ];

        $this->populateClasses($sistemas['Savage Worlds']->id, $classesSW);

        $this->command->info('Classes populadas: D&D 5e, Ordem Paranormal, Call of Cthulhu, Savage Worlds!');
    }

    private function populateClasses($sistemaId, $classes)
    {
        foreach ($classes as $c) {
            $classe = Classe::updateOrCreate(
                ['nome'=>$c['nome'],'sistema_id'=>$sistemaId],
                [
                    'usa_magia'=>$c['usa_magia'],
                    'atributos_bonus'=>json_encode($c['atributos_bonus']),
                    'dado_vida'=>$c['dado_vida'],
                    'pericias_iniciais'=>json_encode($c['pericias_iniciais']),
                    'equipamento_inicial'=>json_encode($c['equipamento_inicial']),
                ]
            );

            if(isset($c['pericias_relacionadas'])){
                $pericias = Pericia::where('sistema_id',$sistemaId)
                                   ->whereIn('nome',$c['pericias_relacionadas'])
                                   ->pluck('id');
                $classe->pericias()->sync($pericias);
            }
        }
    }
}
