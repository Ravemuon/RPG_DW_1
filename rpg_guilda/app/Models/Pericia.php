<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pericia extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'sistema_id',
        'atributo_relacionado',
        'descricao',
    ];

    /**
     * Define o relacionamento com o modelo Sistema.
     */
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
}
