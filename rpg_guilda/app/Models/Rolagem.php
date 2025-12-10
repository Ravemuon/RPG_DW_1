<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rolagem extends Model
{
    use HasFactory;

    protected $table = 'rolagens';

    protected $fillable = [
        'user_id',
        'campanha_id',
        'personagem_id',
        'tipo_dado',
        'quantidade',
        'modificador',
        'resultado',
        'descricao',
        'tipo_rolagem',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'modificador' => 'integer',
        'resultado' => 'integer',
    ];

    /* ---------------------------------
       RELACIONAMENTOS
    -----------------------------------*/

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function personagem()
    {
        return $this->belongsTo(Personagem::class);
    }
}
