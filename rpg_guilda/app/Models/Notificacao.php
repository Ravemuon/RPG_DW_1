<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'sessao_id',
        'tipo',
        'mensagem',
        'lida',
    ];

    // Relação com o usuário que recebe a notificação
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relação com a sessão (opcional)
    public function sessao()
    {
        return $this->belongsTo(Sessao::class, 'sessao_id');
    }
}
