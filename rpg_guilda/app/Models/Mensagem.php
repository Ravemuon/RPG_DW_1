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

    /* ------------------- Relacionamentos ------------------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /* ------------------- Lógica ------------------- */

    public function marcarComoLida(): void
    {
        if (!$this->lida) {
            $this->lida = true;
            $this->save();
        }
    }
}
