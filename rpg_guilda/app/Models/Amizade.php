<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amizade extends Model
{
    use HasFactory;

    protected $table = 'amizades';

    protected $fillable = [
        'user_id',
        'friend_id',
        'status'
    ];

    // Relação com usuário que envia a solicitação
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relação com usuário que recebe a solicitação
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    /**
     * Scope para buscar amizades aceitas
     */
    public function scopeAceitas($query)
    {
        return $query->where('status', 'aceito');
    }

    /**
     * Scope para buscar solicitações pendentes recebidas
     */
    public function scopePendentesRecebidas($query, $userId)
    {
        return $query->where('friend_id', $userId)
                     ->where('status', 'pendente');
    }

    /**
     * Scope para buscar solicitações pendentes enviadas
     */
    public function scopePendentesEnviadas($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->where('status', 'pendente');
    }

    /**
     * Verifica se existe amizade ou solicitação entre dois usuários
     */
    public static function existeEntre($userId, $friendId)
    {
        return self::where(function($q) use ($userId, $friendId) {
            $q->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function($q) use ($userId, $friendId) {
            $q->where('user_id', $friendId)->where('friend_id', $userId);
        })->exists();
    }
}
