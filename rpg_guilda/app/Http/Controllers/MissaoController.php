<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Missao;
use App\Models\Campanha;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Charts\MissoesPrioridadeChart;
use App\Charts\MissoesStatusChart;

class MissaoController extends Controller
{
    public function index(Request $request, Campanha $campanha)
    {
        $this->authorize('view', $campanha);

        $search = $request->search;
        $prioridade = $request->prioridade;
        $status = $request->status;

        $baseQuery = $campanha->missoes()
            ->when($search, fn($q) =>
                $q->where('titulo', 'LIKE', "%$search%")
                  ->orWhere('descricao', 'LIKE', "%$search%"))
            ->when($prioridade, fn($q) =>
                $q->where('prioridade', $prioridade))
            ->when($status, fn($q) =>
                $q->where('status', $status));

        $fullCollection = (clone $baseQuery)->get();

        $missoes = $baseQuery
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $dashboard = [
            'pendente'      => $fullCollection->where('status', 'pendente')->count(),
            'em_andamento'  => $fullCollection->where('status', 'em_andamento')->count(),
            'concluida'     => $fullCollection->where('status', 'concluida')->count(),
            'cancelada'     => $fullCollection->where('status', 'cancelada')->count(),
            'baixa'         => $fullCollection->where('prioridade', 'baixa')->count(),
            'media'         => $fullCollection->where('prioridade', 'media')->count(),
            'alta'          => $fullCollection->where('prioridade', 'alta')->count(),
        ];

        $statusChart = new MissoesStatusChart($dashboard);
        $prioridadeChart = new MissoesPrioridadeChart($dashboard);

        return view('missoes.index', compact(
            'campanha', 'missoes', 'dashboard', 'search', 'prioridade', 'status',
            'statusChart', 'prioridadeChart'
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

        $data = $this->validarMissao($request);
        $data['user_id'] = auth()->id();
        $campanha->missoes()->create($data);

        return redirect()
            ->route('missoes.index', $campanha->id)
            ->with('success', 'Missão criada com sucesso!');
    }

    public function show(Campanha $campanha, Missao $missao)
    {
        $this->authorize('view', $campanha);

        if ($missao->campanha_id !== $campanha->id) {
            abort(404, 'Missão não encontrada nesta campanha.');
        }

        return view('missoes.show', compact('campanha', 'missao'));
    }

    public function edit(Campanha $campanha, Missao $missao)
    {
        $this->authorize('update', $campanha);

        if ($missao->campanha_id !== $campanha->id) {
            abort(404, 'Missão não encontrada nesta campanha.');
        }

        return view('missoes.edit', compact('campanha', 'missao'));
    }

    public function update(Request $request, Campanha $campanha, Missao $missao)
    {
        $this->authorize('update', $campanha);

        if ($missao->campanha_id !== $campanha->id) {
            abort(404, 'Missão não encontrada nesta campanha.');
        }

        $data = $this->validarMissao($request);
        $missao->update($data);

        return redirect()
            ->route('missoes.show', [$campanha->id, $missao->id])
            ->with('success', 'Missão atualizada!');
    }

    public function destroy(Campanha $campanha, Missao $missao)
    {
        $this->authorize('delete', $campanha);

        if ($missao->campanha_id !== $campanha->id) {
            abort(404, 'Missão não encontrada nesta campanha.');
        }

        $missao->delete();

        return redirect()
            ->route('missoes.index', $campanha->id)
            ->with('success', 'Missão removida!');
    }

    public function exportarPdf(Campanha $campanha, Missao $missao)
    {
        $this->authorize('view', $campanha);

        if ($missao->campanha_id !== $campanha->id) {
            abort(404, 'Missão não encontrada nesta campanha.');
        }

        $pdf = Pdf::loadView('missoes.relatorio', compact('campanha', 'missao'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("missao_{$missao->id}.pdf");
    }

    private function validarMissao(Request $request)
    {
        return $request->validate([
            'titulo'      => 'required|string|max:255',
            'descricao'   => 'nullable|string|max:5000',
            'recompensa'  => 'nullable|string|max:1000',
            'prioridade'  => 'required|in:baixa,media,alta',
            'status'      => 'required|in:pendente,em_andamento,concluida,cancelada'
        ]);
    }
}
