<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessao extends Model
{
    use HasFactory;

    protected $fillable = ['campanha_id', 'titulo', 'data_hora', 'status', 'resumo'];

    protected $casts = [
        'data_hora' => 'datetime'
    ];

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'sessoes_personagens')
                    ->withPivot('presente', 'resultado')
                    ->withTimestamps();
    }
}
