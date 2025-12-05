<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'nivel',
        'xp',
        'bonus_proficiencia',
        'sanidade',
        'sorte',
        'atributos',
        'pericias',
        'equipamento',
        'inventario',
        'descricao',
        'historia',
        'personalidade',
        'imagem',
        'ativo',
        'pagina',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function getAtributosAttribute($value): array
    {
        $array = json_decode($value, true);
        return is_array($array) ? $array : [];
    }

    public function getPericiasAttribute($value): array
    {
        $array = json_decode($value, true);
        return is_array($array) ? $array : [];
    }

    public function getEquipamentoAttribute($value): array
    {
        $array = json_decode($value, true);
        return is_array($array) ? $array : [];
    }

    public function getInventarioAttribute($value): array
    {
        $array = json_decode($value, true);
        return is_array($array) ? $array : [];
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->imagem ? Storage::url($this->imagem) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campanha(): BelongsTo
    {
        return $this->belongsTo(Campanha::class);
    }

    public function raca(): BelongsTo
    {
        return $this->belongsTo(Raca::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function origem(): BelongsTo
    {
        return $this->belongsTo(Origem::class);
    }

    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class);
    }
}
