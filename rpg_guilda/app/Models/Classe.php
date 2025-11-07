<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'nome',
        'sistemaRPG',
        'descricao',
        'forca',
        'destreza',
        'constituicao',
        'inteligencia',
        'sabedoria',
        'carisma',
        'agilidade',
        'intelecto',
        'presenca',
        'vigor',
        'nex',
        'sanidade',
        'aspects',
        'stunts',
        'fate_points',
        'atributos_custom',
        'poderes'
    ];

    protected $casts = [
        'aspects' => 'array',
        'stunts' => 'array',
        'atributos_custom' => 'array',
        'poderes' => 'array',
    ];

    // Relação com personagens
    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'classe');
    }
}
