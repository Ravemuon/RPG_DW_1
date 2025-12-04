<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Classe;
use App\Models\Sistema;

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

    /**
     * Relação com Sistema (N pericias → 1 sistema)
     */
    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

}
