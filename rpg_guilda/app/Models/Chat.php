<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $fillable = [
        'campanha_id',
        'nome'
    ];

    // ===================================================
    // Relação com a campanha
    // ===================================================
    public function campanha()
    {
        return $this->belongsTo(Campanha::class, 'campanha_id');
    }

    // ===================================================
    // Relação com mensagens do chat
    // ===================================================
    public function mensagens()
    {
        return $this->hasMany(ChatMensagem::class, 'chat_id');
    }
}
