<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CampanhaUsuario extends Pivot
{
    protected $table = 'campanha_usuario';

    protected $fillable = [
        'user_id',
        'campanha_id',
        'status',
    ];

    // Removida a função jogadores(), pois ela não deve existir em um Model Pivot.
    // O Model Pivot serve para extender a tabela intermediária com métodos e acessores, não para redefinir o BelongsToMany.

    // Se você precisasse de relações do pivot para as tabelas principais:
    /*
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    */
}
