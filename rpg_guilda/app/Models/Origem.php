<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Origem extends Model
{
    protected $table = 'origens';

    protected $fillable = [
        'nome',
        'sistema_id',
        'descricao',
        'bonus_pericias',
        'recursos_adicionais',
        'pagina',
    ];

    protected $casts = [
        'bonus_pericias' => 'array',
        'recursos_adicionais' => 'array',
    ];

    /**
     * Relação: Uma origem pertence a um sistema.
     */
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    /**
     * Retorna as perícias afetadas por esta origem (opcional)
     * Caso o nome das perícias no JSON coincida com a tabela pericias.
     */
    public function pericias()
    {
        return Pericia::whereIn('nome', array_keys($this->bonus_pericias ?? []))->get();
    }
}
