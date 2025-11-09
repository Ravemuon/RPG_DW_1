<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Missao extends Model
{
    // Definindo o nome da tabela associada ao modelo
    protected $table = 'missoes';

    // Atributos que podem ser atribuídos em massa
    protected $fillable = [
        'campanha_id',   // ID da campanha à qual a missão pertence
        'user_id',       // ID do usuário (mestre) que criou a missão
        'titulo',        // Título da missão
        'descricao',     // Descrição detalhada da missão
        'recompensa',    // Recompensa oferecida ao completar a missão
        'status',        // Status da missão (pendente, em andamento, concluída)
    ];

    /**
     * Define a relação de "Missao" com "Campanha".
     * Uma missão pertence a uma campanha específica.
     */
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    /**
     * Define a relação de "Missao" com o "User" (mestre).
     * Uma missão é criada por um mestre (usuário).
     */
    public function mestre()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Escopo para buscar missões concluídas.
     * Filtra as missões que têm o status "concluida".
     */
    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluida');
    }

    /**
     * Escopo para buscar missões em andamento.
     * Filtra as missões que têm o status "em_andamento".
     */
    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    /**
     * Escopo para buscar missões pendentes.
     * Filtra as missões que têm o status "pendente".
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }
}
