<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMensagem extends Model
{
    use HasFactory;
    protected $table = 'chat_mensagens';

    protected $fillable = [
        'chat_id',
        'user_id',
        'mensagem'
    ];

    // ===================================================
    // Relação com o usuário que enviou a mensagem
    // ===================================================
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ===================================================
    // Relação com o chat ao qual a mensagem pertence
    // ===================================================
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
    public function buscarMensagens(Chat $chat)
    {
        // Carrega apenas o necessário
        $mensagens = $chat->mensagens()->with('user:id,nome')->orderBy('created_at', 'asc')->get([
            'id', 'chat_id', 'user_id', 'mensagem', 'created_at'
        ]);

        return response()->json($mensagens);
    }

}
