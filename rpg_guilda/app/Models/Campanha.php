<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campanha extends Model
{
    protected $table = 'campanhas';

    protected $fillable = [
        'nome',
        'sistema_id',
        'descricao',
        'status',
        'privada',
        'codigo_convite',
        'criador_id'
    ];

    protected $casts = [
        'privada' => 'boolean',
    ];

    // ===================================================
    // Relações principais
    // ===================================================
    public function sistema()
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    public function missoes()
    {
        return $this->hasMany(Missao::class, 'campanha_id');
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criador_id');
    }
    
    public function jogadores()
    {
        return $this->belongsToMany(User::class, 'campanha_usuario')
                    ->withPivot('status') // necessário para pivot_status
                    ->withTimestamps();
    }


    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'campanha_id');
    }

    public function sessoes()
    {
        return $this->hasMany(Sessao::class, 'campanha_id');
    }

    public function arquivos()
    {
        return $this->hasMany(Arquivo::class, 'campanha_id');
    }

    // ===================================================
    // Relação com o chat da campanha
    // ===================================================
    public function chat()
    {
        return $this->hasOne(Chat::class, 'campanha_id');
    }

    public function mensagens()
    {
        // Acesso direto às mensagens do chat
        return $this->hasOne(Chat::class, 'campanha_id')->with('mensagens');
    }

    // ===================================================
    // Gera código de convite se a campanha for privada
    // ===================================================
    protected static function booted()
    {
        static::creating(function ($campanha) {
            if ($campanha->privada && empty($campanha->codigo_convite)) {
                $campanha->codigo_convite = Str::upper(Str::random(6));
            }
        });
    }

    // ===================================================
    // Atributos auxiliares
    // ===================================================
    public function getSistemaRPGAttribute()
    {
        return $this->sistema->nome ?? 'Sistema Desconhecido';
    }

    public function mestre()
    {
        return $this->belongsTo(User::class, 'criador_id');
    }
}
