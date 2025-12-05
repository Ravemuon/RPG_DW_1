<?php
// App/Http/Requests/StoreStep3Request.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStep3Request extends FormRequest
{
    const ATRIBUTOS_PADRAO = ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            // O contêiner de atributos deve ser um array
            'atributos_pontuacoes' => ['required', 'array', 'size:6'],
        ];

        // Aplica validação para cada atributo (F, D, C, I, S, C) dentro do array
        foreach (self::ATRIBUTOS_PADRAO as $atributo) {
            $rules['atributos_pontuacoes.' . $atributo] = [
                'required', 
                'integer', 
                'min:1', 
                'max:30' // Ajuste o max conforme a regra do seu sistema
            ];
        }

        return $rules;
    }
}