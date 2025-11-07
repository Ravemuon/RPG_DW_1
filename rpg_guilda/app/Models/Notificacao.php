<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    protected $fillable = [
        'usuario_id',
        'sessao_id',
        'tipo',
        'mensagem',
        'lida',
    ];

    protected $casts = [
        'lida' => 'boolean',
    ];

    // Relação com o usuário destinatário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relação opcional com a sessão
    public function sessao()
    {
        return $this->belongsTo(Sessao::class, 'sessao_id');
    }

    // Marcar como lida
    public function marcarComoLida()
    {
        $this->lida = true;
        $this->save();
    }

}
