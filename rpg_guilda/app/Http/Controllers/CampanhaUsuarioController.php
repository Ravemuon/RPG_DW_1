<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campanha;
use App\Models\User;

class CampanhaUsuarioController extends Controller
{
    // Adicionar usuário a uma campanha
    public function adicionar(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'campanha_id' => 'required|exists:campanhas,id',
            'status' => 'nullable|string',
        ]);

        $campanha = Campanha::findOrFail($request->campanha_id);
        $campanha->jogadores()->attach($request->user_id, [
            'status' => $request->status ?? 'ativo',
        ]);

        return redirect()->back()->with('success', 'Usuário adicionado à campanha com sucesso.');
    }

    // Remover usuário da campanha
    public function remover(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'campanha_id' => 'required|exists:campanhas,id',
        ]);

        $campanha = Campanha::findOrFail($request->campanha_id);
        $campanha->jogadores()->detach($request->user_id);

        return redirect()->back()->with('success', 'Usuário removido da campanha com sucesso.');
    }

    // Listar jogadores de uma campanha
    public function listarJogadores($campanha_id)
    {
        $campanha = Campanha::with('jogadores')->findOrFail($campanha_id);
        return view('campanhas.jogadores', ['campanha' => $campanha]);
    }
}
