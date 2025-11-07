<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raca extends Model
{
    use HasFactory;

    protected $table = 'racas';

    protected $fillable = [
        'nome',
        'sistema_rpg',
        'descricao',
        'forca_bonus',
        'destreza_bonus',
        'constituicao_bonus',
        'inteligencia_bonus',
        'sabedoria_bonus',
        'carisma_bonus',
    ];

    // Se quiser relacionar com personagens
    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'raca_id');
    }
}
