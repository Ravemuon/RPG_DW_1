<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'campanha_id',
        'nome',
    ];

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function mensagens()
    {
        return $this->hasMany(Mensagem::class);
    }
}
