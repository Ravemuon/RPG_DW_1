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
        'modificadores_atributos',
        'tipo_bonus',
        'bonus_livre',
        'pagina'
    ];

    protected $casts = [
        'modificadores_atributos' => 'array',
    ];

    // RELACIONAMENTOS

    /**
     * Sistema ao qual esta raça pertence
     */
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    /**
     * Personagens que usam esta raça
     */
    public function personagens()
    {
        return $this->hasMany(Personagem::class);
    }
}
