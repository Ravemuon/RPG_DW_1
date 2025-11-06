<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    protected $table = 'cache';
    protected $primaryKey = 'key';
    public $incrementing = false; // chave string não auto-incrementa
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'expiration',
    ];

    public $timestamps = false; // tabela não tem created_at/updated_at
}
