<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personagem extends Model
{
    use HasFactory;

    protected $table = 'personagens';

    protected $fillable = [
        'nome',
        'user_id',
        'campanha_id',
        'raca_id',
        'classe_id',
        'origem_id',
        'sistema_id',
        'atributos',
        'descricao',
        'historia',
        'personalidade',
        'inventario',
        'imagem',
        'ativo',
        'pagina',
        // campos auxiliares salvos como JSON
        'selected_skills',
        'selected_equipment',
        'race_choices',
        'rolled_hp',
    ];

    protected $casts = [
        'atributos' => 'array',
        'selected_skills' => 'array',
        'selected_equipment' => 'array',
        'race_choices' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // relacionamentos básicos (opcionais)
    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function campanha() { return $this->belongsTo(\App\Models\Campanha::class); }
    public function raca() { return $this->belongsTo(\App\Models\Raca::class); }
    public function classe() { return $this->belongsTo(\App\Models\Classe::class); }
    public function origem() { return $this->belongsTo(\App\Models\Origem::class); }
    public function sistema() { return $this->belongsTo(\App\Models\Sistema::class); }
}
