<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Administrador extends User
{
    use HasFactory;

    /**
     * Aplica global scope para filtrar apenas administradores
     */
    protected static function booted()
    {
        static::addGlobalScope('administrador', function ($query) {
            $query->where('tipo', 'administrador');
        });
    }

    /**
     * Campanhas que o administrador criou (se aplicável)
     */
    public function campanhasCriadas()
    {
        return $this->hasMany(Campanha::class, 'criador_id');
    }

    /**
     * Campanhas que participa como jogador
     */
    public function campanhasComoJogador()
    {
        return $this->belongsToMany(Campanha::class, 'campanha_usuario')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
