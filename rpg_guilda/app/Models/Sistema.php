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
        'atributos', // Coluna JSON da migration
        'usa_sanidade', // Coluna boolean da migration
        'formula_pontos_vida', // Coluna string da migration
        'recursos', // Coluna JSON da migration
        'regras_opcionais', // Coluna JSON da migration
        // As colunas de atributos individuais (atributo1_nome, etc.) e max_atributos foram removidas
    ];

    protected $casts = [
        'atributos' => 'array',
        'recursos' => 'array',
        'regras_opcionais' => 'array',
        'usa_sanidade' => 'boolean', // Conversão para boolean
    ];

    // --- Relações ---

    public function pericias()
    {
        // Certifique-se de que a foreign key está correta. Assumindo 'sistema_id'.
        return $this->hasMany(Pericia::class, 'sistema_id');
    }

    public function classes()
    {
        return $this->hasMany(Classe::class, 'sistema_id');
    }

    public function racas()
    {
        // Se a foreign key for 'sistema_id', use: $this->hasMany(Raca::class, 'sistema_id');
        return $this->hasMany(Raca::class);
    }

    public function origens()
    {
        // Se a foreign key for 'sistema_id', use: $this->hasMany(Origem::class, 'sistema_id');
        return $this->hasMany(Origem::class);
    }

    public function personagens()
    {
        // Assumindo que Personagem é um model relacionado
        return $this->hasMany(Personagem::class);
    }
}
