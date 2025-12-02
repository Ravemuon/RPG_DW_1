<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Campanha;
use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

// IMPORTAÇÃO DO CHART
use App\Charts\SessaoPresencasChart;

class SessaoController extends Controller
{
    /**
     * Lista todas as sessões de uma campanha.
     */
    public function index(Campanha $campanha, SessaoPresencasChart $chart)
    {
        $this->authorize('view', $campanha);

        $sessoes = $campanha->sessoes()
            ->with(['personagens', 'presencas'])
            ->orderBy('data_hora')
            ->get();

        // GERA O CHART
        $presencasChart = $chart->handler();

        return view('sessoes.index', [
            'campanha' => $campanha,
            'sessoes' => $sessoes,
            'presencasChart' => $presencasChart
        ]);
    }


    /**
     * Formulário de criação de sessão.
     */
    public function create(Campanha $campanha)
    {
        $this->authorize('update', $campanha);
        return view('sessoes.create', compact('campanha'));
    }


    /**
     * Armazena uma nova sessão.
     */
    public function store(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'titulo'    => 'required|string|max:150',
            'data_hora' => 'required|date',
            'resumo'    => 'nullable|string'
        ]);

        $campanha->sessoes()->create([
            'criado_por' => Auth::id(),
            'titulo'     => $request->titulo,
            'data_hora'  => $request->data_hora,
            'resumo'     => $request->resumo
        ]);

        return redirect()
            ->route('sessoes.index', $campanha->id)
            ->with('success', 'Sessão criada com sucesso!');
    }


    /**
     * Exibe detalhes da sessão.
     */
    public function show(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('view', $campanha);

        $user = Auth::user();

        $sessao->load(['personagens', 'campanha', 'presencas']);

        $jaMarqueiPresenca = $user
            ? $sessao->presencas()->where('jogador_id', $user->id)->exists()
            : false;

        return view('sessoes.show', compact('campanha', 'sessao', 'jaMarqueiPresenca'));
    }


    /**
     * Formulário de edição.
     */
    public function edit(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('update', $campanha);
        return view('sessoes.edit', compact('campanha', 'sessao'));
    }


    /**
     * Atualiza sessão.
     */
    public function update(Request $request, Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('update', $campanha);

        $request->validate([
            'titulo'              => 'required|string|max:150',
            'data_hora'           => 'required|date',
            'resumo'              => 'nullable|string',
            'descricao_detalhada' => 'nullable|string',
            'status'              => 'required|in:agendada,em_andamento,concluida,cancelada'
        ]);

        $sessao->update([
            'titulo'              => $request->titulo,
            'data_hora'           => $request->data_hora,
            'resumo'              => $request->resumo,
            'status'              => $request->status,
            'descricao_detalhada' => $request->descricao_detalhada
        ]);

        if ($request->status === 'concluida') {
            return $this->exportarPdf($campanha, $sessao);
        }

        return redirect()
            ->route('sessoes.show', [$campanha->id, $sessao->id])
            ->with('success', 'Sessão atualizada com sucesso!');
    }


    /**
     * Exclui sessão.
     */
    public function destroy(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('delete', $campanha);

        $sessao->delete();

        return redirect()
            ->route('sessoes.index', $campanha->id)
            ->with('success', 'Sessão deletada com sucesso!');
    }


    /**
     * Marca presença do jogador.
     */
    public function marcarPresenca(Request $request, Campanha $campanha, Sessao $sessao)
    {
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'Você precisa estar logado para marcar presença.');
        }

        if ($user->id === $campanha->criador_id) {
            return back()->with('error', 'O Mestre não marca presença.');
        }

        try {
            $sessao->presencas()->attach($user->id, [
                'confirmou_presenca' => true,
            ]);

            return back()->with('success', 'Presença marcada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {

            Log::error("Erro ao marcar presença (user {$user->id}, sessão {$sessao->id}): ".$e->getMessage());

            if (str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return back()->with('error', 'Você já marcou presença nesta sessão.');
            }

            return back()->with('error', 'Erro ao registrar presença. Tente novamente.');
        }
    }


    public function adicionarPersonagem(Request $request, Campanha $campanha, Sessao $sessao)
    {
        return back()->with('error', 'Função de adicionar personagem ainda não implementada.');
    }

    public function confirmarPersonagem(Request $request, Campanha $campanha, Sessao $sessao)
    {
        return back()->with('error', 'Função de confirmar personagem ainda não implementada.');
    }

    public function atualizarPersonagem(Request $request, Campanha $campanha, Sessao $sessao, Personagem $personagem)
    {
        return back()->with('error', 'Função de atualizar personagem ainda não implementada.');
    }


    /**
     * Exporta relatório da sessão em PDF.
     */
    public function exportarPdf(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('view', $campanha);

        $sessao->load(['personagens', 'campanha']);

        $pdf = Pdf::loadView('sessoes.relatorio', compact('sessao'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("sessao_{$sessao->id}.pdf");
    }
}
