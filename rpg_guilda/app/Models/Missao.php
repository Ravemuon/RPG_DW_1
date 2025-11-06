<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Missao extends Model
{
    use HasFactory;

    protected $fillable = [
        'campanha_id',
        'user_id',
        'titulo',
        'descricao',
        'recompensa',
        'status',
    ];

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function mestre()
    {
        return $this->belongsTo(Usuario::class, 'user_id', 'id_usuario');
    }
}
