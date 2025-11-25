<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origem;
use App\Models\Sistema;

class OrigensTableSeeder extends Seeder
{
    public function run(): void
    {
        $sistema = Sistema::where('nome', 'D&D 5e')->first();

        if (!$sistema) {
            $this->command->error("Sistema D&D 5e não encontrado.");
            return;
        }

        $origens = [
            ['nome'=>'Aldeão','descricao'=>'Criado em uma vila simples, habituado ao trabalho braçal e rotina rústica.','pagina'=>'PHB 123','bonus_pericias_data'=>['Atletismo'=>1,'Sobrevivência'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Ferramentas de artesão (à sua escolha)','Recurso de Origem'=>'Um lugar para descansar (Hospitalidade em comunidades rurais)']],
            ['nome'=>'Nobre','descricao'=>'Pertencente a uma família influente, treinado em etiqueta e política.','pagina'=>'PHB 135','bonus_pericias_data'=>['História'=>1,'Persuasão'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Um tipo de instrumento musical','Recurso de Origem'=>'Posição de Prestígio']],
            ['nome'=>'Criminoso','descricao'=>'Cresceu entre ladrões, contrabandistas e marginais.','pagina'=>'PHB 129','bonus_pericias_data'=>['Furtividade'=>1,'Enganação'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Kit de Ladrão e um jogo de cartas','Recurso de Origem'=>'Conexão Criminosa']],
            ['nome'=>'Artista','descricao'=>'Viajante ou intérprete, acostumado ao encanto das apresentações.','pagina'=>'PHB 134','bonus_pericias_data'=>['Atuação'=>1,'Acrobacia'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Três tipos de instrumentos musicais','Recurso de Origem'=>'Identidade Popular']],
            ['nome'=>'Soldado','descricao'=>'Treinado nas artes da guerra, já viu combates e campanhas.','pagina'=>'PHB 140','bonus_pericias_data'=>['Intimidação'=>1,'Atletismo'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Ferramentas de armaria','Recurso de Origem'=>'Patente militar reconhecida']],
            ['nome'=>'Marinheiro','descricao'=>'Acostumado à vida no mar, com experiência em navegação e combate naval.','pagina'=>'PHB 142','bonus_pericias_data'=>['Atletismo'=>1,'Sobrevivência'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Ferramentas de navegação','Recurso de Origem'=>'Conhecimento marítimo']],
            ['nome'=>'Ermitão','descricao'=>'Passou a vida isolado, estudando ou meditando em solidão.','pagina'=>'PHB 143','bonus_pericias_data'=>['Medicina'=>1,'Intuição'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Kit de herborismo','Recurso de Origem'=>'Fonte de conhecimento secreta']],
            ['nome'=>'Mercador','descricao'=>'Viajou por diversos mercados e cidades, conhecendo comércio e negociações.','pagina'=>'PHB 144','bonus_pericias_data'=>['Persuasão'=>1,'História'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Kit de contabilidade','Recurso de Origem'=>'Rede de contatos comerciais']],
            ['nome'=>'Forasteiro','descricao'=>'Viajante constante, habituado a regiões selvagens e encontros inesperados.','pagina'=>'PHB 146','bonus_pericias_data'=>['Sobrevivência'=>1,'Percepção'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Kit de viajante','Recurso de Origem'=>'Conhecimento de rotas e terrenos']],
            ['nome'=>'Sacerdote','descricao'=>'Serviu a uma divindade, conhecendo ritos e tradições sagradas.','pagina'=>'PHB 148','bonus_pericias_data'=>['Religião'=>1,'Persuasão'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Símbolo Sagrado','Recurso de Origem'=>'Conexão com templos']],
            ['nome'=>'Acólito','descricao'=>'Cresceu em um templo ou monastério, conhecendo os mistérios de sua fé.','pagina'=>'PHB 149','bonus_pericias_data'=>['Religião'=>1,'Intuição'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Rituais sagrados','Recurso de Origem'=>'Rede de contatos religiosos']],
            ['nome'=>'Explorador','descricao'=>'Familiarizado com trilhas desconhecidas, criaturas e sobrevivência em ambientes hostis.','pagina'=>'PHB 150','bonus_pericias_data'=>['Percepção'=>1,'Sobrevivência'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Kit de explorador','Recurso de Origem'=>'Mapa e rotas seguras']],
            ['nome'=>'Charlatão','descricao'=>'Astuto e persuasivo, vive de truques, vendas e artimanhas.','pagina'=>'PHB 151','bonus_pericias_data'=>['Enganação'=>1,'Persuasão'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Kit de falsificação','Recurso de Origem'=>'Contatos duvidosos']],
            ['nome'=>'Aventureiro','descricao'=>'Iniciou sua vida em viagens e combates em busca de fama ou riqueza.','pagina'=>'PHB 152','bonus_pericias_data'=>['Atletismo'=>1,'Percepção'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Kit de aventureiro','Recurso de Origem'=>'Equipamento básico']],
            ['nome'=>'Místico','descricao'=>'Estudioso de magia ou ocultismo desde jovem, conhecendo rituais e encantamentos.','pagina'=>'PHB 153','bonus_pericias_data'=>['Arcanismo'=>1,'Intuição'=>1],'recursos_adicionais_data'=>['Proficiência em Ferramentas'=>'Foco Arcano','Recurso de Origem'=>'Grimório pessoal']],
        ];

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

        $this->command->info('Origens D&D 5e populadas!');
    }
}
