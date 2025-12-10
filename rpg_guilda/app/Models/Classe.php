<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Sistema;
use App\Models\Pericia;

class Classe extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = [
        'nome',
        'sistema_id',
        'descricao',
        'dado_vida',
        'pericias_iniciais',
        'equipamento_inicial',
        'usa_magia',
        'atributos_bonus',
        'poderes',
        'pagina',
    ];

    protected $casts = [
        'usa_magia' => 'boolean',
        'pericias_iniciais' => 'array',
        'equipamento_inicial' => 'array',
        'atributos_bonus' => 'array',
        'poderes' => 'array',
    ];

    // Relação com Sistema
    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    // Relação com Pericias
    public function pericias(): BelongsToMany
    {
        return $this->belongsToMany(Pericia::class, 'classe_pericia', 'classe_id', 'pericia_id');
    }

    public function personagens(): HasMany 
    {
        return $this->hasMany(Personagem::class);
    }
}
