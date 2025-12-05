<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Origem extends Model
{
    use HasFactory;

    protected $table = 'origens';

    protected $fillable = [
        'nome',
        'sistema_id',
        'descricao',
        'pericias_iniciais',
        'recursos_adicionais',
        'pagina',
    ];

    protected $casts = [
        'pericias_iniciais' => 'array',
        'recursos_adicionais' => 'array',
    ];

    // Origem pertence a um sistema
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
}
