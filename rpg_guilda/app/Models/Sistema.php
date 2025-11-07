<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Importando todos os Models usados nas relações
use App\Models\Classe;
use App\Models\Raca;
use App\Models\Origem;
use App\Models\Pericia;
use App\Models\Personagem;

class Sistema extends Model
{
    use HasFactory;

    protected $table = 'sistemas';

    protected $fillable = [
        'nome', 'descricao', 'foco', 'mecanica_principal', 'complexidade',
    ];

    /**
     * Um Sistema tem muitas Classes.
     */
    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    /**
     * Um Sistema tem muitas Origens.
     */
    public function origens()
    {
        return $this->hasMany(Origem::class);
    }

    /**
     * Um Sistema tem muitas Raças.
     * As Raças definem atributos base do personagem.
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
