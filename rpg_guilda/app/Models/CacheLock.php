<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheLock extends Model
{
    protected $table = 'cache_locks';

    // Chave primária é 'key', não auto-incrementa, tipo string
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos atribuíveis em massa
    protected $fillable = [
        'key',
        'owner',
        'expiration',
    ];

    // Sem timestamps
    public $timestamps = false;
}
