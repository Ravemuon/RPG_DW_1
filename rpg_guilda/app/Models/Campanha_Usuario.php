<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CampanhaUsuario extends Pivot
{
    protected $table = 'campanha_usuario';

    protected $fillable = [
        'user_id',
        'campanha_id',
        'status',
    ];
    public function jogadores()
    {
        return $this->belongsToMany(User::class, 'campanha_usuario') // nome correto da tabela pivot
                    ->withPivot('status')
                    ->withTimestamps();
    }

}
