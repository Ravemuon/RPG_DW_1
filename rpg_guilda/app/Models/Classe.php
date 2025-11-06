<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sistemaRPG',
        'descricao',
        'forca','destreza','constituicao','inteligencia','sabedoria','carisma',
        'agilidade','intelecto','presenca','vigor','nex','sanidade',
        'forca_cth','destreza_cth','poder','constituição_cth','aparencia','educacao','tamanho','inteligencia_cth','sanidade_cth','pontos_vida',
        'aspects','stunts','fate_points',
        'atributos_custom','poderes',
    ];

    protected $casts = [
        'aspects' => 'array',
        'stunts' => 'array',
        'atributos_custom' => 'array',
        'poderes' => 'array',
    ];

    public function personagens()
    {
        return $this->hasMany(\App\Models\Personagem::class);
    }
}
