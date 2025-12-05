<?php

// App/Http/Requests/StoreStep1Request.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStep1Request extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'campanha_id' => ['required', 'exists:campanhas,id'],
            'sistema_id' => ['required', 'exists:sistemas,id'],
            
            // Nível e XP são opcionais/numéricos
            'nivel' => ['nullable', 'integer', 'min:1', 'max:20'], 
            'xp' => ['nullable', 'integer', 'min:0'],
            
            'descricao' => ['nullable', 'string', 'max:1000'],
            'historia' => ['nullable', 'string'],
            'personalidade' => ['nullable', 'string', 'max:1000'],

            // Validação para o upload de imagem
            'imagem_upload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'imagem' => ['nullable', 'string'], // Path temporário da imagem (se houver)

            'pagina' => ['nullable', 'string', 'max:50'],
            
            // O campo 'ativo' é definido como 0 ou 1 no prepareForValidation
            'ativo' => ['required', 'in:0,1'],
        ];
    }

    protected function prepareForValidation()
    {
        // Converte strings vazias para null e garante que sejam inteiros
        $this->merge([
            'nivel' => $this->input('nivel') === '' ? null : (int) $this->input('nivel'),
            'xp' => $this->input('xp') === '' ? null : (int) $this->input('xp'),

            // Lógica para o switch: Se 'ativo_checkbox_only' foi enviado (checked), 'ativo' é 1, senão é 0.
            'ativo' => $this->has('ativo_checkbox_only') ? 1 : 0,
        ]);

        $this->request->remove('ativo_checkbox_only');
    }
}