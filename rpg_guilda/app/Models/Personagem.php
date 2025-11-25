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
        'ativo',
        'pagina',
        'imagem',
        'historia',
        'personalidade',
        'inventario',
    ];


    protected $casts = [
        'atributos' => 'array',
        'ativo' => 'boolean',
        'inventario' => 'array',       // <- RECOMENDADO
        'personalidade' => 'array',    // <- SE FOR JSON
        'historia' => 'string',        // <- TEXTO LONGO
    ];

    // Relação com usuário
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relação com campanha
    public function campanha() {
        return $this->belongsTo(Campanha::class);
    }

    // Relação com raça
    public function raca() {
        return $this->belongsTo(Raca::class);
    }

    // Relação com classe
    public function classe() {
        return $this->belongsTo(Classe::class);
    }

    // Relação com origem
    public function origem() {
        return $this->belongsTo(Origem::class);
    }

    // Relação com sistema
    public function sistema() {
        return $this->belongsTo(Sistema::class);
    }

    // Relação com perícias
    public function pericias() {
        return $this->belongsToMany(Pericia::class, 'personagem_pericia')
                    ->withPivot('nivel', 'proficiente')
                    ->withTimestamps();
    }
}
