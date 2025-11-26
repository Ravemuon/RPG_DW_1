<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personagem extends Model
{
    use HasFactory;

    protected $table = 'personagens';

    protected $fillable = [
        'nome',
        'user_id',
        'sistema_id',
        'campanha_id',
        'nivel',
        'xp',
        'bonus_proficiencia',

        'raca_id',
        'classe_id',
        'origem_id',

        'sanidade',
        'sorte',

        'descricao',
        'historia',
        'personalidade',
        'inventario',
        'imagem',
        'ativo',
        'pagina',

        'atributos',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'atributos' => 'array',
        'ativo' => 'boolean',
    ];

    /* ------------------- Relacionamentos ------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    public function campanha(): BelongsTo
    {
        return $this->belongsTo(Campanha::class, 'campanha_id');
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function raca(): BelongsTo
    {
        return $this->belongsTo(Raca::class, 'raca_id');
    }

    public function origem(): BelongsTo
    {
        return $this->belongsTo(Origem::class, 'origem_id');
    }

    /* ------------------- Lógica ------------------- */

    public function getAtributoValor(string $nomeAtributo): ?int
    {
        return $this->atributos[$nomeAtributo] ?? null;
    }
}
