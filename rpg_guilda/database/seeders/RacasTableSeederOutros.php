<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Raca;
use App\Models\Sistema;

class RacasTableSeederOutros extends Seeder
{
    /**
     * Popula a tabela de raças para os sistemas Ordem Paranormal e Call of Cthulhu.
     */
    public function run(): void
    {
        // Busca os sistemas necessários e os organiza por nome para fácil acesso.
        $sistemas = Sistema::whereIn('nome', ['Ordem Paranormal', 'Call of Cthulhu'])->get()->keyBy('nome');

        // Note: A lista de atributos não é estritamente necessária aqui, mas serve como documentação
        // sobre quais atributos são relevantes para cada sistema.
        $atributosPorSistema = [
            'Ordem Paranormal' => ['forca', 'agilidade', 'intelecto', 'percepcao', 'vontade', 'carisma'],
            'Call of Cthulhu' => ['forca', 'destreza', 'constituicao', 'inteligencia', 'poder', 'carisma'],
        ];

        // Definição dos dados das Raças, incluindo o array 'modificadores'.
        // Para a raça Humano nestes sistemas, os modificadores são tipicamente nulos/vazios.
        $racasData = [
            'Ordem Paranormal' => [
                ['nome' => 'Humano', 'descricao' => 'Investigadores de fenômenos sobrenaturais.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => null, 'modificadores' => []],
                // Exemplo de raça com bônus (se fosse o caso):
                // ['nome' => 'Mestiço', 'descricao' => 'Com resiliência sobrenatural.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => null, 'modificadores' => ['vontade' => 1]],
            ],
            'Call of Cthulhu' => [
                ['nome' => 'Humano', 'descricao' => 'Investigadores enfrentando horrores cósmicos.', 'tipo_bonus' => 'flat', 'bonus_livre' => 0, 'pagina' => null, 'modificadores' => []],
            ],
        ];

        foreach ($racasData as $sistemaNome => $racas) {
            $sistema = $sistemas->get($sistemaNome);
            if (!$sistema) {
                $this->command->error("Sistema {$sistemaNome} não encontrado.");
                continue;
            }

            foreach ($racas as $raca) {
                $raca['sistema_id'] = $sistema->id;

                // 1. Obtém os modificadores de atributos (se existirem, caso contrário, usa array vazio)
                $modificadores = $raca['modificadores'] ?? [];

                // 2. Codifica os modificadores explícitos em JSON para o campo do banco de dados.
                $raca['modificadores_atributos'] = json_encode($modificadores);

                // 3. Remove a chave temporária 'modificadores' antes de salvar.
                unset($raca['modificadores']);

                // Insere ou atualiza o registro
                Raca::updateOrCreate(
                    ['nome' => $raca['nome'], 'sistema_id' => $sistema->id],
                    $raca
                );
            }
            $this->command->info("Raças do sistema {$sistemaNome} populadas!");
        }

        $this->command->info('Raças de outros sistemas populadas!');
    }
}
