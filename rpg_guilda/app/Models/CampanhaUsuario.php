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

}
