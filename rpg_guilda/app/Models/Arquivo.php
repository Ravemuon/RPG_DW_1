<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    protected $table = 'arquivos';
    protected $primaryKey = 'id_arquivo';
    protected $fillable = [
        'usuario_id',
        'campanha_id',
        'nome_original',
        'caminho',
        'tipo',
        'tamanho'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class, 'campanha_id');
    }

    /**
     * Retorna a URL do arquivo (local ou externa)
     */
    public function url()
    {
        if ($this->tipo === 'url') {
            return $this->caminho;
        }

        return asset('storage/' . $this->caminho);
    }
}
