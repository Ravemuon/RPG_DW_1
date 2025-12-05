<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Campanha;
use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Charts\SessaoPresencasChart;

class SessaoController extends Controller
{
    // Lista sessões de uma campanha com filtros de busca, data e status
    public function index(Campanha $campanha, SessaoPresencasChart $chart, Request $request)
    {
        $this->authorize('view', $campanha);

        $search       = trim($request->get('search', ''));
        $dateSearch   = trim($request->get('date_search', ''));
        $statusFilter = $request->get('status', 'todas');

        $sessoesQuery = $campanha->sessoes()
            ->with(['personagens', 'presencas'])
            ->orderBy('data_hora', 'desc');

        if ($search !== '') {
            $sessoesQuery->where('titulo', 'LIKE', "%{$search}%");
        }

        if ($dateSearch !== '') {
            $sessoesQuery->where(function ($query) use ($dateSearch) {
                if (ctype_digit($dateSearch) && strlen($dateSearch) == 4) {
                    $query->orWhereYear('data_hora', $dateSearch);
                }
                try {
                    $formatada = date('Y-m-d', strtotime($dateSearch));
                    $query->orWhereDate('data_hora', $formatada);
                } catch (\Exception $e) {}
            });
        }

        $statuses = ['agendada', 'em_andamento', 'concluida', 'cancelada'];
        if ($statusFilter !== 'todas' && in_array($statusFilter, $statuses)) {
            $sessoesQuery->where('status', $statusFilter);
        }

        $sessoes = $sessoesQuery->paginate(10);

        $todas = $campanha->sessoes()->get();

        $dashboardData = [
            'total'      => $todas->count(),
            'concluidas' => $todas->where('status', 'concluida')->count(),
            'agendadas'  => $todas->where('status', 'agendada')->count(),
        ];

        $ordenadas = $todas->sortBy('data_hora');
        $presencasChart = $ordenadas->count()
            ? $chart->handler($campanha, $ordenadas)
            : null;

        return view('sessoes.index', compact(
            'campanha',
            'sessoes',
            'dashboardData',
            'presencasChart',
            'search',
            'dateSearch',
            'statusFilter'
        ));
    }

    // Exibe formulário para criar nova sessão
    public function create(Campanha $campanha)
    {
        $this->authorize('update', $campanha);
        return view('sessoes.create', compact('campanha'));
    }

    // Armazena nova sessão no banco
    public function store(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $data = $request->validate([
            'titulo'    => 'required|string|max:150',
            'data_hora' => 'required|date',
            'resumo'    => 'nullable|string'
        ]);

        $data['criado_por'] = Auth::id();

        $campanha->sessoes()->create($data);

        return redirect()
            ->route('sessoes.index', $campanha->id)
            ->with('success', 'Sessão criada com sucesso!');
    }

    // Mostra detalhes de uma sessão
    public function show(Campanha $campanha, Sessao $sessao, SessaoPresencasChart $chart)
    {
        $this->authorize('view', $campanha);

        if ($sessao->campanha_id !== $campanha->id) {
            abort(404, 'Sessão não encontrada nesta campanha.');
        }

        $user = Auth::user();
        $sessao->load(['personagens', 'campanha', 'presencas']);
        $presencaChart = $chart->handler($campanha, $sessao);

        $jaMarqueiPresenca = $user
            ? $sessao->presencas()->where('jogador_id', $user->id)->exists()
            : false;

        return view('sessoes.show', compact('campanha', 'sessao', 'jaMarqueiPresenca', 'presencaChart'));
    }

    // Exibe formulário para editar sessão
    public function edit(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('update', $campanha);

        if ($sessao->campanha_id !== $campanha->id) {
            abort(404, 'Sessão não encontrada nesta campanha.');
        }

        return view('sessoes.edit', compact('campanha', 'sessao'));
    }

    // Atualiza sessão existente e exporta PDF se concluída
    public function update(Request $request, Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('update', $campanha);

        if ($sessao->campanha_id !== $campanha->id) {
            abort(404, 'Sessão não encontrada nesta campanha.');
        }

        $data = $request->validate([
            'titulo'              => 'required|string|max:150',
            'data_hora'           => 'required|date',
            'resumo'              => 'nullable|string',
            'descricao_detalhada' => 'nullable|string',
            'status'              => 'required|in:agendada,em_andamento,concluida,cancelada'
        ]);

        $sessao->update($data);

        if ($request->status === 'concluida') {
            return $this->exportarPdf($campanha, $sessao);
        }

        return redirect()
            ->route('sessoes.show', [$campanha->id, $sessao->id])
            ->with('success', 'Sessão atualizada com sucesso!');
    }

    // Deleta uma sessão
    public function destroy(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('delete', $campanha);

        if ($sessao->campanha_id !== $campanha->id) {
            abort(404, 'Sessão não encontrada nesta campanha.');
        }

        $sessao->delete();

        return redirect()
            ->route('sessoes.index', $campanha->id)
            ->with('success', 'Sessão deletada com sucesso!');
    }

    // Marca presença do jogador em uma sessão
    public function marcarPresenca(Request $request, Campanha $campanha, Sessao $sessao)
    {
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'Você precisa estar logado para marcar presença.');
        }

        if ($user->id === $campanha->criador_id) {
            return back()->with('error', 'O Mestre não marca presença.');
        }

        if ($sessao->campanha_id !== $campanha->id) {
            return back()->with('error', 'Sessão inválida para esta campanha.');
        }

        try {
            $sessao->presencas()->attach($user->id, [
                'confirmou_presenca' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Presença marcada com sucesso!');
        } catch (QueryException $e) {
            Log::error("Erro ao marcar presença (user {$user->id}, sessão {$sessao->id}): ".$e->getMessage());

            if (str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return back()->with('error', 'Você já marcou presença nesta sessão.');
            }

            return back()->with('error', 'Erro ao registrar presença. Tente novamente.');
        }
    }

    // Funções ainda não implementadas
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

    // Exporta relatório da sessão em PDF
    public function exportarPdf(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('view', $campanha);

        if ($sessao->campanha_id !== $campanha->id) {
            abort(404, 'Sessão não encontrada nesta campanha.');
        }

        $sessao->load(['personagens', 'campanha', 'presencas']);

        $pdf = Pdf::loadView('sessoes.relatorio', compact('sessao'))
            ->setPaper('a4', 'portrait');

        $tituloLimpo = Str::slug($sessao->titulo ?? 'sessao');

        return $pdf->download("sessao_{$sessao->id}_{$tituloLimpo}.pdf");
    }
}
