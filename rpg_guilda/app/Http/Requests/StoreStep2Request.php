<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// Assumindo que você usa este namespace para sua Model Classe
use App\Models\Classe;

class StoreStep2Request extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Obtém as regras de validação que se aplicam à requisição.
     */
    public function rules(): array
    {
        return [
            // IDs de escolha (obrigatórios e devem existir nas respectivas tabelas)
            'raca_id' => ['required', 'integer', 'exists:racas,id'],
            'classe_id' => ['required', 'integer', 'exists:classes,id'],
            'origem_id' => ['required', 'integer', 'exists:origens,id'],

            // Campo oculto do JavaScript. Deve ser uma string (contém JSON).
            // A validação de conteúdo (se é JSON válido e a quantidade) é feita no método after().
            'pericias_classe_selecionadas' => ['required', 'string'],
        ];
    }

    /**
     * Define mensagens de erro personalizadas para as regras.
     */
    public function messages(): array
    {
        return [
            'raca_id.required' => 'Você deve selecionar uma Raça para o personagem.',
            'raca_id.exists' => 'A Raça selecionada é inválida.',

            'classe_id.required' => 'Você deve selecionar uma Classe para o personagem.',
            'classe_id.exists' => 'A Classe selecionada é inválida.',

            'origem_id.required' => 'Você deve selecionar uma Origem para o personagem.',
            'origem_id.exists' => 'A Origem selecionada é inválida.',

            'pericias_classe_selecionadas.required' => 'As perícias de classe são obrigatórias, certifique-se de que a seleção está completa.',
        ];
    }

    /**
     * Validação que ocorre após a passagem das regras básicas.
     * Usado para validar o JSON e a lógica de quantidade de perícias.
     */
    public function after(): array
    {
        return [
            function ($validator) {
                $periciasJson = $this->input('pericias_classe_selecionadas');
                $classeId = $this->input('classe_id');
                $classe = null;

                // 1. Validar se é JSON válido
                $periciasArray = json_decode($periciasJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $validator->errors()->add(
                        'pericias_classe_selecionadas',
                        'O formato dos dados das perícias é inválido.'
                    );
                    return; // Parar se o JSON for inválido
                }

                // 2. Buscar a classe para verificar o limite dinâmico
                if ($classeId) {
                    // Tenta carregar a classe, ignorando se não existir (o 'rules' já cobriu isso)
                    $classe = Classe::find($classeId);
                }

                // 3. Validar a quantidade de perícias selecionadas
                if ($classe) {
                    // EXIGE que a model Classe tenha o campo 'limite_pericias_selecionaveis'
                    $limite = $classe->limite_pericias_selecionaveis ?? 0;
                    $selecionadas = count($periciasArray);

                    // Verifica se a quantidade selecionada é diferente do limite exigido pela classe
                    if ($selecionadas !== $limite) {
                        $validator->errors()->add(
                            'pericias_classe_selecionadas',
                            "Você deve selecionar exatamente **{$limite}** perícia(s) de Classe. Você selecionou {$selecionadas}."
                        );
                    }
                }
            }
        ];
    }
}
