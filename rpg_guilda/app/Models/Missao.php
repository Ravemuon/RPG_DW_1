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

    // Relação com a campanha
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    // Relação com o usuário que criou a missão (mestre)
    public function mestre()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Escopos para filtrar status
    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    // Atualiza status da missão
    public function atualizarStatus($novoStatus)
    {
        $statusValidos = ['pendente', 'em_andamento', 'concluida'];
        if (!in_array($novoStatus, $statusValidos)) {
            throw new \InvalidArgumentException("Status inválido: $novoStatus");
        }
        $this->status = $novoStatus;
        $this->save();
    }

    // Verificações de status
    public function estaConcluida() { return $this->status === 'concluida'; }
    public function estaEmAndamento() { return $this->status === 'em_andamento'; }
    public function estaPendente() { return $this->status === 'pendente'; }
}
