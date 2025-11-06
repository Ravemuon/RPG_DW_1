<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jogador extends User
{
    use HasFactory;

    // Global scope: filtra automaticamente usuários do tipo "jogador"
    protected static function booted()
    {
        static::addGlobalScope('jogador', function ($query) {
            $query->where('tipo', 'jogador');
        });
    }

    // Relação com campanhas
    public function campanhas()
    {
        return $this->belongsToMany(Campanha::class, 'campanha_usuario')
                    ->withPivot('status') // pendente/aprovado
                    ->withTimestamps();
    }
}
