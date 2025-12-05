<?php

// App/Http/Requests/StoreStep4Request.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStep4Request extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            // Pontos de vida totais são obrigatórios
            'vida' => ['required', 'integer', 'min:1', 'max:999'],
            
            // Sanidade e Sorte são opcionais
            'sanidade' => ['nullable', 'integer', 'min:0', 'max:999'],
            'sorte' => ['nullable', 'integer', 'min:0', 'max:999'],
            
            // Se houver Mana, Ki, Fúria, etc.
            'recurso_adicional' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
    
    protected function prepareForValidation()
    {
        // Garante que campos opcionais vazios sejam tratados como null
        $this->merge([
            'sanidade' => $this->input('sanidade') === '' ? null : (int) $this->input('sanidade'),
            'sorte' => $this->input('sorte') === '' ? null : (int) $this->input('sorte'),
        ]);
    }
}