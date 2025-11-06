<?php

namespace App\Http\Controllers;

use App\Models\Missao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissaoController extends Controller
{
    // Lista todas as missões de uma campanha
    public function index($campanha_id)
    {
        $missoes = Missao::where('campanha_id', $campanha_id)
            ->with('mestre')
            ->get();

        return response()->json($missoes);
    }

    // Mostra uma missão específica
    public function show($id)
    {
        $missao = Missao::with('mestre')->findOrFail($id);
        return response()->json($missao);
    }

    // Cria uma nova missão
    public function store(Request $request)
    {
        $request->validate([
            'campanha_id' => 'required|exists:campanhas,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'recompensa' => 'nullable|string|max:255',
            'status' => 'nullable|in:pendente,em_andamento,concluida,cancelada',
        ]);

        $missao = Missao::create([
            'campanha_id' => $request->campanha_id,
            'user_id' => Auth::id(), // mestre logado
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'recompensa' => $request->recompensa,
            'status' => $request->status ?? 'pendente',
        ]);

        return response()->json([
            'message' => 'Missão criada com sucesso!',
            'missao' => $missao
        ]);
    }

    // Atualiza uma missão
    public function update(Request $request, $id)
    {
        $missao = Missao::findOrFail($id);

        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'recompensa' => 'nullable|string|max:255',
            'status' => 'nullable|in:pendente,em_andamento,concluida,cancelada',
        ]);

        $missao->update($request->only(['titulo', 'descricao', 'recompensa', 'status']));

        return response()->json([
            'message' => 'Missão atualizada com sucesso!',
            'missao' => $missao
        ]);
    }

    // Deleta uma missão
    public function destroy($id)
    {
        $missao = Missao::findOrFail($id);
        $missao->delete();

        return response()->json([
            'message' => 'Missão deletada com sucesso!'
        ]);
    }
}
