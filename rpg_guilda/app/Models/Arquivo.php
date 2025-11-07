<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'campanha_id',
        'nome_original',
        'caminho',
        'tipo',
        'tamanho',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class, 'campanha_id');
    }

    // Helper para URL completa
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->caminho);
    }
}
