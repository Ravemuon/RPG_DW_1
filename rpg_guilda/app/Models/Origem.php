<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Origem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sistemaRPG',
        'descricao',
        'bônus'
    ];

    protected $casts = [
        'bônus' => 'array', // Pontos ou vantagens em JSON
    ];

    public function personagens()
    {
        return $this->belongsToMany(\App\Models\Personagem::class, 'personagem_origem');
    }
}
