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
        'pericias_iniciais',     // <-- correto
        'recursos_adicionais',   // <-- correto
        'pagina',
    ];

    protected $casts = [
        'pericias_iniciais' => 'array',     // <-- FALTAVA
        'recursos_adicionais' => 'array',
    ];

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    public function pericias()
    {
        return $this->belongsToMany(Pericia::class, 'origem_pericia')
                    ->withPivot('bonus');
    }
}
