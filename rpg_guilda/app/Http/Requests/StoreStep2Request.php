<?php

// App/Http/Requests/StoreStep2Request.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStep2Request extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'raca_id' => ['required', 'integer', 'exists:racas,id'],
            'classe_id' => ['required', 'integer', 'exists:classes,id'],
            
            // CORRIGIDO: Origem é opcional (pode ser null)
            'origem_id' => ['nullable', 'integer', 'exists:origens,id'],
            
            'bonus_proficiencia' => ['required', 'integer', 'min:1', 'max:6'],
            
            // REMOVIDO: pericias_classe_selecionadas
        ];
    }
}