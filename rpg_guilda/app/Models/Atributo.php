<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    // Defina a tabela se o nome não for padrão
    protected $table = 'atributos';

    // Relação com o Sistema
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
}
    