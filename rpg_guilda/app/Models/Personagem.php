<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Personagem extends Model
{
    protected $table = 'personagens';

    protected $fillable = [
        'nome',
        'classe',
        'sistema_rpg',
        'user_id',
        'campanha_id',
        'raca_id',          // ✅ Adicionado
        'atributos',
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'atributos' => 'array'
    ];

    // ===============================
    // 🔹 Relações
    // ===============================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class);
    }

    public function classeObj()
    {
        return $this->belongsTo(Classe::class, 'classe', 'nome');
    }

    public function raca() // ✅ Nova relação
    {
        return $this->belongsTo(Raca::class);
    }

    public function origens()
    {
        return $this->belongsToMany(Origem::class, 'personagem_origem')
                    ->withTimestamps();
    }

    public function pericias()
    {
        return $this->belongsToMany(Pericia::class, 'personagem_pericias')
                    ->withPivot('valor', 'definida')
                    ->withTimestamps();
    }

    // ===============================
    // 🔹 Inicializa atributos do personagem
    // ===============================
    public function inicializarAtributos()
    {
        $atributos = [];

        // 1️⃣ Carrega atributos da classe
        if ($this->classeObj) {
            $classeAttrs = Arr::only($this->classeObj->toArray(), [
                'forca', 'destreza', 'constituicao', 'inteligencia',
                'sabedoria', 'carisma', 'agilidade', 'intelecto',
                'presenca', 'vigor', 'nex', 'sanidade',
                'forca_cth', 'destreza_cth', 'constituição_cth', 'inteligencia_cth',
                'poder', 'sanidade_cth', 'aparencia', 'educacao', 'tamanho', 'pontos_vida'
            ]);

            foreach ($classeAttrs as $key => $value) {
                if (!is_null($value)) {
                    $atributos[$key] = $value;
                }
            }
        }

        // 2️⃣ Aplica bônus das origens
        foreach ($this->origens as $origem) {
            $bonus = $origem->bônus ?? [];
            foreach ($bonus as $key => $value) {
                if (isset($atributos[$key])) {
                    $atributos[$key] += $value;
                } else {
                    $atributos[$key] = $value;
                }
            }
        }

        // 3️⃣ Aplica bônus da raça, se houver
        if ($this->raca) {
            $bonusRaca = $this->raca->bonus ?? [];
            foreach ($bonusRaca as $key => $value) {
                if (isset($atributos[$key])) {
                    $atributos[$key] += $value;
                } else {
                    $atributos[$key] = $value;
                }
            }
        }

        $this->atributos = $atributos;
        $this->save();
    }

    public function racas()
    {
        return $this->belongsToMany(Raca::class, 'personagem_raca')
                    ->using(PersonagemRaca::class) // usa o pivot customizado
                    ->withPivot('nivel', 'descricao_personalizada')
                    ->withTimestamps();
    }

}
