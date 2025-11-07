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

    // ===================================================
    // 🔹 Relação com a campanha
    // ===================================================
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    // ===================================================
    // 🔹 Relação com o criador da sessão (usuário)
    // ===================================================
    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    // ===================================================
    // 🔹 Relação com personagens presentes na sessão
    // ===================================================
    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'sessoes_personagens')
                    ->withPivot('presente', 'resultado')
                    ->withTimestamps();
    }

    // ===================================================
    // 🔹 Escopos úteis
    // ===================================================

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
