<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    // Nome da tabela
    protected $table = 'cache';

    // Chave primária é 'key', não auto-incrementa, tipo string
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos atribuíveis em massa
    protected $fillable = [
        'key',
        'value',
        'expiration',
    ];

    // Desabilita timestamps, pois a tabela não possui created_at/updated_at
    public $timestamps = false;
}
