<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raca extends Model
{
    use HasFactory;

    protected $table = 'racas';

    protected $fillable = [
        'nome',
        'sistema_id',
        'descricao',
        'forca_bonus',
        'destreza_bonus',
        'constituicao_bonus',
        'inteligencia_bonus',
        'sabedoria_bonus',
        'carisma_bonus',
        'pagina'
    ];

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
}
