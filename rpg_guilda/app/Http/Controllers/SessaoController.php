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

// IMPORTAÇÃO DA CLASSE DO CHART
use App\Charts\SessaoPresencasChart;

// IMPORTAÇÃO DO CHART
use App\Charts\SessaoPresencasChart;

class SessaoController extends Controller
{
<<<<<<< HEAD
    public function index(Campanha $campanha, SessaoPresencasChart $chart, Request $request)
=======
    /**
     * Lista todas as sessões de uma campanha.
     */
    public function index(Campanha $campanha, SessaoPresencasChart $chart)
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
    {
        $this->authorize('view', $campanha);

        // ----------------------------
        // PARÂMETROS DE BUSCA / FILTRO
        // ----------------------------
        $search       = trim($request->get('search', ''));
        $dateSearch   = trim($request->get('date_search', ''));
        $statusFilter = $request->get('status', 'todas');

        // ----------------------------
        // QUERY PRINCIPAL
        // ----------------------------
        $sessoesQuery = $campanha->sessoes()
            ->with(['personagens', 'presencas'])
<<<<<<< HEAD
            ->orderBy('data_hora', 'desc');

        // ----------------------------
        // FILTRO: TÍTULO
        // ----------------------------
        if ($search !== '') {
            $sessoesQuery->where('titulo', 'LIKE', "%{$search}%");
        }

        // ----------------------------
        // FILTRO: DATA OU ANO
        // ----------------------------
        if ($dateSearch !== '') {
            $sessoesQuery->where(function ($query) use ($dateSearch) {

                // Ano (exemplo: 2025)
                if (ctype_digit($dateSearch) && strlen($dateSearch) == 4) {
                    $query->orWhereYear('data_hora', $dateSearch);
                }

                // Data completa (ex: 2025-10-20 ou 20/10/2025)
                try {
                    $formatada = date('Y-m-d', strtotime($dateSearch));
                    $query->orWhereDate('data_hora', $formatada);
                } catch (\Exception $e) {
                    // ignora erros de data
                }

            });
        }

        // ----------------------------
        // FILTRO: STATUS
        // ----------------------------
        $statuses = ['agendada', 'em_andamento', 'concluida', 'cancelada'];

        if ($statusFilter !== 'todas' && in_array($statusFilter, $statuses)) {
            $sessoesQuery->where('status', $statusFilter);
        }

        // ----------------------------
        // EXECUTA QUERY COM PAGINAÇÃO
        // ----------------------------
        $sessoes = $sessoesQuery->paginate(10);

        // ----------------------------
        // DASHBOARD
        // ----------------------------
        $todas = $campanha->sessoes()->get();

        $dashboardData = [
            'total'      => $todas->count(),
            'concluidas' => $todas->where('status', 'concluida')->count(),
            'agendadas'  => $todas->where('status', 'agendada')->count(),
        ];

        // ----------------------------
        // GRÁFICO (somente se houver concluídas)
        // ----------------------------
        $ordenadas = $todas->sortBy('data_hora');

        $presencasChart = $ordenadas->count()
            ? $chart->handler($campanha, $ordenadas)
            : null;

        // ----------------------------
        // RETORNO
        // ----------------------------
        return view('sessoes.index', compact(
            'campanha',
            'sessoes',
            'dashboardData',
            'presencasChart',
            'search',
            'dateSearch',
            'statusFilter'
        ));
=======
            ->orderBy('data_hora')
            ->get();

        // GERA O CHART
        $presencasChart = $chart->handler();

        return view('sessoes.index', [
            'campanha' => $campanha,
            'sessoes' => $sessoes,
            'presencasChart' => $presencasChart
        ]);
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
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

<<<<<<< HEAD
        $data = $request->validate([
=======
        $request->validate([
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
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


    /**
<<<<<<< HEAD
     * Exibe detalhes da sessão E GERA O GRÁFICO (Gráfico de Donut).
=======
     * Exibe detalhes da sessão.
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
     */
    public function show(Campanha $campanha, Sessao $sessao, SessaoPresencasChart $chart)
    {
        $this->authorize('view', $campanha);
        if ($sessao->campanha_id !== $campanha->id) {
             abort(404, 'Sessão não encontrada nesta campanha.');
        }

        $user = Auth::user();

        $sessao->load(['personagens', 'campanha', 'presencas']);

<<<<<<< HEAD
        // GERA O GRÁFICO (Gráfico de Donut, chamando o handler com a Sessão única)
        $presencaChart = $chart->handler($campanha, $sessao);

=======
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
        $jaMarqueiPresenca = $user
            ? $sessao->presencas()->where('jogador_id', $user->id)->exists()
            : false;

        return view('sessoes.show', compact('campanha', 'sessao', 'jaMarqueiPresenca', 'presencaChart'));
    }


    /**
     * Formulário de edição.
     */
    public function edit(Campanha $campanha, Sessao $sessao)
    {
        $this->authorize('update', $campanha);
<<<<<<< HEAD
        if ($sessao->campanha_id !== $campanha->id) {
            abort(404, 'Sessão não encontrada nesta campanha.');
        }
=======
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
        return view('sessoes.edit', compact('campanha', 'sessao'));
    }


    /**
     * Atualiza sessão.
     */
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

<<<<<<< HEAD
        // Se o status for alterado para 'concluida', exporta o PDF
=======
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
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
        if ($sessao->campanha_id !== $campanha->id) {
            abort(404, 'Sessão não encontrada nesta campanha.');
        }

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
<<<<<<< HEAD
        if (!$user) { return back()->with('error', 'Você precisa estar logado para marcar presença.'); }
        if ($user->id === $campanha->criador_id) { return back()->with('error', 'O Mestre não marca presença.'); }
        if ($sessao->campanha_id !== $campanha->id) { return back()->with('error', 'Sessão inválida para esta campanha.'); }
=======

        if (!$user) {
            return back()->with('error', 'Você precisa estar logado para marcar presença.');
        }

        if ($user->id === $campanha->criador_id) {
            return back()->with('error', 'O Mestre não marca presença.');
        }
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0

        try {
            $sessao->presencas()->attach($user->id, [
                'confirmou_presenca' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Presença marcada com sucesso!');

<<<<<<< HEAD
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                 return back()->with('error', 'Você já marcou presença nesta sessão.');
=======
        } catch (\Illuminate\Database\QueryException $e) {

            Log::error("Erro ao marcar presença (user {$user->id}, sessão {$sessao->id}): ".$e->getMessage());

            if (str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'Integrity constraint violation')) {
                return back()->with('error', 'Você já marcou presença nesta sessão.');
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
            }
            Log::error("Erro ao marcar presença (user {$user->id}, sessão {$sessao->id}): ".$e->getMessage());
            return back()->with('error', 'Erro ao registrar presença. Tente novamente.');
        }
    }


<<<<<<< HEAD
    /**
     * Exporta relatório da sessão em PDF usando DomPDF.
=======
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
>>>>>>> 7d446f2343567dbc425c23c550ef5e589bd7d8f0
     */
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
