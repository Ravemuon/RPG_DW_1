<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Missao extends Model
{
    /**
     * Nome da tabela explícita (opcional se seguir padrão, mas aqui está correto).
     */
    protected $table = 'missoes';

    /**
     * Campos permitidos para atribuição em massa.
     */
    protected $fillable = [
        'campanha_id',
        'user_id',      // Mestre que criou a missão
        'titulo',
        'descricao',
        'recompensa',
        'status',       // pendente | em_andamento | concluida
    ];

    /**
     * ---------------------------
     * RELACIONAMENTOS
     * ---------------------------
     */

    /**
     * Cada missão pertence a uma campanha.
     */
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    /**
     * Cada missão é criada por um mestre (usuário).
     */
    public function mestre()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ---------------------------
     *    ESCOPOS PERSONALIZADOS
     * ---------------------------
     */

    /**
     * Missões concluídas.
     */
    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    /**
     * Missões em andamento.
     */
    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    /**
     * Missões pendentes.
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    /**
     * ---------------------------
     *   MÉTODOS ÚTEIS (EXTRA)
     * ---------------------------
     */

    /**
     * Atualiza o status da missão com validação simples.
     */
    public function atualizarStatus($novoStatus)
    {
        $statusValidos = ['pendente', 'em_andamento', 'concluida'];

        if (!in_array($novoStatus, $statusValidos)) {
            throw new \InvalidArgumentException("Status inválido: $novoStatus");
        }

        $this->status = $novoStatus;
        $this->save();
    }

    /**
     * Retorna true se a missão estiver concluída.
     */
    public function estaConcluida()
    {
        return $this->status === 'concluida';
    }

    /**
     * Retorna true se estiver em andamento.
     */
    public function estaEmAndamento()
    {
        return $this->status === 'em_andamento';
    }

    /**
     * Retorna true se estiver pendente.
     */
    public function estaPendente()
    {
        return $this->status === 'pendente';
    }
}
