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
        'descricao',
        'modificadores_atributos',
        'tipo_bonus',
        'bonus_livre',
        'pagina',
        'sistema_id',
    ];

    protected $casts = [
        'modificadores_atributos' => 'array',
    ];

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }
    
    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'raca_id');
    }
}