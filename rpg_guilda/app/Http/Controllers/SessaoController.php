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
    // ===================================================
    // 🔹 Lista sessões de uma campanha
    // ===================================================
    public function index(Campanha $campanha)
    {
        $this->authorize('view', $campanha);

        $sessoes = $campanha->sessoes()->with('personagens')->get();

        return view('sessoes.index', compact('campanha', 'sessoes'));
    }

    // ===================================================
    // 🔹 Formulário de criação de sessão
    // ===================================================
    public function create(Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        return view('sessoes.create', compact('campanha'));
    }

    // ===================================================
    // 🔹 Armazena nova sessão
    // ===================================================
    public function store(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'titulo' => 'required|string|max:150',
            'data_hora' => 'required|date',
            'resumo' => 'nullable|string'
        ]);

        $sessao = Sessao::create([
            'campanha_id' => $campanha->id,
            'criado_por' => Auth::id(),
            'titulo' => $request->titulo,
            'data_hora' => $request->data_hora,
            'resumo' => $request->resumo
        ]);

        return redirect()->route('sessoes.index', $campanha->id)
                         ->with('success', 'Sessão criada com sucesso!');
    }

    // ===================================================
    // 🔹 Exibe detalhes da sessão
    // ===================================================
    public function show(Sessao $sessao)
    {
        $this->authorize('view', $sessao->campanha);

        $sessao->load('personagens');

        return view('sessoes.show', compact('sessao'));
    }

    // ===================================================
    // 🔹 Formulário de edição da sessão
    // ===================================================
    public function edit(Sessao $sessao)
    {
        $this->authorize('update', $sessao->campanha);

        $sessao->load('personagens');

        return view('sessoes.edit', compact('sessao'));
    }

    // ===================================================
    // 🔹 Atualiza sessão
    // ===================================================
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

        // Se finalizar a sessão, gerar PDF automaticamente
        if ($request->status === 'concluida') {
            return $this->exportarPdf($sessao);
        }

        return redirect()->route('sessoes.show', $sessao->id)
                         ->with('success', 'Sessão atualizada com sucesso!');
    }

    // ===================================================
    // 🔹 Remove sessão
    // ===================================================
    public function destroy(Sessao $sessao)
    {
        $this->authorize('delete', $sessao->campanha);

        $campanhaId = $sessao->campanha_id;
        $sessao->delete();

        return redirect()->route('sessoes.index', $campanhaId)
                         ->with('success', 'Sessão deletada com sucesso!');
    }

    // ===================================================
    // 🔹 Vincula personagem à sessão
    // ===================================================
    public function adicionarPersonagem(Request $request, Sessao $sessao)
    {
        $this->authorize('update', $sessao->campanha);

        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'presente' => 'nullable|boolean'
        ]);

        $sessao->personagens()->syncWithoutDetaching([
            $request->personagem_id => ['presente' => $request->presente ?? false]
        ]);

        return redirect()->back()->with('success', 'Personagem adicionado à sessão!');
    }

    // ===================================================
    // 🔹 Atualiza presença ou resultado do personagem na sessão
    // ===================================================
    public function atualizarPersonagem(Request $request, Sessao $sessao, Personagem $personagem)
    {
        $this->authorize('update', $sessao->campanha);

        $request->validate([
            'presente' => 'nullable|boolean',
            'resultado' => 'nullable|array'
        ]);

        $sessao->personagens()->updateExistingPivot($personagem->id, [
            'presente' => $request->presente ?? false,
            'resultado' => $request->resultado
        ]);

        return redirect()->back()->with('success', 'Status do personagem atualizado!');
    }

    // ===================================================
    // 🔹 Exporta relatório da sessão em PDF
    // ===================================================
    public function exportarPdf(Sessao $sessao)
    {
        $this->authorize('view', $sessao->campanha);

        $sessao->load(['campanha', 'personagens']);

        $pdf = Pdf::loadView('sessoes.relatorio', [
            'sessao' => $sessao,
            'personagens' => $sessao->personagens
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = 'sessao_' . $sessao->id . '.pdf';

        return $pdf->download($nomeArquivo);
    }
}
