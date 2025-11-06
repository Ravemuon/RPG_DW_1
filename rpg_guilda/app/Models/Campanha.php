<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campanha extends Model
{
    protected $table = 'campanhas';
    protected $fillable = [
        'nome',
        'sistemaRPG',
        'descricao',
        'status',
        'privada',
        'codigo_convite',
        'criador_id'
    ];

    // Relação com o mestre (criador)
    public function criador()
    {
        return $this->belongsTo(User::class, 'criador_id');
    }

    // Relação com jogadores da campanha
    public function jogadores()
    {
        return $this->belongsToMany(User::class, 'campanha_usuario')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Relação com personagens da campanha
    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'campanha_id');
    }

    // Relação com sessões
    public function sessoes()
    {
        return $this->hasMany(Sessao::class, 'id_campanha');
    }

    // Relação com arquivos da campanha
    public function arquivos()
    {
        return $this->hasMany(Arquivo::class, 'campanha_id');
    }

    // Gera código de convite se a campanha for privada
    public static function booted()
    {
        static::creating(function ($campanha) {
            if ($campanha->privada && empty($campanha->codigo_convite)) {
                $campanha->codigo_convite = Str::upper(Str::random(10));
            }
        });
    }
}
