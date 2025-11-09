<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    // Definindo o nome da tabela, caso seja diferente do pluralizado
    protected $table = 'classes';

    // Atributos que podem ser atribuídos em massa
    protected $fillable = [
        'nome',                   // Nome da classe, ex: Guerreiro, Mago
        'sistema_id',             // ID do sistema que define a classe
        'descricao',              // Descrição da classe
        'forca',                  // Atributos iniciais
        'destreza',
        'constituicao',
        'inteligencia',
        'sabedoria',
        'carisma',
        'agilidade',
        'intelecto',
        'presenca',
        'vigor',
        'nex',
        'sanidade',
        'forca_cth',
        'destreza_cth',
        'poder',
        'constituicao_cth',
        'aparencia',
        'educacao',
        'tamanho',
        'inteligencia_cth',
        'sanidade_cth',
        'pontos_vida',
        'aspects',                // Aspectos e poderes especiais
        'stunts',                 // Habilidades especiais (stunts)
        'fate_points',            // Pontos de fate
        'atributos_custom',       // Atributos customizados para sistemas específicos
        'poderes'                 // Poderes especiais da classe
    ];

    /**
     * Define a relação de "Classe" com o "Sistema".
     * Uma classe pertence a um sistema.
     */
    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    /**
     * Recupera os atributos de uma classe, incluindo os atributos customizados.
     * Retorna um array com os atributos.
     */
    public function getAtributos()
    {
        // Atributos padrão da classe
        $atributos = [
            'forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma',
            'agilidade', 'intelecto', 'presenca', 'vigor', 'nex', 'sanidade',
            'forca_cth', 'destreza_cth', 'poder', 'constituicao_cth', 'aparencia',
            'educacao', 'tamanho', 'inteligencia_cth', 'sanidade_cth', 'pontos_vida'
        ];

        // Adiciona atributos customizados, se houver
        if ($this->atributos_custom) {
            // Decodifica os atributos customizados, caso haja JSON válido
            $custom = json_decode($this->atributos_custom, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $atributos = array_merge($atributos, $custom);
            }
        }

        return $atributos;
    }

    /**
     * Recupera os aspectos e poderes especiais da classe.
     * Retorna um array com as informações dos aspectos, stunts e poderes.
     */
    public function getAspectosEPoderes()
    {
        return [
            'aspects' => $this->aspects ? json_decode($this->aspects, true) : [], // Decodifica o campo 'aspects' (caso haja JSON)
            'stunts'  => $this->stunts ? json_decode($this->stunts, true) : [],   // Decodifica o campo 'stunts' (caso haja JSON)
            'poderes' => $this->poderes ? json_decode($this->poderes, true) : [], // Decodifica o campo 'poderes' (caso haja JSON)
        ];
    }
}
