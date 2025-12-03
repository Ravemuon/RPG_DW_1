<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Missao;
use App\Models\Campanha;
use Barryvdh\DomPDF\Facade\Pdf;
// Importação das classes dos gráficos
use App\Charts\MissoesPrioridadeChart;
use App\Charts\MissoesStatusChart;

class MissaoController extends Controller
{
    public function index(Request $request, Campanha $campanha)
    {
        $this->authorize('view', $campanha);

        $search = $request->search ?? null;
        $prioridade = $request->prioridade ?? null;

        $missoes = $campanha->missoes()
            ->when($search, fn($q) =>
                $q->where('titulo', 'LIKE', "%$search%")
                    ->orWhere('descricao', 'LIKE', "%$search%"))
            ->when($prioridade, fn($q) =>
                $q->where('prioridade', $prioridade))
            ->orderByDesc('id')
            ->get();

        // Dados para o Dashboard (Status e Prioridade)
        $dashboard = [
            // Status
            'pendentes'  => $missoes->where('status', 'pendente')->count(),
            'andamento'  => $missoes->where('status', 'em_andamento')->count(),
            'concluidas' => $missoes->where('status', 'concluida')->count(),
            'canceladas' => $missoes->where('status', 'cancelada')->count(),
            // Prioridade
            'baixa'      => $missoes->where('prioridade', 'baixa')->count(),
            'media'      => $missoes->where('prioridade', 'media')->count(),
            'alta'       => $missoes->where('prioridade', 'alta')->count(),
        ];

        // Instanciação dos Gráficos
        $prioridadeChart = new MissoesPrioridadeChart($dashboard);
        $statusChart = new MissoesStatusChart($dashboard);


        return view('missoes.index', compact(
            'campanha', 'missoes', 'dashboard', 'search', 'prioridade',
            'prioridadeChart', 'statusChart' // Variáveis de gráfico adicionadas
        ));
    }

    public function create(Campanha $campanha)
    {
        $this->authorize('update', $campanha);
        return view('missoes.create', compact('campanha'));
    }

    public function store(Request $request, Campanha $campanha)
    {
        $this->authorize('update', $campanha);

        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descricao'   => 'nullable|string|max:5000',
            'recompensa'  => 'nullable|string|max:1000',
            'prioridade'  => 'required|in:baixa,media,alta',
            'status'      => 'required|in:pendente,em_andamento,concluida,cancelada'
        ]);

        $data['user_id'] = auth()->id();
        $campanha->missoes()->create($data);

        return redirect()
            ->route('missoes.index', $campanha->id)
            ->with('success', 'Missão criada com sucesso!');
    }

    public function show(Campanha $campanha, Missao $missao)
    {
        $this->authorize('view', $campanha);
        return view('missoes.show', compact('campanha', 'missao'));
    }

    public function edit(Campanha $campanha, Missao $missao)
    {
        $this->authorize('update', $campanha);
        return view('missoes.edit', compact('campanha', 'missao'));
    }

    public function update(Request $request, Campanha $campanha, Missao $missao)
    {
        $this->authorize('update', $campanha);

        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descricao'   => 'nullable|string|max:5000',
            'recompensa'  => 'nullable|string|max:1000',
            'prioridade'  => 'required|in:baixa,media,alta',
            'status'      => 'required|in:pendente,em_andamento,concluida,cancelada'
        ]);

        $missao->update($data);

        return redirect()
            ->route('missoes.show', [$campanha->id, $missao->id])
            ->with('success', 'Missão atualizada!');
    }

    public function destroy(Campanha $campanha, Missao $missao)
    {
        $this->authorize('delete', $campanha);

        $missao->delete();

        return redirect()
            ->route('missoes.index', $campanha->id)
            ->with('success', 'Missão removida!');
    }

    public function exportarPdf(Campanha $campanha, Missao $missao)
    {
        $this->authorize('view', $campanha);

        $pdf = Pdf::loadView('missoes.relatorio', compact('campanha', 'missao'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("missao_{$missao->id}.pdf");
    }
}
