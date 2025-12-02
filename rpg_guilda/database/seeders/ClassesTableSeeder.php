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
        // Busca os sistemas necessários
        $sistemas = Sistema::whereIn('nome', ['D&D 5e', 'Ordem Paranormal', 'Call of Cthulhu', 'Savage Worlds'])
                           ->get()->keyBy('nome');

        // -----------------------------
        // D&D 5e Classes
        // -----------------------------
        $classesDnd = [
            [
                'nome'=>'Guerreiro',
                'descricao'=>'Mestres em combate marcial, treinados para usar uma vasta gama de armas e armaduras. O guerreiro é o pilar de qualquer grupo de aventureiros.',
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
                'poderes'=>[
                    ['nome' => 'Estilo de Luta', 'descricao' => 'Escolhe um estilo de luta (Defesa, Duelismo, etc.).'],
                    ['nome' => 'Surto de Ação', 'descricao' => 'Pode realizar uma ação extra em seu turno.']
                ],
                'pagina'=>'PHB 70',
                'pericias_relacionadas'=>['Atletismo','Intimidação','Percepção','Sobrevivência']
            ],
            [
                'nome'=>'Mago',
                'descricao'=>'Estudiosos da magia, capazes de manipular as forças arcanas através de feitiços e rituais complexos. Dependem de sua inteligência.',
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
                'poderes'=>[
                    ['nome' => 'Recuperação Arcana', 'descricao' => 'Recupera espaços de magia após um descanso curto.']
                ],
                'pagina'=>'PHB 112',
                'pericias_relacionadas'=>['Arcanismo','História','Investigação']
            ],
            [
                'nome'=>'Ladino',
                'descricao'=>'Mestres em furtividade, truques e precisão. Excelentes em combate furtivo e em desarmar armadilhas.',
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
                'poderes'=>[
                    ['nome' => 'Ataque Furtivo', 'descricao' => 'Causa dano extra em ataques com vantagem.']
                ],
                'pagina'=>'PHB 96',
                'pericias_relacionadas'=>['Furtividade','Prestidigitação','Investigação','Enganação']
            ],
            [
                'nome'=>'Clérigo',
                'descricao'=>'Servos dos deuses, manifestando poder divino através de magia de cura e combate. Sua fé é sua maior arma.',
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
                'poderes'=>[
                    ['nome' => 'Conjurando e Preparando Magias', 'descricao' => 'Acesso à magia divina com base em Sabedoria.']
                ],
                'pagina'=>'PHB 57',
                'pericias_relacionadas'=>['Religião','Intuição','Medicina']
            ],
            [
                'nome'=>'Paladino',
                'descricao'=>'Guerreiros sagrados juramentados a um ideal. Combinam poder marcial com magia divina para proteger os inocentes.',
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
                'poderes'=>[
                    ['nome' => 'Sentido Divino', 'descricao' => 'Detecta a presença de celestiais, demônios e mortos-vivos.']
                ],
                'pagina'=>'PHB 82',
                'pericias_relacionadas'=>['Intimidação','Religião']
            ],
            [
                'nome'=>'Bardo',
                'descricao'=>'Com a palavra e a melodia, o bardo inspira aliados e confunde inimigos. Sua magia vem da arte.',
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
                'poderes'=>[
                    ['nome' => 'Inspiração de Bardo', 'descricao' => 'Pode gastar um uso para conceder um bônus em um teste de perícia ou ataque.']
                ],
                'pagina'=>'PHB 51',
                'pericias_relacionadas'=>['Persuasão','Atuação']
            ],
            [
                'nome'=>'Druida',
                'descricao'=>'Protetores da natureza, canalizando seus poderes para se transformar em animais e lançar feitiços primordiais.',
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
                'poderes'=>[
                    ['nome' => 'Forma Selvagem', 'descricao' => 'Pode se transformar em um animal.']
                ],
                'pagina'=>'PHB 64',
                'pericias_relacionadas'=>['Natureza','Sobrevivência']
            ],
            [
                'nome'=>'Monge',
                'descricao'=>'Dominadores de ki, que alcançam perfeição física e espiritual através de treinamento rigoroso. Lutam desarmados e desprotegidos.',
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
                'poderes'=>[
                    ['nome' => 'Ataque Desarmado', 'descricao' => 'Seus ataques desarmados causam mais dano.']
                ],
                'pagina'=>'PHB 76',
                'pericias_relacionadas'=>['Acrobacia','Atletismo']
            ],
            [
                'nome'=>'Feiticeiro',
                'descricao'=>'Possuidores de magia inata, que flui diretamente de seu sangue ou linhagem mística. Não precisam estudar para lançar magias.',
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
                'poderes'=>[
                    ['nome' => 'Feitiçaria', 'descricao' => 'Pode manipular as propriedades de suas magias.']
                ],
                'pagina'=>'PHB 101',
                'pericias_relacionadas'=>['Arcanismo','Persuasão']
            ],
            [
                'nome'=>'Bruxo',
                'descricao'=>'Fez um pacto com um ser de outro mundo para obter poder arcano. Sua magia vem de um patrono.',
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
                'poderes'=>[
                    ['nome' => 'Pacto Sobrenatural', 'descricao' => 'Ganha características especiais baseadas no seu Patrono.']
                ],
                'pagina'=>'PHB 105',
                'pericias_relacionadas'=>['Arcanismo','Intimidação']
            ],
            [
                'nome'=>'Barbaro',
                'descricao'=>'Um guerreiro selvagem, capaz de entrar em fúria em batalha. Sua força bruta é incomparável.',
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
                'poderes'=>[
                    ['nome' => 'Fúria', 'descricao' => 'Pode entrar em um estado de fúria, ganhando bônus em dano e resistência.']
                ],
                'pagina'=>'PHB 46',
                'pericias_relacionadas'=>['Atletismo','Intimidação']
            ],
            [
                'nome'=>'Ranger',
                'descricao'=>'Rastreadores e caçadores, com profundo conhecimento da natureza e uma mistura de combate e magia primal.',
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
                'poderes'=>[
                    ['nome' => 'Inimigo Predileto', 'descricao' => 'Escolhe um tipo de criatura para ter vantagens em combate e rastreio.']
                ],
                'pagina'=>'PHB 89',
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
                'descricao'=>'Focado em combate e resistência, o combatente é a linha de frente contra o Paranormal.',
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
                'poderes'=>[
                    ['nome' => 'Bônus de Ataque', 'descricao' => 'Melhora a proficiência em armas.']
                ],
                'pagina'=>'Ordem RPG 30',
                'pericias_relacionadas'=>['Luta','Pontaria','Atletismo','Intimidação']
            ],
            [
                'nome'=>'Especialista',
                'descricao'=>'Utiliza o conhecimento e a tecnologia para superar os desafios, oferecendo suporte crucial ao time.',
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
                'poderes'=>[
                    ['nome' => 'Versatilidade', 'descricao' => 'Maior número de perícias treinadas.']
                ],
                'pagina'=>'Ordem RPG 35',
                'pericias_relacionadas'=>['Tecnologia','Investigação','Furtividade','Reflexos']
            ],
            [
                'nome'=>'Ocultista',
                'descricao'=>'Aquele que se aprofunda nos mistérios do Outro Lado, manipulando a energia do Paranormal.',
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
                'poderes'=>[
                    ['nome' => 'Poderes Paranormais', 'descricao' => 'Pode aprender e lançar Rituais.']
                ],
                'pagina'=>'Ordem RPG 40',
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
                'descricao'=>'Pessoa perspicaz e com faro para mistérios, ganhando a vida desvendando segredos.',
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
                'poderes'=>[],
                'pagina'=>'CoC 55',
                'pericias_relacionadas'=>['Ocultismo','Psicologia','Arqueologia','Furtividade']
            ],
            [
                'nome'=>'Professor',
                'descricao'=>'Acadêmico com vasto conhecimento em uma ou mais áreas, muitas vezes desvendando o oculto através da pesquisa.',
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
                'poderes'=>[],
                'pagina'=>'CoC 60',
                'pericias_relacionadas'=>['História','Biblioteca','Antropologia']
            ],
            [
                'nome'=>'Artista',
                'descricao'=>'Pessoa que vive da arte, seja música, pintura ou teatro. Possui charme e sensibilidade acima da média.',
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
                'poderes'=>[],
                'pagina'=>'CoC 65',
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
                'descricao'=>'Treinado em combate corpo a corpo, usa força e armas para resolver conflitos.',
                'usa_magia'=>false,
                'atributos_bonus'=>['lutar'=>1],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>['Lutar d6','Atletismo d6']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Arma Leve','Colete Simples','200$']
                ],
                'poderes'=>[],
                'pagina'=>'SWADE 45',
                'pericias_relacionadas'=>['Lutar','Atletismo']
            ],
            [
                'nome'=>'Atirador',
                'descricao'=>'Especialista em armas de fogo, capaz de acertar alvos a longas distâncias.',
                'usa_magia'=>false,
                'atributos_bonus'=>['atirar'=>1],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>['Atirar d6','Furtividade d6']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Rifle de Caça','Munição (2 caixas)','200$']
                ],
                'poderes'=>[],
                'pagina'=>'SWADE 46',
                'pericias_relacionadas'=>['Atirar','Furtividade']
            ],
            [
                'nome'=>'Místico',
                'descricao'=>'Aquele que usa poderes sobrenaturais, seja magia, milagres ou poderes psíquicos.',
                'usa_magia'=>true,
                'atributos_bonus'=>['espirito'=>2],
                'dado_vida'=>'d6',
                'pericias_iniciais'=>[
                    'fixas'=>['Curar d4','Investigar d4','Arcanismo d6']
                ],
                'equipamento_inicial'=>[
                    'fixas'=>['Livro de Magias','Talismã','150$']
                ],
                'poderes'=>[
                    ['nome' => 'Poderes Arcanos', 'descricao' => 'Acesso a uma lista de poderes.']
                ],
                'pagina'=>'SWADE 47',
                'pericias_relacionadas'=>['Curar','Investigar']
            ],
        ];

        $this->populateClasses($sistemas['Savage Worlds']->id, $classesSW);

        $this->command->info('Classes populadas: D&D 5e, Ordem Paranormal, Call of Cthulhu, Savage Worlds!');
    }

    /**
     * Cria ou atualiza as classes no banco de dados, incluindo os novos campos.
     *
     * @param int $sistemaId
     * @param array $classes
     * @return void
     */
    private function populateClasses($sistemaId, $classes)
    {
        foreach ($classes as $c) {
            $classe = Classe::updateOrCreate(
                ['nome'=>$c['nome'],'sistema_id'=>$sistemaId],
                [
                    'descricao' => $c['descricao'] ?? null,
                    'usa_magia'=>$c['usa_magia'] ?? false,
                    'atributos_bonus'=>json_encode($c['atributos_bonus'] ?? []),
                    'dado_vida'=>$c['dado_vida'] ?? null,
                    'pericias_iniciais'=>json_encode($c['pericias_iniciais'] ?? []),
                    'equipamento_inicial'=>json_encode($c['equipamento_inicial'] ?? []),
                    'poderes' => json_encode($c['poderes'] ?? []),
                    'pagina' => $c['pagina'] ?? null,
                ]
            );

            // Relaciona as perícias
            if(isset($c['pericias_relacionadas'])){
                $pericias = Pericia::where('sistema_id',$sistemaId)
                                   ->whereIn('nome',$c['pericias_relacionadas'])
                                   ->pluck('id');
                // Sincroniza (anexa/desanexa) o relacionamento
                $classe->pericias()->sync($pericias);
            }
        }
    }
}
