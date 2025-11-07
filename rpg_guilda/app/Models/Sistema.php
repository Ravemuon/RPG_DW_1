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

    // ===============================
    // Relações
    // ===============================

    /**
     * Um Sistema tem muitas Classes.
     */
        public function classes()
    {
        return $this->hasMany(Classe::class, 'sistema_id');
    }

    /**
     * Um Sistema tem muitas Raças.
     */
    public function racas()
    {
        return $this->hasMany(Raca::class);
    }

    /**
     * Um Sistema tem muitas Origens.
     */
    public function origens()
    {
        return $this->hasMany(Origem::class);
    }

    /**
     * Um Sistema possui muitas Perícias (Many-to-Many via pivot 'sistema_pericias').
     */
    public function pericias()
    {
        return $this->belongsToMany(Pericia::class, 'sistema_pericias')->withTimestamps();
    }

    /**
     * Um Sistema tem muitos Personagens.
     */
    public function personagens()
    {
        return $this->hasMany(Personagem::class);
    }

    // ===============================
    // Métodos auxiliares
    // ===============================

    /**
     * Retorna todos os atributos configurados no sistema
     * Ex: ['Força', 'Destreza', 'Constituição', ...]
     */
    public function atributos()
    {
        $atributos = [];
        for ($i = 1; $i <= $this->max_atributos; $i++) {
            $nome = $this->{"atributo{$i}_nome"};
            if ($nome) {
                $atributos[] = $nome;
            }
        }
        return $atributos;
    }

}
