<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Campanha extends Model
{
    use SoftDeletes;

    protected $table = 'campanhas';

    // Campos atribuíveis em massa
    protected $fillable = [
        'nome',
        'sistema_id',
        'descricao',
        'status',
        'privada',
        'codigo_convite',
        'criador_id',
        'pagina',
    ];

    // Converte 'privada' para booleano
    protected $casts = [
        'privada' => 'boolean',
    ];

    // Relações
    public function sistema() { return $this->belongsTo(Sistema::class, 'sistema_id'); }
    public function missoes() { return $this->hasMany(Missao::class, 'campanha_id'); }
    public function criador() { return $this->belongsTo(User::class, 'criador_id'); }
    public function jogadores() {
        return $this->belongsToMany(User::class, 'campanha_usuario')
                    ->withPivot('status')
                    ->withTimestamps();
    }
    public function solicitacoes() {
        return $this->belongsToMany(User::class, 'campanha_usuario')
                    ->withPivot('status')
                    ->wherePivot('status', 'pendente')
                    ->withTimestamps();
    }
    public function personagens() { return $this->hasMany(Personagem::class, 'campanha_id'); }
    public function sessoes() { return $this->hasMany(Sessao::class, 'campanha_id'); }
    public function arquivos() { return $this->hasMany(Arquivo::class, 'campanha_id'); }
    public function chat() { return $this->hasOne(Chat::class, 'campanha_id'); }
    public function mensagens() { return $this->hasOne(Chat::class, 'campanha_id')->with('mensagens'); }

    // Gera código de convite para campanhas privadas
    protected static function booted()
    {
        static::creating(function ($campanha) {
            if ($campanha->privada && empty($campanha->codigo_convite)) {
                $campanha->codigo_convite = Str::upper(Str::random(6));
            }
        });
    }

    // Retorna nome do sistema
    public function getSistemaRPGAttribute() {
        return $this->sistema->nome ?? 'Sistema Desconhecido';
    }

    // Retorna o mestre da campanha
    public function mestre() { return $this->belongsTo(User::class, 'criador_id'); }
    
    public function participantes()
    {
        return $this->jogadores()->whereIn('campanha_usuario.status', ['ativo', 'mestre']);
    }
}
