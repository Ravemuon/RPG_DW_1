<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    use HasFactory;

    protected $table = 'sistemas';

    protected $fillable = [
        'nome',
        'descricao',
        'foco',
        'mecanica_principal',
        'complexidade',
        'atributos',
        'usa_sanidade',
        'formula_pontos_vida',
        'recursos',
        'regras_opcionais',
    ];

    protected $casts = [
        'atributos' => 'array',
        'recursos' => 'array',
        'regras_opcionais' => 'array',
        'usa_sanidade' => 'boolean',
    ];

    public function pericias()
    {
        return $this->hasMany(Pericia::class, 'sistema_id');
    }

    public function classes()
    {
        return $this->hasMany(Classe::class, 'sistema_id');
    }

    public function racas()
    {
        return $this->hasMany(Raca::class, 'sistema_id');
    }

    public function origens()
    {
        return $this->hasMany(Origem::class, 'sistema_id');
    }

    public function personagens()
    {
        return $this->hasMany(Personagem::class, 'sistema_id');
    }
}
