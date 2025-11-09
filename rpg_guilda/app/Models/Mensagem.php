<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    protected $fillable = [
        'user_id',        // ID do usuário que enviou a mensagem
        'campanha_id',    // ID da campanha (se aplicável)
        'conteudo',       // O conteúdo da mensagem
        'tipo',           // Tipo da mensagem: privada, campanha, chat
        'lida',           // Status de leitura (se for uma mensagem privada)
    ];

    /**
     * Relacionamento com o usuário que enviou a mensagem.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com a campanha (se for uma mensagem de campanha).
     */
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    /**
     * Relacionamento com o chat (se for uma mensagem de chat).
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Método para marcar a mensagem como lida (aplicável para mensagens privadas).
     */
    public function marcarComoLida()
    {
        $this->lida = true;
        $this->save();
    }
}
