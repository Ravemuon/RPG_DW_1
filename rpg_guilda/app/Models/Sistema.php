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
        'max_atributos',
        'atributo1_nome',
        'atributo2_nome',
        'atributo3_nome',
        'atributo4_nome',
        'atributo5_nome',
        'atributo6_nome',
        'pagina',
        'regras_opcionais',
    ];

    protected $casts = [
        'atributos' => 'array',
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
        return $this->hasMany(Raca::class);
    }

    public function origens()
    {
        return $this->hasMany(Origem::class);
    }

    public function personagens()
    {
        return $this->hasMany(Personagem::class);
    }


    public function atributosRelacionados()
    {
        return $this->hasMany(Atributo::class);
    }

}
