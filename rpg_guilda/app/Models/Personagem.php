<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personagem extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'nivel', 'imagem', 'classe_id', 'missao_id'];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function missao()
    {
        return $this->belongsTo(Missao::class);
    }
}
