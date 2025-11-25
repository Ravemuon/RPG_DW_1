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
        'pericias',
        'descricao',
        'historia',
        'personalidade',
        'inventario',
        'nivel',
        'experiencia',
        'vida_maxima',
        'vida_atual',
        'imagem',
        'ativo',
        'pagina'
    ];

    protected $casts = [
        'atributos' => 'array',
        'pericias' => 'array',
        'inventario' => 'array',
        'ativo' => 'boolean'
    ];

    protected $attributes = [
        'nivel' => 1,
        'experiencia' => 0,
        'ativo' => true
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function raca()
    {
        return $this->belongsTo(Raca::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function origem()
    {
        return $this->belongsTo(Origem::class);
    }

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    // Escopos
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDaCampanha($query, $campanhaId)
    {
        return $query->where('campanha_id', $campanhaId);
    }

    public function scopeDoUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Acessores
    public function getAtributoModificadorAttribute()
    {
        return function($atributo) {
            return $this->atributos[$atributo]['modificador'] ?? 0;
        };
    }

    public function getPericiaValorAttribute()
    {
        return function($pericia) {
            return $this->pericias[$pericia] ?? 0;
        };
    }

    public function getProficienciaAttribute()
    {
        // Cálculo de proficiência baseado no nível
        return 2 + floor(($this->nivel - 1) / 4);
    }

    public function getClasseArmaduraAttribute()
    {
        $base = 10;
        $modificadorDestreza = $this->atributos['destreza']['modificador'] ?? 0;

        return $base + $modificadorDestreza;
    }

    // Métodos de negócio
    public function adicionarExperiencia($quantidade)
    {
        $this->experiencia += $quantidade;
        $this->verificarSubidaNivel();
        $this->save();
    }

    private function verificarSubidaNivel()
    {
        $experienciaNecessaria = $this->calcularExperienciaProximoNivel();

        if ($this->experiencia >= $experienciaNecessaria) {
            $this->nivel++;
            $this->aumentarVidaMaxima();
            // Outras lógicas de subida de nível
        }
    }

    private function calcularExperienciaProximoNivel()
    {
        $tabelaExperiencia = [
            1 => 0, 2 => 300, 3 => 900, 4 => 2700, 5 => 6500,
            6 => 14000, 7 => 23000, 8 => 34000, 9 => 48000,
            10 => 64000, 11 => 85000, 12 => 100000, 13 => 120000,
            14 => 140000, 15 => 165000, 16 => 195000, 17 => 225000,
            18 => 265000, 19 => 305000, 20 => 355000
        ];

        return $tabelaExperiencia[$this->nivel + 1] ?? 0;
    }

    private function aumentarVidaMaxima()
    {
        $dadoVida = $this->classe->dado_vida;
        $valorDado = intval(substr($dadoVida, 1));
        $modificadorConstituicao = $this->atributos['constituicao']['modificador'] ?? 0;

        $vidaGanha = rand(1, $valorDado) + $modificadorConstituicao;
        $this->vida_maxima += max(1, $vidaGanha);
        $this->vida_atual += max(1, $vidaGanha);
    }

    public function sofrerDano($dano)
    {
        $this->vida_atual -= $dano;

        if ($this->vida_atual <= 0) {
            $this->vida_atual = 0;
            // Lógica de morte/inconsciência
        }

        $this->save();
    }

    public function curar($cura)
    {
        $this->vida_atual = min($this->vida_maxima, $this->vida_atual + $cura);
        $this->save();
    }

    public function adicionarItemInventario($item)
    {
        $inventario = $this->inventario ?? [];
        $inventario[] = $item;
        $this->inventario = array_unique($inventario);
        $this->save();
    }

    public function removerItemInventario($item)
    {
        $inventario = $this->inventario ?? [];
        $inventario = array_filter($inventario, function($i) use ($item) {
            return $i !== $item;
        });
        $this->inventario = array_values($inventario);
        $this->save();
    }
}
