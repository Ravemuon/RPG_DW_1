<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Missao extends Model
{
    protected $table = 'missoes';

    protected $fillable = [
        'campanha_id',
        'user_id',
        'titulo',
        'descricao',
        'recompensa',
        'status',
    ];

    // ===================================================
    // 🔹 Relação com a campanha
    // ===================================================
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    // ===================================================
    // 🔹 Relação com o usuário (mestre que criou a missão)
    // ===================================================
    public function mestre()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ===================================================
    // 🔹 Escopos úteis
    // ===================================================

    /**
     * Escopo para missões concluídas
     */
    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    /**
     * Escopo para missões em andamento
     */
    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    /**
     * Escopo para missões pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }
}
