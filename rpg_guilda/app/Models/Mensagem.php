<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    protected $fillable = [
        'user_id',
        'campanha_id',
        'conteudo',
        'tipo',
        'lida',
    ];

    // Relação com o usuário que enviou a mensagem
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação com a campanha da mensagem
    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    // Relação com o chat
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    // Marca a mensagem como lida
    public function marcarComoLida(): void
    {
        if (!$this->lida) {
            $this->lida = true;
            $this->save();
        }
    }
}
