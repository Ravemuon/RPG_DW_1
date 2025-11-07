<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Missao;
use App\Models\Campanha;
use Illuminate\Support\Facades\Auth;

class MissaoController extends Controller
{
    // ===================================================
    // 🔹 Lista missões de uma campanha
    // ===================================================
    public function index(Campanha $campanha)
    {
        $this->authorize('view', $campanha);

        $missoes = $campanha->missoes()->get();

        return view('missoes.index', compact('campanha', 'missoes'));
    }

    // ===================================================
    // 🔹 Formulário para criar missão
    // ===================================================
    public function create(Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        return view('missoes.create', compact('campanha'));
    }

    // ===================================================
    // 🔹 Armazena nova missão
    // ===================================================
    public function store(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'recompensa' => 'nullable|string',
            'status' => 'nullable|in:pendente,em_andamento,concluida,cancelada'
        ]);

        $missao = Missao::create([
            'campanha_id' => $campanha->id,
            'user_id' => Auth::id(),
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'recompensa' => $request->recompensa,
            'status' => $request->status ?? 'pendente',
        ]);

        return redirect()->route('missoes.index', $campanha->id)
                         ->with('success', 'Missão criada com sucesso!');
    }

    // ===================================================
    // 🔹 Exibe detalhes de uma missão
    // ===================================================
    public function show(Campanha $campanha, Missao $missao)
    {
        $this->authorize('view', $campanha);

        return view('missoes.show', compact('campanha', 'missao'));
    }

    // ===================================================
    // 🔹 Formulário de edição de missão
    // ===================================================
    public function edit(Campanha $campanha, Missao $missao)
    {
        $this->authorize('update', $campanha);

        return view('missoes.edit', compact('campanha', 'missao'));
    }

    // ===================================================
    // 🔹 Atualiza missão
    // ===================================================
    public function update(Request $request, Campanha $campanha, Missao $missao)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'recompensa' => 'nullable|string',
            'status' => 'nullable|in:pendente,em_andamento,concluida,cancelada'
        ]);

        $missao->update($request->all());

        return redirect()->route('missoes.index', $campanha->id)
                         ->with('success', 'Missão atualizada com sucesso!');
    }

    // ===================================================
    // 🔹 Deleta missão
    // ===================================================
    public function destroy(Campanha $campanha, Missao $missao)
    {
        $this->authorize('update', $campanha);

        $missao->delete();

        return redirect()->route('missoes.index', $campanha->id)
                         ->with('success', 'Missão deletada com sucesso!');
    }
}
