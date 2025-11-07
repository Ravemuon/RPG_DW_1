<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    use HasFactory;

    protected $table = 'sistemas';

    protected $fillable = [
        'nome', 'descricao', 'foco', 'mecanica_principal', 'complexidade',
        'atributo1_nome', 'atributo2_nome', 'atributo3_nome',
        'atributo4_nome', 'atributo5_nome', 'atributo6_nome',
    ];

    // --- NOVOS RELACIONAMENTOS ---

    /**
     * Um Sistema tem muitas Classes.
     */
    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    /**
     * Um Sistema tem muitas Raças.
     */
    public function racas()
    {
        return $this->hasMany(Raca::class);
    }

    /**
     * Um Sistema tem muitas Perícias.
     */
    public function pericias()
    {
        return $this->hasMany(Pericia::class);
    }

    /**
     * Um Sistema tem muitos Personagens.
     */
    public function personagens()
    {
        return $this->hasMany(Personagem::class);
    }
}
