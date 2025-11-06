<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Origem;
use App\Models\Personagem;

class OrigemController extends Controller
{
    // Listar todas as origens ou por sistema
    public function index(Request $request)
    {
        $sistema = $request->query('sistema');

        if ($sistema) {
            $origens = Origem::where('sistemaRPG', $sistema)->get();
        } else {
            $origens = Origem::all();
        }

        return response()->json($origens);
    }

    // Criar nova origem
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'bônus' => 'nullable|array'
        ]);

        $origem = Origem::create($data);

        return response()->json($origem, 201);
    }

    // Mostrar detalhes de uma origem
    public function show($id)
    {
        $origem = Origem::findOrFail($id);
        return response()->json($origem);
    }

    // Atualizar origem
    public function update(Request $request, $id)
    {
        $origem = Origem::findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|string|max:100',
            'sistemaRPG' => 'sometimes|string|max:50',
            'descricao' => 'nullable|string',
            'bônus' => 'nullable|array'
        ]);

        $origem->update($data);

        return response()->json($origem);
    }

    // Deletar origem
    public function destroy($id)
    {
        $origem = Origem::findOrFail($id);
        $origem->delete();

        return response()->json(['message' => 'Origem deletada com sucesso']);
    }

    // Vincular origem a personagem e aplicar bônus
    public function vincularOrigemPersonagem(Request $request, $personagem_id)
    {
        $personagem = Personagem::findOrFail($personagem_id);

        $request->validate([
            'origem_id' => 'required|exists:origens,id'
        ]);

        $origem = Origem::findOrFail($request->origem_id);

        // Vincula a origem ao personagem
        $personagem->origens()->syncWithoutDetaching([$origem->id]);

        // Aplica os bônus da origem
        if ($origem->bônus) {
            $bônus = $origem->bônus;
            foreach ($bônus as $atributo => $valor) {
                if (isset($personagem->$atributo)) {
                    $personagem->$atributo += $valor;
                }
            }
            $personagem->save();
        }

        return response()->json([
            'personagem' => $personagem->load('origens'),
            'message' => 'Origem vinculada e bônus aplicados com sucesso'
        ]);
    }
}
