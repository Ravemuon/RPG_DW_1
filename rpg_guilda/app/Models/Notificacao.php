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

    // Relacionamento com o usuário destinatário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relacionamento com sessão associada
    public function sessao()
    {
        return $this->belongsTo(Sessao::class, 'sessao_id');
    }

    // Marca a notificação como lida
    public function marcarComoLida()
    {
        $this->lida = true;
        $this->save();
    }

    // Cria nova notificação no banco
    public static function criarNotificacao($usuarioId, $mensagem, $tipo = 'geral', $sessaoId = null)
    {
        try {
            return self::create([
                'usuario_id' => $usuarioId,
                'tipo' => $tipo,
                'mensagem' => $mensagem,
                'lida' => false,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao criar notificação: ' . $e->getMessage());
            return false;
        }
    }
}
