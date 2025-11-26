<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonagemRequest extends FormRequest
{
    /**
     * Autoriza somente usuários autenticados a criar personagens.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Regras de validação para criação de personagem.
     */
    public function rules(): array
    {
        return [
            // Dados básicos
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|integer|exists:campanhas,id',
            'sistema_id' => 'required|integer|exists:sistemas,id',

            // Relações opcionais
            'raca_id' => 'nullable|integer|exists:racas,id',
            'classe_id' => 'nullable|integer|exists:classes,id',
            'origem_id' => 'nullable|integer|exists:origens,id',

            // Dados estruturados (arrays JSON)
            'atributos' => 'required|array',
            'selected_skills' => 'nullable|array',
            'selected_equipment' => 'nullable|array',
            'race_choices' => 'nullable|array',

            // Informações complementares
            'rolled_hp' => 'nullable|integer|min:0',

            // Textos
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'inventario' => 'nullable|string',
        ];
    }

    /**
     * Mensagens personalizadas de validação.
     */
    public function messages(): array
    {
        return [
            'atributos.required' => 'Os atributos finais são obrigatórios.',
            'atributos.array' => 'Formato inválido para os atributos.',

            'campanha_id.required' => 'A campanha é obrigatória.',
            'campanha_id.exists' => 'A campanha selecionada não existe.',

            'sistema_id.required' => 'O sistema é obrigatório.',
            'sistema_id.exists' => 'O sistema selecionado não existe.',
        ];
    }
}
