<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personagem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campanha_id',
        'nome',
        'classe',
        'sistemaRPG',
        'npc',
        'classe_npc',
        'atributos_extra',
        'forca','destreza','constituicao','inteligencia','sabedoria','carisma',
        'agilidade','intelecto','presenca','vigor','nex','sanidade',
        'forca_cth','destreza_cth','poder','constituição_cth','aparencia','educacao','tamanho','inteligencia_cth','sanidade_cth','pontos_vida',
        'aspects','stunts','fate_points',
        'atributos_custom','poderes','habilidades',
    ];

    protected $casts = [
        'habilidades' => 'array',
        'aspects' => 'array',
        'stunts' => 'array',
        'atributos_custom' => 'array',
        'poderes' => 'array',
        'atributos_extra' => 'array',
    ];

    public function jogador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campanha()
    {
        return $this->belongsTo(Campanha::class, 'campanha_id');
    }

    // Retorna atributos relevantes dependendo do sistema
    public function atributos()
    {
        $base = [];

        switch ($this->sistemaRPG) {
            case 'D&D':
                $base = [
                    'forca' => $this->forca,
                    'destreza' => $this->destreza,
                    'constituicao' => $this->constituicao,
                    'inteligencia' => $this->inteligencia,
                    'sabedoria' => $this->sabedoria,
                    'carisma' => $this->carisma,
                ];
                break;

            case 'Ordem Paranormal':
                $base = [
                    'agilidade' => $this->agilidade,
                    'forca' => $this->forca,
                    'intelecto' => $this->intelecto,
                    'presenca' => $this->presenca,
                    'vigor' => $this->vigor,
                    'nex' => $this->nex,
                    'sanidade' => $this->sanidade,
                ];
                break;

            case 'Call of Cthulhu':
                $base = [
                    'forca' => $this->forca_cth,
                    'destreza' => $this->destreza_cth,
                    'poder' => $this->poder,
                    'constituicao' => $this->constituição_cth,
                    'aparencia' => $this->aparencia,
                    'educacao' => $this->educacao,
                    'tamanho' => $this->tamanho,
                    'inteligencia' => $this->inteligencia_cth,
                    'sanidade' => $this->sanidade_cth,
                    'pontos_vida' => $this->pontos_vida,
                ];
                break;

            case 'Fate Core':
                $base = [
                    'aspects' => $this->aspects,
                    'stunts' => $this->stunts,
                    'fate_points' => $this->fate_points,
                ];
                break;

            case 'Cypher System':
            case 'Apocalypse World':
            case 'Cyberpunk 2093 - Arkana-RPG':
                $base = [
                    'atributos_custom' => $this->atributos_custom,
                    'poderes' => $this->poderes,
                ];
                break;
        }

        // Se for NPC, adiciona atributos extras
        if ($this->npc && $this->atributos_extra) {
            $base = array_merge($base, $this->atributos_extra);
        }

        return $base;
    }
        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function origens()
    {
        return $this->belongsToMany(Origem::class, 'personagem_origem');
    }

    public function pericias()
    {
        return $this->belongsToMany(Pericia::class, 'personagem_pericia')
                    ->withPivot('valor')
                    ->withTimestamps();
    }

    public function sessoes()
    {
        return $this->belongsToMany(Sessao::class, 'sessoes_personagens')
                    ->withPivot('presente', 'resultado')
                    ->withTimestamps();
    }

    // Calcula perícias automáticas
    public function calcularPericias()
    {
        $pericias = Pericia::where('sistemaRPG', $this->sistemaRPG)
                        ->where('automatica', true)
                        ->get();

        foreach($pericias as $pericia){
            $valor = 0;
            if($pericia->formula){
                foreach($pericia->formula as $atributo=>$peso){
                    $valor += ($this->$atributo ?? 0) * $peso;
                }
            }

            $this->pericias()->updateOrCreate(
                ['pericia_id'=>$pericia->id],
                ['valor'=>$valor,'definida'=>false]
            );
        }
    }

}
