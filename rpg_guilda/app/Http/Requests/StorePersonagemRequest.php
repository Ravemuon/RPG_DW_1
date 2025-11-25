<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonagemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Apenas usuários logados podem criar personagens
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|integer|exists:campanhas,id',
            'sistema_id' => 'required|integer|exists:sistemas,id',
            'raca_id' => 'nullable|integer|exists:racas,id',
            'classe_id' => 'nullable|integer|exists:classes,id',
            'origem_id' => 'nullable|integer|exists:origens,id',

            // Campos JSON tratados como array
            'atributos' => 'required|array',
            'selected_skills' => 'nullable|array',
            'selected_equipment' => 'nullable|array',
            'race_choices' => 'nullable|array',

            'rolled_hp' => 'nullable|integer|min:0',

            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'inventario' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'atributos.required' => 'Os atributos finais são obrigatórios.',
            'atributos.array' => 'Formato de atributos inválido.',
            'campanha_id.required' => 'A campanha é obrigatória.',
            'campanha_id.exists' => 'Campanha não encontrada.',
            'sistema_id.required' => 'O sistema é obrigatório.',
            'sistema_id.exists' => 'Sistema não encontrado.',
        ];
    }
}
