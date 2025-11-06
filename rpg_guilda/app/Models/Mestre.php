<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mestre extends User
{
    use HasFactory;

    // Global scope: filtra automaticamente usuários do tipo "mestre"
    protected static function booted()
    {
        static::addGlobalScope('mestre', function ($query) {
            $query->where('tipo', 'mestre');
        });
    }

    // Relação com campanhas que o usuário criou (como mestre)
    public function campanhas()
    {
        return $this->hasMany(Campanha::class, 'criador_id');
    }

    // Relação com campanhas que participa como jogador (opcional)
    public function campanhasComoJogador()
    {
        return $this->belongsToMany(Campanha::class, 'campanha_usuario')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
