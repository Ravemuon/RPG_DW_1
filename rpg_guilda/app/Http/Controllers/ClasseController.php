<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Personagem;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    // Lista todas as classes
    public function index()
    {
        $classes = Classe::all();
        return response()->json($classes);
    }

    // Mostra uma classe específica
    public function show($id)
    {
        $classe = Classe::findOrFail($id);
        return response()->json($classe);
    }

    // Cria uma nova classe
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string',
            'descricao' => 'nullable|string',
        ]);

        $classe = Classe::create($request->all());

        return response()->json([
            'message' => 'Classe criada com sucesso!',
            'classe' => $classe
        ]);
    }

    // Atualiza uma classe existente
    public function update(Request $request, $id)
    {
        $classe = Classe::findOrFail($id);

        $classe->update($request->all());

        return response()->json([
            'message' => 'Classe atualizada com sucesso!',
            'classe' => $classe
        ]);
    }

    // Deleta uma classe
    public function destroy($id)
    {
        $classe = Classe::findOrFail($id);
        $classe->delete();

        return response()->json([
            'message' => 'Classe deletada com sucesso!'
        ]);
    }

    // Cria um personagem a partir de uma classe
    public function criarPersonagemPorClasse(Request $request, $classe_id)
    {
        $classe = Classe::findOrFail($classe_id);

        $request->validate([
            'nome' => 'required|string|max:100',
            'user_id' => 'required|exists:users,id',
            'campanha_id' => 'required|exists:campanhas,id',
            'npc' => 'boolean',
        ]);

        // Preenche atributos iniciais de acordo com o sistema da classe
        $atributos_iniciais = [];
        switch ($classe->sistemaRPG) {
            case 'D&D':
                $atributos_iniciais = [
                    'forca' => $classe->forca,
                    'destreza' => $classe->destreza,
                    'constituicao' => $classe->constituicao,
                    'inteligencia' => $classe->inteligencia,
                    'sabedoria' => $classe->sabedoria,
                    'carisma' => $classe->carisma,
                ];
                break;

            case 'Ordem Paranormal':
                $atributos_iniciais = [
                    'agilidade' => $classe->agilidade,
                    'intelecto' => $classe->intelecto,
                    'presenca' => $classe->presenca,
                    'vigor' => $classe->vigor,
                    'nex' => $classe->nex,
                    'sanidade' => $classe->sanidade,
                ];
                break;

            case 'Call of Cthulhu':
                $atributos_iniciais = [
                    'forca_cth' => $classe->forca_cth,
                    'destreza_cth' => $classe->destreza_cth,
                    'poder' => $classe->poder,
                    'constituição_cth' => $classe->constituição_cth,
                    'aparencia' => $classe->aparencia,
                    'educacao' => $classe->educacao,
                    'tamanho' => $classe->tamanho,
                    'inteligencia_cth' => $classe->inteligencia_cth,
                    'sanidade_cth' => $classe->sanidade_cth,
                    'pontos_vida' => $classe->pontos_vida,
                ];
                break;

            case 'Fate Core':
                $atributos_iniciais = [
                    'aspects' => $classe->aspects ?? [],
                    'stunts' => $classe->stunts ?? [],
                    'fate_points' => $classe->fate_points,
                ];
                break;

            case 'Cypher System':
            case 'Apocalypse World':
            case 'Cyberpunk 2093 - Arkana-RPG':
                $atributos_iniciais = [
                    'atributos_custom' => $classe->atributos_custom ?? [],
                    'poderes' => $classe->poderes ?? [],
                ];
                break;
        }

        $personagemData = array_merge(
            $request->only(['nome', 'user_id', 'campanha_id', 'npc']),
            [
                'sistemaRPG' => $classe->sistemaRPG,
                'classe' => $classe->nome,
            ],
            $atributos_iniciais
        );

        $personagem = Personagem::create($personagemData);

        return response()->json([
            'message' => 'Personagem criado a partir da classe com sucesso!',
            'personagem' => $personagem
        ]);
    }
}
