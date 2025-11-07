<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Origem extends Model
{
    protected $table = 'origens';

    protected $fillable = [
        'nome',
        'sistemaRPG',
        'descricao',
        'bônus'
    ];

    protected $casts = [
        'bônus' => 'array'
    ];

    // Relação pivot com personagens
    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'personagem_origem')
                    ->withTimestamps();
    }
}
    