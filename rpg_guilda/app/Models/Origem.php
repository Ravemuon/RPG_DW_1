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
        'pagina',
    ];

    /**
     * Relacionamento com o modelo Sistema
     */
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
}
