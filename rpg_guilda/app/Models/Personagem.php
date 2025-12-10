<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personagem extends Model
{
    use HasFactory, SoftDeletes;

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
        'descricao',
        'historia',
        'personalidade',
        'inventario',
        'imagem',
        'ativo',
        'pagina'
    ];

    protected $casts = [
        'atributos' => 'array',
        'inventario' => 'array',
        'ativo' => 'boolean',
        'nivel' => 'integer',
        'xp' => 'integer',
        'bonus_proficiencia' => 'integer',
        'sanidade' => 'integer',
        'sorte' => 'integer'
    ];

    /**
     * Relacionamento com o usuário (jogador)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com a campanha
     */
    public function campanha(): BelongsTo
    {
        return $this->belongsTo(Campanha::class);
    }

    /**
     * Relacionamento com raça
     */
    public function raca(): BelongsTo
    {
        return $this->belongsTo(Raca::class);
    }

    /**
     * Relacionamento com classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relacionamento com origem
     */
    public function origem(): BelongsTo
    {
        return $this->belongsTo(Origem::class);
    }

    /**
     * Relacionamento com sistema
     */
    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class);
    }

    /**
     * Relacionamento com perícias do personagem
     */
    public function pericias(): HasMany
    {
        return $this->hasMany(PersonagemPericia::class);
    }

    /**
     * Calcula XP necessário para próximo nível
     */
    public function xpProximoNivel(): int
    {
        // Sistema padrão D&D 5e - pode ser customizado por sistema
        $xpPorNivel = [
            1 => 0,
            2 => 300,
            3 => 900,
            4 => 2700,
            5 => 6500,
            6 => 14000,
            7 => 23000,
            8 => 34000,
            9 => 48000,
            10 => 64000,
            11 => 85000,
            12 => 100000,
            13 => 120000,
            14 => 140000,
            15 => 165000,
            16 => 195000,
            17 => 225000,
            18 => 265000,
            19 => 305000,
            20 => 355000
        ];

        $nivelAtual = $this->nivel;
        return $xpPorNivel[min($nivelAtual + 1, 20)] ?? 355000;
    }

    /**
     * Calcula porcentagem de progresso para próximo nível
     */
    public function progressoNivel(): float
    {
        $xpAtual = $this->xp;
        $nivelAtual = $this->nivel;
        
        // Sistema padrão D&D 5e
        $xpPorNivel = [
            1 => 0,
            2 => 300,
            3 => 900,
            4 => 2700,
            5 => 6500,
            6 => 14000,
            7 => 23000,
            8 => 34000,
            9 => 48000,
            10 => 64000,
            11 => 85000,
            12 => 100000,
            13 => 120000,
            14 => 140000,
            15 => 165000,
            16 => 195000,
            17 => 225000,
            18 => 265000,
            19 => 305000,
            20 => 355000
        ];

        $xpNecessarioAtual = $xpPorNivel[$nivelAtual] ?? 0;
        $xpProximoNivel = $xpPorNivel[min($nivelAtual + 1, 20)] ?? 355000;
        
        if ($xpProximoNivel <= $xpNecessarioAtual) {
            return 100;
        }
        
        $rangeTotal = $xpProximoNivel - $xpNecessarioAtual;
        $progresso = $xpAtual - $xpNecessarioAtual;
        
        return ($progresso / $rangeTotal) * 100;
    }

    /**
     * Obtém atributos do personagem com cálculos aplicados
     */
    public function atributosCompletos(): array
    {
        $atributosBase = $this->atributos ?? [];
        
        // Se atributosBase for string JSON, converte para array
        if (is_string($atributosBase)) {
            $atributosBase = json_decode($atributosBase, true) ?? [];
        }
        
        $bonusRaca = $this->raca?->modificadores_atributos ?? [];
        
        // Se bonusRaca for string JSON, converte para array
        if (is_string($bonusRaca)) {
            $bonusRaca = json_decode($bonusRaca, true) ?? [];
        }
        
        // Aplicar bônus de raça
        foreach ($bonusRaca as $atributo => $valor) {
            if (isset($atributosBase[$atributo])) {
                $atributosBase[$atributo] += $valor;
            }
        }
        
        // Calcular modificador para cada atributo
        $atributosCompletos = [];
        foreach ($atributosBase as $nome => $valor) {
            $atributosCompletos[$nome] = [
                'valor' => $valor,
                'modificador' => floor(($valor - 10) / 2)
            ];
        }
        
        return $atributosCompletos;
    }

    /**
     * Calcula pontos de vida baseado no sistema
     */
    public function calcularPontosVida(): int
    {
        if (!$this->sistema || !$this->classe) {
            return 0;
        }
        
        $dadoVida = $this->classe->dado_vida;
        if (!$dadoVida) {
            return 0;
        }
        
        // Extrair número do dado (ex: "d8" -> 8)
        $valorDado = (int) str_replace('d', '', $dadoVida);
        
        // Primeiro nível: valor máximo do dado + modificador de constituição
        $pv = $valorDado;
        
        // Níveis subsequentes: média do dado (arredondado para cima) + modificador
        $atributos = $this->atributosCompletos();
        $modConstituicao = $atributos['constituicao']['modificador'] ?? 0;
        
        for ($i = 2; $i <= $this->nivel; $i++) {
            $pv += ceil($valorDado / 2) + $modConstituicao;
        }
        
        return $pv;
    }

    /**
     * Verifica se personagem pertence ao usuário atual
     */
    public function pertenceAoUsuario(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Scope para personagens ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para personagens de uma campanha específica
     */
    public function scopeDaCampanha($query, $campanhaId)
    {
        return $query->where('campanha_id', $campanhaId);
    }

    /**
     * Scope para personagens de um usuário específico
     */
    public function scopeDoUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}