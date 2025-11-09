<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonagemOrigem extends Model
{
    protected $fillable = [
        'nome', 'sistemaRPG', 'descricao', 'bônus'
    ];

    protected $casts = [
        'bônus' => 'array'
    ];

    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'personagem_origem')
                    ->withTimestamps();
    }
}
