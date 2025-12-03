<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classe extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
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

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usa_magia' => 'boolean',
        'pericias_iniciais' => 'array',
        'equipamento_inicial' => 'array',
        'atributos_bonus' => 'array',
        'poderes' => 'array',
    ];

    /**
     * Obtém o sistema ao qual a classe pertence.
     */
    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    public function pericias()
    {
        return $this->belongsToMany(Pericia::class, 'classe_pericia', 'classe_id', 'pericia_id');
    }

}
