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
    // Lista todas as sessões de uma campanha
    public function index(Campanha $campanha)
    {
        $this->authorize('view', $campanha);

        $sessoes = $campanha->sessoes()->with('personagens')->get();

        return view('sessoes.index', compact('campanha', 'sessoes'));
    }

    // Exibe o formulário para criar uma nova sessão
    public function create(Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        return view('sessoes.create', compact('campanha'));
    }

    // Armazena uma nova sessão
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

        return redirect()->route('sessoes.index', $campanha)
                         ->with('success', 'Sessão criada com sucesso!');
    }

    // Exibe os detalhes de uma sessão
    public function show(Sessao $sessao)
    {
        $this->authorize('view', $sessao->campanha);

        $sessao->load('personagens', 'campanha');

        return view('sessoes.show', compact('sessao'));
    }

    // Exibe o formulário de edição de uma sessão
    public function edit(Sessao $sessao)
    {
        $this->authorize('update', $sessao->campanha);

        $sessao->load('personagens', 'campanha');

        return view('sessoes.edit', compact('sessao'));
    }

    // Atualiza os dados de uma sessão
    public function update(Request $request, Sessao $sessao)
    {
        $this->authorize('update', $sessao->campanha);

        $request->validate([
            'titulo' => 'required|string|max:150',
            'data_hora' => 'required|date',
            'resumo' => 'nullable|string',
            'status' => 'required|in:agendada,em_andamento,concluida,cancelada'
        ]);

        $sessao->update($request->only('titulo', 'data_hora', 'resumo', 'status'));

        // Se a sessão for concluída, exporta o PDF
        if ($request->status === 'concluida') {
            return $this->exportarPdf($sessao);
        }

        return redirect()->route('sessoes.show', $sessao)
                         ->with('success', 'Sessão atualizada com sucesso!');
    }

    // Remove uma sessão
    public function destroy(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('delete', $campanha);

        $sessao->delete();

        return redirect()->route('sessoes.index', $campanha)
                         ->with('success', 'Sessão deletada com sucesso!');
    }

    // Adiciona um personagem à sessão
    public function adicionarPersonagem(Request $request, Sessao $sessao)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
        ]);

        $personagem = \App\Models\Personagem::findOrFail($request->personagem_id);

        // Verifica se o personagem pertence à campanha e ao jogador logado
        if ($personagem->usuario_id !== auth()->id() || $personagem->campanha_id !== $sessao->campanha_id) {
            abort(403, 'Você não pode adicionar este personagem a esta sessão.');
        }

        // Adiciona o personagem à sessão como presente
        $sessao->personagens()->syncWithoutDetaching([
            $personagem->id => ['presente' => true],
        ]);

        return redirect()->back()->with('success', 'Presença confirmada com sucesso!');
    }

    // Remove a presença de um personagem na sessão
    public function removerPersonagem(Sessao $sessao, Personagem $personagem)
    {
        // Verifica se o personagem pertence ao usuário logado
        if ($personagem->usuario_id !== auth()->id()) {
            abort(403, 'Você não pode cancelar a presença deste personagem.');
        }

        // Remove o personagem da sessão
        $sessao->personagens()->detach($personagem->id);

        return redirect()->back()->with('success', 'Sua presença foi cancelada com sucesso.');
    }

    // Permite ao mestre/admin alterar a presença de um personagem manualmente
    public function atualizarPresenca(Request $request, Sessao $sessao, Personagem $personagem)
    {
        $this->authorize('update', $sessao->campanha);

        $request->validate([
            'presente' => 'required|boolean',
        ]);

        $sessao->personagens()->updateExistingPivot($personagem->id, [
            'presente' => $request->boolean('presente'),
        ]);

        return redirect()->back()->with('success', 'Presença do personagem atualizada!');
    }

    // Atualiza a presença ou o resultado de um personagem na sessão
    public function atualizarPersonagem(Request $request, Sessao $sessao, Personagem $personagem)
    {
        $this->authorize('update', $sessao->campanha);

        $request->validate([
            'presente' => 'nullable|boolean',
            'resultado' => 'nullable|array'
        ]);

        $sessao->personagens()->updateExistingPivot($personagem->id, [
            'presente' => $request->boolean('presente'),
            'resultado' => $request->resultado
        ]);

        return redirect()->back()->with('success', 'Status do personagem atualizado!');
    }

    // Exporta os detalhes da sessão para um PDF
    public function exportarPdf(Sessao $sessao)
    {
        $this->authorize('view', $sessao->campanha);

        $sessao->load(['campanha', 'personagens']);

        $pdf = Pdf::loadView('sessoes.relatorio', [
            'sessao' => $sessao,
            'personagens' => $sessao->personagens
        ])->setPaper('a4', 'portrait');

        return $pdf->download('sessao_' . $sessao->id . '.pdf');
    }

    // Confirma a presença de um personagem na sessão
    public function confirmarPersonagem(Request $request, Sessao $sessao)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
        ]);

        $personagem = $request->user()->personagens()
            ->where('id', $request->personagem_id)
            ->where('campanha_id', $sessao->campanha_id)
            ->firstOrFail();

        // Adiciona o personagem à sessão (presente)
        $sessao->personagens()->syncWithoutDetaching([
            $personagem->id => ['presente' => true],
        ]);

        return back()->with('success', 'Presença confirmada para o personagem "' . $personagem->nome . '"!');
    }
}
