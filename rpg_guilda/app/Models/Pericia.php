<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pericia extends Model
{
    use HasFactory;

    protected $table = 'pericias';

    protected $fillable = [
        'nome',
        'sistema_id',
        'atributo_relacionado',
        'atributo_nome',
        'descricao',
        'modificador',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /* ------------------- Relacionamentos ------------------- */

    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'classe_pericia');
    }

    /* ------------------------ Scopes ------------------------ */

    public function scopeDoSistema($query, int $sistemaId)
    {
        return $query->where('sistema_id', $sistemaId);
    }

    /* --------------------- Modificador D&D --------------------- */

    public static function calcularModificadorDND(int $valorAtributo): int
    {
        return (int) floor(($valorAtributo - 10) / 2);
    }

    public function modificadorFinal($personagem): int
    {
        $valorAtributo = $personagem->{$this->atributo_relacionado} ?? 10;
        return self::calcularModificadorDND($valorAtributo);
    }
}
