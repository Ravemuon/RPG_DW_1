<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rolagem;
use Illuminate\Support\Facades\Auth;

class RolagemController extends Controller
{

    /**
     * Lista todas as rolagens.
     * Pode filtrar por campanha e personagem.
     */
    public function index(Request $request)
    {
        $campanhaId = $request->campanha_id;
        $personagemId = $request->personagem_id;

        // Consulta dinâmica
        $rolagens = Rolagem::query()
            ->when($campanhaId, fn($q) => $q->where('campanha_id', $campanhaId))
            ->when($personagemId, fn($q) => $q->where('personagem_id', $personagemId))
            ->orderByDesc('created_at')
            ->get();

        return view('rolagens.index', compact('rolagens'));
    }


    /**
     * Cria uma rolagem e salva no banco.
     */
    public function store(Request $request)
    {
        // Validação
        $request->validate([
            'campanha_id' => 'required|exists:campanhas,id',
            'personagem_id' => 'nullable|exists:personagens,id',
            'tipo_dado' => 'required|string|regex:/^d[0-9]+$/i',
            'quantidade' => 'nullable|integer|min:1',
            'modificador' => 'nullable|integer',
            'descricao' => 'nullable|string|max:255',
            'tipo_rolagem' => 'nullable|in:ataque,pericia,magia,resistencia,outro',
        ]);

        // Normaliza quantidade e modificador
        $quantidade = max(1, (int) ($request->quantidade ?? 1));
        $modificador = (int) ($request->modificador ?? 0);

        // Executa a rolagem usando método estático do Model
        $resultado = Rolagem::rolar(
            $request->tipo_dado,
            $quantidade,
            $modificador
        );

        // Salva no banco
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

        return redirect()->back()->with(
            'success',
            "Rolagem realizada: {$rolagem->resultado}"
        );
    }


    /**
     * Exibe detalhes de uma rolagem específica.
     */
    public function show(Rolagem $rolagem)
    {
        return view('rolagens.show', compact('rolagem'));
    }


    /**
     * Exclui uma rolagem do histórico.
     */
    public function destroy(Rolagem $rolagem)
    {
        $rolagem->delete();

        return redirect()->back()->with(
            'success',
            'Rolagem deletada com sucesso.'
        );
    }
}
