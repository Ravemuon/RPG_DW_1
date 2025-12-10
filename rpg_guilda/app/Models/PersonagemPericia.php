<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonagemPericia extends Model
{
    protected $table = 'personagem_pericia';
    
    protected $fillable = [
        'personagem_id',
        'pericia_id',
        'proficiente',
        'bonus_especial'
    ];
    
    protected $casts = [
        'proficiente' => 'boolean'
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
     * Calcular bônus completo considerando classe e origem
     */
    public function calcularBonusCompleto(Personagem $personagem): int
    {
        $modificadorAtributo = $this->calcularModificadorAtributo($personagem);
        $bonusProficiencia = $this->proficiente ? $personagem->bonus_proficiencia : 0;
        $bonusEspecial = $this->bonus_especial ?? 0;
        $bonusClasse = $this->calcularBonusClasse($personagem);
        $bonusOrigem = $this->calcularBonusOrigem($personagem);
        
        return $modificadorAtributo + $bonusProficiencia + $bonusEspecial + $bonusClasse + $bonusOrigem;
    }
    
    /**
     * Calcular modificador do atributo relacionado
     */
    public function calcularModificadorAtributo(Personagem $personagem): int
    {
        $atributosCompletos = $personagem->atributosCompletos();
        $atributoKey = $this->pericia->atributo_relacionado;
        
        return $atributosCompletos[$atributoKey]['modificador'] ?? 0;
    }
    
    /**
     * Calcular bônus da classe para esta perícia
     */
    public function calcularBonusClasse(Personagem $personagem): int
    {
        if (!$personagem->classe || !$personagem->classe->atributos_bonus) {
            return 0;
        }
        
        $bonusClasse = is_string($personagem->classe->atributos_bonus) 
            ? json_decode($personagem->classe->atributos_bonus, true)
            : $personagem->classe->atributos_bonus;
        
        // Verificar se há bônus para o atributo desta perícia
        $atributoKey = $this->pericia->atributo_relacionado;
        return $bonusClasse[$atributoKey] ?? 0;
    }
    
    /**
     * Calcular bônus da origem para esta perícia
     */
    public function calcularBonusOrigem(Personagem $personagem): int
    {
        if (!$personagem->origem || !$personagem->origem->pericias_iniciais) {
            return 0;
        }
        
        $bonusOrigem = is_string($personagem->origem->pericias_iniciais) 
            ? json_decode($personagem->origem->pericias_iniciais, true)
            : $personagem->origem->pericias_iniciais;
        
        // Verificar se há bônus específico para esta perícia
        $periciaNome = $this->pericia->nome;
        return $bonusOrigem[$periciaNome] ?? 0;
    }
    
    /**
     * Calcular bônus antigo (para compatibilidade)
     */
    public function calcularBonus(): int
    {
        return $this->calcularBonusCompleto($this->personagem);
    }
}