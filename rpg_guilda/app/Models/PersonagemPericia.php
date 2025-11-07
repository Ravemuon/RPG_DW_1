<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pericia extends Model
{
    protected $fillable = [
        'nome', 'sistemaRPG', 'automatica', 'formula'
    ];

    protected $casts = [
        'formula' => 'array'
    ];

    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'personagem_pericias')
                    ->withPivot('valor', 'definida')
                    ->withTimestamps();
    }
}
