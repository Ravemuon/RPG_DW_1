<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rolagem;
use Illuminate\Support\Facades\Auth;

class RolagemController extends Controller
{
    // Lista as rolagens de uma campanha ou personagem
    public function index(Request $request)
    {
        $campanhaId = $request->campanha_id;
        $personagemId = $request->personagem_id;

        $rolagens = Rolagem::where('campanha_id', $campanhaId)
            ->when($personagemId, fn($q) => $q->where('personagem_id', $personagemId))
            ->orderByDesc('created_at')
            ->get();

        return view('rolagens.index', compact('rolagens'));
    }

    // Cria uma nova rolagem
    public function store(Request $request)
    {
        $request->validate([
            'campanha_id' => 'required|exists:campanhas,id',
            'personagem_id' => 'nullable|exists:personagens,id',
            'tipo_dado' => 'required|string|regex:/^d\d+$/i',
            'quantidade' => 'nullable|integer|min:1',
            'modificador' => 'nullable|integer',
            'descricao' => 'nullable|string|max:255',
            'tipo_rolagem' => 'nullable|in:ataque,pericia,magia,resistencia,outro',
        ]);

        $quantidade = $request->quantidade ?? 1;
        $modificador = $request->modificador ?? 0;

        $resultado = Rolagem::rolar($request->tipo_dado, $quantidade, $modificador);

        $rolagem = Rolagem::create([
            'user_id' => Auth::id(),
            'campanha_id' => $request->campanha_id,
            'personagem_id' => $request->personagem_id,
            'tipo_dado' => $request->tipo_dado,
            'quantidade' => $quantidade,
            'modificador' => $modificador,
            'resultado' => $resultado,
            'descricao' => $request->descricao,
            'tipo_rolagem' => $request->tipo_rolagem ?? 'outro',
        ]);

        return redirect()->back()->with('success', "Rolagem realizada: {$rolagem->resultado}");
    }

    // Exibe os detalhes de uma rolagem
    public function show(Rolagem $rolagem)
    {
        return view('rolagens.show', compact('rolagem'));
    }

    // Deleta uma rolagem
    public function destroy(Rolagem $rolagem)
    {
        $rolagem->delete();
        return redirect()->back()->with('success', 'Rolagem deletada com sucesso.');
    }
}
