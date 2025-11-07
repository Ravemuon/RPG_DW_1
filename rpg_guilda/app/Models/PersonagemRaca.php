<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonagemRaca extends Pivot
{
    protected $table = 'personagem_raca';

    protected $fillable = [
        'personagem_id',
        'raca_id',
        'nivel',
        'descricao_personalizada'
    ];

    // Se quiser trabalhar com timestamps do pivot
    public $timestamps = true;

    // Relação com Personagem
    public function personagem()
    {
        return $this->belongsTo(Personagem::class);
    }

    // Relação com Raça
    public function raca()
    {
        return $this->belongsTo(Raca::class);
    }
}
