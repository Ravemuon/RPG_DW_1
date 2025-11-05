<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Missao extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'descricao', 'recompensa'];

    public function personagem()
    {
        return $this->hasOne(Personagem::class);
    }
}
