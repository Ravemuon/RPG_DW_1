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

    public $timestamps = true;

    public function personagem()
    {
        return $this->belongsTo(Personagem::class);
    }

    public function raca()
    {
        return $this->belongsTo(Raca::class);
    }
}
