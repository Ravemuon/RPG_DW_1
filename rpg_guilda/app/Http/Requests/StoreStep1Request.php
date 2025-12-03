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
            'nivel' => ['nullable', 'integer', 'min:1', 'max:20'], // Adicionado max:20 (comum)
            'xp' => ['nullable', 'integer', 'min:0'],
            'descricao' => ['nullable', 'string', 'max:1000'], // Adicionado max:1000 (do Blade)
            'historia' => ['nullable', 'string'],
            'personalidade' => ['nullable', 'string', 'max:1000'], // Adicionado max:1000 (do Blade)

            // CORREÇÃO 1: Nome do campo no Blade é 'imagem_upload'
            'imagem_upload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // 2MB

            // CORREÇÃO 2: Validação do campo oculto 'imagem' que armazena o path temporário.
            'imagem' => ['nullable', 'string'],

            'pagina' => ['nullable', 'string', 'max:50'],

            // O campo final 'ativo' é criado no prepareForValidation.
            // O campo 'ativo' oculto no formulário é '0', e o checkbox é 'ativo_checkbox_only'.
            'ativo' => ['required', 'in:0,1'],
            'ativo_checkbox_only' => ['nullable', 'in:1'], // Apenas para checar se o checkbox foi enviado
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
            'sistema_id.required' => 'O sistema é obrigatório.',
            'nivel.min' => 'O nível deve ser no mínimo 1.',
            'xp.min' => 'A experiência (XP) não pode ser negativa.',

            // CORREÇÃO 3: Mensagens ajustadas para 'imagem_upload'
            'imagem_upload.image' => 'O arquivo deve ser uma imagem válida.',
            'imagem_upload.mimes' => 'A imagem deve ser do tipo: JPG, PNG ou WebP.',
            'imagem_upload.max' => 'A imagem não pode ter mais de 2MB.',

            'pagina.max' => 'A referência de página não pode ter mais de 50 caracteres.',
            'ativo.required' => 'O status ativo é obrigatório.',
        ];
    }

    /**
     * Prepara os dados para validação e padroniza o campo 'ativo'.
     */
    protected function prepareForValidation()
    {
        // Converte campos vazios para null e garante que sejam inteiros
        $this->merge([
            'nivel' => $this->input('nivel') === '' ? null : (int) $this->input('nivel'),
            'xp' => $this->input('xp') === '' ? null : (int) $this->input('xp'),

            // Lógica para o switch: O campo 'ativo' será 1 se 'ativo_checkbox_only' for enviado, caso contrário, será 0.
            // Isso garante que o campo 'ativo' validado seja um booleano limpo (0 ou 1).
            'ativo' => $this->has('ativo_checkbox_only') ? 1 : 0,
        ]);

        // Remove a chave 'ativo_checkbox_only' do request para que apenas o 'ativo' finalizado seja salvo.
        $this->request->remove('ativo_checkbox_only');
    }
}
