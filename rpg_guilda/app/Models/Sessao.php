<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessao extends Model
{
    protected $table = 'sessoes';

    protected $fillable = [
        'campanha_id',
        'titulo',
        'data_hora',
        'status',
        'criado_por',
        'resumo'
    ];

    protected $casts = [
        'data_hora' => 'datetime',
        'resumo' => 'string',
    ];

    // --- RELACIONAMENTOS ---

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    /**
     * Relacionamento N:N com Personagens.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'sessoes_personagens')
                    ->withPivot('resultado')
                    ->withTimestamps();
    }

    /**
     * Relacionamento N:N com Usuários/Jogadores para rastrear PRESENÇA.
     * Usa a tabela pivot 'sessao_jogador_presenca'.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function presencas()
    {
        return $this->belongsToMany(User::class, 'sessao_jogador_presenca', 'sessao_id', 'jogador_id')
                    ->withPivot('confirmou_presenca')
                    ->withTimestamps();
    }

    // --- SCOPES ---

    public function scopeAgendadas($query)
    {
        return $query->where('status', 'agendada');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('status', 'cancelada');
    }
}
