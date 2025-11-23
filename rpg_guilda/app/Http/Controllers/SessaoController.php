<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Campanha;
use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SessaoController extends Controller
{
    public function index(Campanha $campanha)
    {
        $this->authorize('view', $campanha);
        $sessoes = $campanha->sessoes()->with('personagens')->get();

        return view('sessoes.index', compact('campanha', 'sessoes'));
    }

    public function create(Campanha $campanha)
    {
        $this->authorize('update', $campanha);
        return view('sessoes.create', compact('campanha'));
    }

    public function store(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'titulo' => 'required|string|max:150',
            'data_hora' => 'required|date',
            'resumo' => 'nullable|string'
        ]);

        $sessao = $campanha->sessoes()->create([
            'criado_por' => Auth::id(),
            'titulo' => $request->titulo,
            'data_hora' => $request->data_hora,
            'resumo' => $request->resumo
        ]);

        return redirect()->route('sessoes.index', $campanha->id)
                         ->with('success', 'Sessão criada com sucesso!');
    }

    public function show(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('view', $campanha);
        $sessao->load('personagens', 'campanha');

        return view('sessoes.show', compact('campanha', 'sessao'));
    }

    public function edit(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('update', $campanha);
        return view('sessoes.edit', compact('campanha', 'sessao'));
    }

    public function update(Request $request, Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'titulo' => 'required|string|max:150',
            'data_hora' => 'required|date',
            'resumo' => 'nullable|string',
            'status' => 'required|in:agendada,em_andamento,concluida,cancelada'
        ]);

        $sessao->update($request->only('titulo', 'data_hora', 'resumo', 'status'));

        if ($request->status === 'concluida') {
            return $this->exportarPdf($campanha, $sessao);
        }

        return redirect()->route('sessoes.show', [$campanha->id, $sessao->id])
                         ->with('success', 'Sessão atualizada com sucesso!');
    }

    public function destroy(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('delete', $campanha);
        $sessao->delete();

        return redirect()->route('sessoes.index', $campanha->id)
                         ->with('success', 'Sessão deletada com sucesso!');
    }

    public function confirmarPersonagem(Request $request, Campanha $campanha, Sessao $sessao)
    {
        $request->validate(['personagem_id' => 'required|exists:personagens,id']);
        $personagem = $request->user()->personagens()
            ->where('id', $request->personagem_id)
            ->where('campanha_id', $campanha->id)
            ->firstOrFail();

        $sessao->personagens()->syncWithoutDetaching([$personagem->id => ['presente' => true]]);
        return back()->with('success', "Presença confirmada para '{$personagem->nome}'!");
    }

    public function atualizarPersonagem(Request $request, Campanha $campanha, Sessao $sessao, Personagem $personagem)
    {
        $this->authorize('update', $campanha);

        $request->validate(['presente' => 'nullable|boolean', 'resultado' => 'nullable|array']);

        $sessao->personagens()->updateExistingPivot($personagem->id, [
            'presente' => $request->boolean('presente'),
            'resultado' => $request->resultado
        ]);

        return back()->with('success', 'Status do personagem atualizado!');
    }

    public function exportarPdf(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('view', $campanha);

        $sessao->load(['personagens', 'campanha']);

        $pdf = Pdf::loadView('sessoes.relatorio', compact('sessao'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("sessao_{$sessao->id}.pdf");
    }
}
