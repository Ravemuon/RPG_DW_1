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
        'classe',
        'origem',
        'sistema_rpg',
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

    // Relação com perícias
    public function pericias() {
        return $this->belongsToMany(Pericia::class, 'personagem_pericia')
                    ->withPivot('nivel', 'proficiente')
                    ->withTimestamps();
    }
}
