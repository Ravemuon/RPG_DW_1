<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Campanha;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Pericia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonagemController extends Controller
{
    // Lista todos os personagens de uma campanha
    public function index($campanha_id)
    {
        $personagens = Personagem::where('campanha_id', $campanha_id)
            ->with(['classe', 'origens', 'pericias'])
            ->get();

        return response()->json($personagens);
    }

    // Mostra um personagem específico
    public function show($id)
    {
        $personagem = Personagem::with(['classe', 'origens', 'pericias'])->findOrFail($id);
        return response()->json($personagem);
    }

    // Cria um personagem (jogador ou NPC)
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|exists:campanhas,id',
            'sistemaRPG' => 'required|string',
            'npc' => 'boolean',
            'classe_id' => 'nullable|exists:classes,id',
            'origem_ids' => 'nullable|array',
            'atributos' => 'nullable|array',
        ]);

        $user_id = $request->npc ? $request->user_id ?? null : Auth::id();

        $this->validaAtributosPorSistema($request);

        $personagem = new Personagem();
        $personagem->nome = $request->nome;
        $personagem->campanha_id = $request->campanha_id;
        $personagem->sistemaRPG = $request->sistemaRPG;
        $personagem->npc = $request->npc ?? false;
        $personagem->user_id = $user_id;
        $personagem->classe_id = $request->classe_id ?? null;

        // Aplica atributos enviados
        if ($request->atributos) {
            foreach ($request->atributos as $atributo => $valor) {
                if (isset($personagem->$atributo)) {
                    $personagem->$atributo = $valor;
                }
            }
        }

        $personagem->save(); // salva primeiro para ter ID

        // Vincula origens e aplica bônus
        if ($request->origem_ids) {
            foreach ($request->origem_ids as $origem_id) {
                $origem = Origem::find($origem_id);
                if ($origem) {
                    $personagem->origens()->attach($origem->id);
                    $this->aplicaBonus($personagem, $origem->bônus);
                }
            }
        }

        // Aplica bônus da classe
        if ($personagem->classe) {
            $this->aplicaBonus($personagem, $personagem->classe->bônus ?? []);
        }

        // Gera perícias automáticas
        $this->gerarPericias($personagem);

        $personagem->save();

        return response()->json([
            'message' => 'Personagem criado com sucesso!',
            'personagem' => $personagem->load(['classe', 'origens', 'pericias'])
        ]);
    }

    // Atualiza um personagem
    public function update(Request $request, $id)
    {
        $personagem = Personagem::findOrFail($id);

        $this->validaAtributosPorSistema($request, $personagem->sistemaRPG);

        $personagem->update($request->only([
            'nome', 'classe_id', 'npc', 'sistemaRPG', 'campanha_id'
        ]));

        // Atualiza atributos
        if ($request->atributos) {
            foreach ($request->atributos as $atributo => $valor) {
                if (isset($personagem->$atributo)) {
                    $personagem->$atributo = $valor;
                }
            }
        }

        // Atualiza origens
        if ($request->origem_ids) {
            $personagem->origens()->sync($request->origem_ids);

            foreach ($personagem->origens as $origem) {
                $this->aplicaBonus($personagem, $origem->bônus);
            }
        }

        // Aplica bônus da classe
        if ($personagem->classe) {
            $this->aplicaBonus($personagem, $personagem->classe->bônus ?? []);
        }

        // Atualiza perícias de acordo com atributos novos
        $personagem->pericias()->detach(); // remove perícias antigas
        $this->gerarPericias($personagem);

        $personagem->save();

        return response()->json([
            'message' => 'Personagem atualizado com sucesso!',
            'personagem' => $personagem->load(['classe', 'origens', 'pericias'])
        ]);
    }

    // Deleta um personagem
    public function destroy($id)
    {
        $personagem = Personagem::findOrFail($id);
        $personagem->delete();

        return response()->json([
            'message' => 'Personagem deletado com sucesso!'
        ]);
    }

    // Cria NPC (somente mestre)
    public function storeNPC(Request $request)
    {
        $request->merge(['npc' => true]);
        return $this->store($request);
    }

    // Valida os atributos de acordo com o sistema
    private function validaAtributosPorSistema(Request $request, $sistemaRPG = null)
    {
        $sistema = $sistemaRPG ?? $request->sistemaRPG;

        switch ($sistema) {
            case 'D&D':
                $request->validate([
                    'forca' => 'required|integer|min:1',
                    'destreza' => 'required|integer|min:1',
                    'constituicao' => 'required|integer|min:1',
                    'inteligencia' => 'required|integer|min:1',
                    'sabedoria' => 'required|integer|min:1',
                    'carisma' => 'required|integer|min:1',
                ]);
                break;

            case 'Ordem Paranormal':
                $request->validate([
                    'agilidade' => 'required|integer|min:1',
                    'intelecto' => 'required|integer|min:1',
                    'presenca' => 'required|integer|min:1',
                    'vigor' => 'required|integer|min:1',
                    'nex' => 'required|integer|min:0',
                    'sanidade' => 'required|integer|min:0',
                ]);
                break;

            case 'Call of Cthulhu':
                $request->validate([
                    'forca_cth' => 'required|integer|min:1',
                    'destreza_cth' => 'required|integer|min:1',
                    'poder' => 'required|integer|min:1',
                    'constituição_cth' => 'required|integer|min:1',
                    'aparencia' => 'required|integer|min:1',
                    'educacao' => 'required|integer|min:1',
                    'tamanho' => 'required|integer|min:1',
                    'inteligencia_cth' => 'required|integer|min:1',
                    'sanidade_cth' => 'required|integer|min:0',
                    'pontos_vida' => 'required|integer|min:1',
                ]);
                break;

            case 'Fate Core':
                $request->validate([
                    'aspects' => 'required|array|min:1',
                    'stunts' => 'nullable|array',
                    'fate_points' => 'required|integer|min:0',
                ]);
                break;

            case 'Cypher System':
            case 'Apocalypse World':
            case 'Cyberpunk 2093 - Arkana-RPG':
                $request->validate([
                    'atributos_custom' => 'required|array|min:1',
                    'poderes' => 'nullable|array',
                ]);
                break;

            default:
                throw new \Exception("Sistema RPG desconhecido: $sistema");
        }
    }

    // Aplica bônus de origem ou classe
    private function aplicaBonus(Personagem $personagem, $bonus)
    {
        if (!$bonus) return;

        foreach ($bonus as $atributo => $valor) {
            if (isset($personagem->$atributo)) {
                $personagem->$atributo += $valor;
            } elseif (is_array($personagem->atributos_custom) && isset($personagem->atributos_custom[$atributo])) {
                $personagem->atributos_custom[$atributo] += $valor;
            }
        }
    }

    // Gera perícias automáticas
    private function gerarPericias(Personagem $personagem)
    {
        $pericias = Pericia::where('sistemaRPG', $personagem->sistemaRPG)->get();

        foreach ($pericias as $pericia) {
            $valor = 0;

            if ($pericia->automatica && $pericia->formula) {
                $formula = json_decode($pericia->formula, true);

                foreach ($formula as $atributo => $multiplicador) {
                    if (isset($personagem->$atributo)) {
                        $valor += $personagem->$atributo * $multiplicador;
                    } elseif (is_array($personagem->atributos_custom) && isset($personagem->atributos_custom[$atributo])) {
                        $valor += $personagem->atributos_custom[$atributo] * $multiplicador;
                    }
                }
            }

            $personagem->pericias()->attach($pericia->id, ['valor' => $valor]);
        }
    }
}
