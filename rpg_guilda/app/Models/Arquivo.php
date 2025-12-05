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

    // Relacionamento com o usuário que enviou o arquivo
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relacionamento com a campanha à qual o arquivo pertence
    public function campanha()
    {
        return $this->belongsTo(Campanha::class, 'campanha_id');
    }

    // Retorna a URL completa do arquivo
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->caminho);
    }
}
