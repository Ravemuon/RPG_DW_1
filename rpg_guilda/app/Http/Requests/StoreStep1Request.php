<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep1Request extends FormRequest
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
            'nome' => ['required', 'string', 'max:100'],
            'campanha_id' => ['required', 'exists:campanhas,id'],
            'sistema_id' => ['required', 'exists:sistemas,id'],
            'nivel' => ['nullable', 'integer', 'min:1'],
            'xp' => ['nullable', 'integer', 'min:0'],
            'descricao' => ['nullable', 'string'],
            'historia' => ['nullable', 'string'],
            'personalidade' => ['nullable', 'string'],
            'imagem_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // 2MB
            'pagina' => ['nullable', 'string', 'max:50'],
            'ativo' => ['sometimes', 'in:0,1'], // Aceita 0 ou 1
        ];
    }

    /**
     * Define mensagens de erro personalizadas para as regras.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do personagem é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'campanha_id.required' => 'A campanha é obrigatória.',
            'campanha_id.exists' => 'A campanha selecionada é inválida.',
            'sistema_id.required' => 'O sistema é obrigatório.',
            'sistema_id.exists' => 'O sistema selecionado é inválido.',
            'nivel.min' => 'O nível deve ser no mínimo 1.',
            'xp.min' => 'A experiência (XP) não pode ser negativa.',
            'imagem_file.image' => 'O arquivo deve ser uma imagem válida.',
            'imagem_file.mimes' => 'A imagem deve ser do tipo: JPG, PNG, GIF ou WebP.',
            'imagem_file.max' => 'A imagem não pode ter mais de 2MB.',
            'pagina.max' => 'A referência de página não pode ter mais de 50 caracteres.',
            'ativo.in' => 'O status ativo deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Prepara os dados para validação.
     */
    protected function prepareForValidation()
    {
        // Converte campos vazios para null
        $this->merge([
            'nivel' => $this->input('nivel') === '' ? null : (int) $this->input('nivel'),
            'xp' => $this->input('xp') === '' ? null : (int) $this->input('xp'),
            'ativo' => $this->has('ativo') && $this->input('ativo') == '1',
        ]);
    }
}
