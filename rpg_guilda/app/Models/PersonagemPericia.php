<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonagemPericia extends Model
{
    use HasFactory;

    protected $table = 'personagem_pericias';

    protected $fillable = [
        'personagem_id',
        'pericia_id',
        'proficiente',
        'bonus_especial',
        'observacoes'
    ];

    protected $casts = [
        'proficiente' => 'boolean',
        'bonus_especial' => 'integer'
    ];

    public function personagem()
    {
        return $this->belongsTo(Personagem::class);
    }

    public function pericia()
    {
        return $this->belongsTo(Pericia::class);
    }

    /**
     * Calcula o bônus total da perícia
     */
    public function calcularBonus(): int
    {
        $bonus = 0;
        
        // Bônus da perícia base
        $bonus += $this->pericia->modificador ?? 0;
        
        // Bônus de proficiência
        if ($this->proficiente) {
            $bonus += $this->personagem->bonus_proficiencia ?? 2;
        }
        
        // Bônus especial adicional
        $bonus += $this->bonus_especial ?? 0;
        
        return $bonus;
    }
}