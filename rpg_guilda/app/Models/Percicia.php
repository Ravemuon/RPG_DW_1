<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pericia extends Model
{
    use HasFactory;

    protected $table = 'pericias';
    protected $fillable = [
        'nome',
        'sistemaRPG',
        'automatica',
        'formula',
    ];

    protected $casts = [
        'automatica' => 'boolean',
        'formula' => 'array',
    ];

    // Relação com personagens via pivot
    public function personagens()
    {
        return $this->belongsToMany(Personagem::class, 'personagem_pericias')
                    ->withPivot('valor', 'definida')
                    ->withTimestamps();
    }
}
