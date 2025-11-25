<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Campanha;
use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SessaoController extends Controller
{
    public function index(Campanha $campanha)
    {
        $this->authorize('view', $campanha);
        // Carrega as presenças para uso futuro ou exibição opcional no índice
        $sessoes = $campanha->sessoes()->with(['personagens', 'presencas'])->get();

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

    /**
     * Exibe os detalhes da sessão, verificando a presença do usuário logado.
     */
    public function show(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('view', $campanha);
        $user = Auth::user();

        // Carrega o relacionamento 'presencas' para verificar se o usuário já marcou.
        $sessao->load(['personagens', 'campanha', 'presencas']);

        $jaMarqueiPresenca = false;

        if ($user) {
            // Verifica se o usuário logado existe no relacionamento 'presencas'
            // Mantendo 'jogador_id' como você definiu, assumindo que esta é a coluna na tabela pivot.
            $jaMarqueiPresenca = $sessao->presencas()->where('jogador_id', $user->id)->exists();
        }

        return view('sessoes.show', compact('campanha', 'sessao', 'jaMarqueiPresenca'));
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
            'descricao_detalhada' => 'nullable|string', // Adicionado para permitir atualização no formulário
            'status' => 'required|in:agendada,em_andamento,concluida,cancelada'
        ]);

        // Incluindo 'descricao_detalhada' na atualização
        $sessao->update($request->only('titulo', 'data_hora', 'resumo', 'status', 'descricao_detalhada'));

        // Se o status for concluída, exporta o PDF
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

    /**
     * Marca a presença de um jogador em uma sessão.
     * Corrigido para injetar Request.
     */
    public function marcarPresenca(Request $request, Campanha $campanha, Sessao $sessao)
    {
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'Você precisa estar logado para marcar presença.');
        }

        // 1. Verificar se o usuário é o Mestre (Criador da Campanha)
        if ($user->id === $campanha->criador_id) {
            return back()->with('error', 'O Mestre não marca presença, ele gerencia.');
        }

        // 2. Tentar registrar a Presença
        try {

            $sessao->presencas()->attach($user->id, [
                'confirmou_presenca' => true,
            ]);

            return back()->with('success', '✅ Presença marcada com sucesso! Nos vemos na sessão.');

        } catch (\Illuminate\Database\QueryException $e) {

            // Loga o erro completo para debug, mas mostra uma mensagem amigável ao usuário
            Log::error("Erro ao marcar presença para user {$user->id} na sessão {$sessao->id}: " . $e->getMessage());

            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'Integrity constraint violation')) {
                 return back()->with('error', '⚠️ Sua presença já está confirmada nesta sessão.');
            }
            return back()->with('error', 'Erro ao registrar presença. Tente novamente.');
        }
    }

    // Métodos stubs (corpos vazios) para rotas que não foram fornecidas no Controller
    public function adicionarPersonagem(Request $request, Campanha $campanha, Sessao $sessao) {
        return back()->with('error', 'Função de adicionar personagem ainda não implementada.');
    }

    public function confirmarPersonagem(Request $request, Campanha $campanha, Sessao $sessao) {
        return back()->with('error', 'Função de confirmar personagem ainda não implementada.');
    }

    public function atualizarPersonagem(Request $request, Campanha $campanha, Sessao $sessao, Personagem $personagem) {
        return back()->with('error', 'Função de atualizar personagem ainda não implementada.');
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
