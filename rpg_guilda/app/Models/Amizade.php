<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amizade extends Model
{
    use HasFactory;

    protected $table = 'amizades';

    protected $fillable = ['user_id', 'friend_id', 'status'];

    // Relacionamento com o usuário que envia a solicitação
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relacionamento com o usuário que recebe a solicitação
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
