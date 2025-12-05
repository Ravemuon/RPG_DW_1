<?php

// App/Http/Requests/StoreStep5Request.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Classe; // Necessário para a validação dinâmica

class StoreStep5Request extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            // Campos de texto para organização
            'inventario' => ['nullable', 'string'],
            'equipamento' => ['nullable', 'string'],
            
            // Campo oculto que armazena as perícias selecionadas (JSON string)
            // É 'required' se a classe exigir seleção de perícias
            'pericias_classe_selecionadas' => ['nullable', 'string'], 
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
                // Se a classe atual não exigir seleção de perícias, não faz nada.
                if ($this->input('pericias_classe_selecionadas') === null) {
                    return;
                }
                
                $periciasJson = $this->input('pericias_classe_selecionadas');
                $classeId = session('personagem_data.classe_id'); // Pega a classe salva na sessão
                $classe = null;

                // 1. Validar se é JSON válido
                $periciasArray = json_decode($periciasJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $validator->errors()->add('pericias_classe_selecionadas', 'O formato dos dados das perícias é inválido.');
                    return;
                }
                
                // 2. Buscar a classe para verificar o limite dinâmico
                if ($classeId) {
                    $classe = Classe::find($classeId);
                }

                // 3. Validar a quantidade de perícias selecionadas
                if ($classe) {
                    // EXIGE que a model Classe tenha o campo 'limite_pericias_selecionaveis'
                    $limite = $classe->limite_pericias_selecionaveis ?? 0;
                    $selecionadas = count($periciasArray);

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