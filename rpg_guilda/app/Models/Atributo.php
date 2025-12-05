<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    // Nome da tabela no banco de dados
    protected $table = 'atributos';

    // Relacionamento com o sistema ao qual pertence o atributo
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
}
