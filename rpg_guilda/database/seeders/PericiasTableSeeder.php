<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sistema;
use App\Models\Pericia;

class PericiasTableSeeder extends Seeder
{
    public function run(): void
    {
        $periciasPorSistema = [
            'D&D 5e' => [
                ['nome' => 'Atletismo', 'atributo_relacionado' => 'forca', 'descricao' => 'Testa capacidade física, como escalar, nadar ou saltar.'],
                ['nome' => 'Acrobacia', 'atributo_relacionado' => 'destreza', 'descricao' => 'Testa agilidade e equilíbrio, como manobras complexas.'],
                ['nome' => 'Furtividade', 'atributo_relacionado' => 'destreza', 'descricao' => 'Testa a capacidade de se esconder e mover em silêncio.'],
                ['nome' => 'Prestidigitação', 'atributo_relacionado' => 'destreza', 'descricao' => 'Testa a destreza manual, como batedor de carteiras ou ilusionismo manual.'],
                ['nome' => 'Arcanismo', 'atributo_relacionado' => 'inteligencia', 'descricao' => 'Conhecimento sobre magias, planos de existência e criaturas mágicas.'],
                ['nome' => 'História', 'atributo_relacionado' => 'inteligencia', 'descricao' => 'Conhecimento sobre eventos passados, povos e civilizações.'],
                ['nome' => 'Investigação', 'atributo_relacionado' => 'inteligencia', 'descricao' => 'Habilidade para encontrar pistas, deduzir informações e resolver mistérios.'],
                ['nome' => 'Natureza', 'atributo_relacionado' => 'inteligencia', 'descricao' => 'Conhecimento sobre terrenos, clima, plantas e animais.'],
                ['nome' => 'Religião', 'atributo_relacionado' => 'inteligencia', 'descricao' => 'Conhecimento sobre deuses, ritos, orações e estruturas religiosas.'],
                ['nome' => 'Adestrar Animais', 'atributo_relacionado' => 'sabedoria', 'descricao' => 'Testa a capacidade de acalmar, controlar e interagir com animais.'],
                ['nome' => 'Intuição', 'atributo_relacionado' => 'sabedoria', 'descricao' => 'Testa a leitura de pessoas, detectando mentiras e intenções.'],
                ['nome' => 'Medicina', 'atributo_relacionado' => 'sabedoria', 'descricao' => 'Habilidade para estabilizar feridos e diagnosticar doenças.'],
                ['nome' => 'Percepção', 'atributo_relacionado' => 'sabedoria', 'descricao' => 'Testa a consciência do ambiente, detectando sons, cheiros ou objetos escondidos.'],
                ['nome' => 'Sobrevivência', 'atributo_relacionado' => 'sabedoria', 'descricao' => 'Habilidade para se guiar, caçar, encontrar comida e abrigo na natureza.'],
                ['nome' => 'Enganação', 'atributo_relacionado' => 'carisma', 'descricao' => 'Testa a capacidade de mentir, blefar ou fingir emoções.'],
                ['nome' => 'Intimidação', 'atributo_relacionado' => 'carisma', 'descricao' => 'Testa a capacidade de influenciar pelo medo ou ameaça.'],
                ['nome' => 'Atuação', 'atributo_relacionado' => 'carisma', 'descricao' => 'Habilidade de se apresentar em público, seja cantando, dançando ou interpretando.'],
                ['nome' => 'Persuasão', 'atributo_relacionado' => 'carisma', 'descricao' => 'Testa a capacidade de convencer, negociar e fazer amigos.'],
            ],
            'Ordem Paranormal' => [
                ['nome' => 'Luta', 'atributo_relacionado' => 'forca', 'descricao' => 'Habilidade em combate corpo a corpo desarmado ou com armas brancas.'],
                ['nome' => 'Acrobacia', 'atributo_relacionado' => 'agilidade', 'descricao' => 'Manobras atléticas, equilíbrio e quedas.'],
                ['nome' => 'Pilotagem', 'atributo_relacionado' => 'agilidade', 'descricao' => 'Dirigir veículos terrestres, aéreos e aquáticos.'],
                ['nome' => 'Reflexos', 'atributo_relacionado' => 'agilidade', 'descricao' => 'Velocidade de reação e esquiva.'],
                ['nome' => 'Furtividade', 'atributo_relacionado' => 'agilidade', 'descricao' => 'Mover-se sem ser notado e esconder-se.'],
                ['nome' => 'Ocultismo', 'atributo_relacionado' => 'intelecto', 'descricao' => 'Conhecimento sobre o Paranormal e o Outro Lado.'],
                ['nome' => 'Perícia', 'atributo_relacionado' => 'intelecto', 'descricao' => 'Perícias gerais de conhecimento e uso de ferramentas.'],
                ['nome' => 'Profissão', 'atributo_relacionado' => 'intelecto', 'descricao' => 'Perícias específicas de uma área profissional.'],
                ['nome' => 'Tecnologia', 'atributo_relacionado' => 'intelecto', 'descricao' => 'Uso e reparo de aparelhos eletrônicos e computadores.'],
                ['nome' => 'Investigação', 'atributo_relacionado' => 'percepcao', 'descricao' => 'Encontrar pistas, analisar cenas de crime e dedução.'],
                ['nome' => 'Vontade', 'atributo_relacionado' => 'vontade', 'descricao' => 'Resistência mental e emocional (Usado para o limite de Sanidade).'],
                ['nome' => 'Diplomacia', 'atributo_relacionado' => 'carisma', 'descricao' => 'Negociar, acalmar conflitos e convencer de forma amistosa.'],
                ['nome' => 'Enganação', 'atributo_relacionado' => 'carisma', 'descricao' => 'Mentir, disfarçar e ludibriar.'],
                ['nome' => 'Intimidação', 'atributo_relacionado' => 'carisma', 'descricao' => 'Forçar ou ameaçar alguém.'],
                ['nome' => 'Intuição', 'atributo_relacionado' => 'carisma', 'descricao' => 'Perceber intenções, detectar mentiras e pressentir perigos.'],
            ],
        ];

        foreach ($periciasPorSistema as $nomeSistema => $pericias) {
            $sistema = Sistema::where('nome', $nomeSistema)->first();

            if (!$sistema) {
                $this->command->error("Sistema não encontrado: {$nomeSistema}");
                continue;
            }
            $atributosSistema = json_decode($sistema->atributos, true) ?? [];

            foreach ($pericias as $periciaData) {
                $periciaData['sistema_id'] = $sistema->id;

                // Calcula modificador base. Mantido o 'match' como estava.
                $periciaData['modificador'] = match($nomeSistema) {
                    'D&D 5e' => 0, // default
                    'Ordem Paranormal' => 0,
                    default => 0
                };
                $periciaData['atributo_nome'] = $atributosSistema[$periciaData['atributo_relacionado']] ?? $periciaData['atributo_relacionado'];

                Pericia::updateOrCreate(
                    ['nome' => $periciaData['nome'], 'sistema_id' => $sistema->id],
                    $periciaData
                );
            }

            $this->command->info("Perícias do sistema {$nomeSistema} populadas!");
        }

        $this->command->info('Todas as perícias foram populadas com sucesso!');
    }
}
