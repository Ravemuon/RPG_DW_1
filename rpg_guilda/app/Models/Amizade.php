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
}
