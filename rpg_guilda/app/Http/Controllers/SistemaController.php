<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;

class SistemaController extends Controller
{
    // Lista sistemas com busca e filtro por complexidade
    public function index(Request $request)
    {
        $search = $request->input('search');
        $complexidade = $request->input('complexidade');

        $sistemas = Sistema::query()
            ->with(['classes','origens','racas','pericias'])
            ->when($search, fn($q) => 
                $q->where('nome','like',"%$search%")
                  ->orWhere('foco','like',"%$search%")
                  ->orWhere('mecanica_principal','like',"%$search%")
            )
            ->when($complexidade, fn($q)=> 
                $q->where('complexidade',$complexidade)
            )
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('sistemas.index', compact('sistemas','search','complexidade'));
    }

    // Exibe detalhes de um sistema
    public function show(Sistema $sistema)
    {
        $sistema->load(['classes','origens','racas','pericias']);

        $complexidadeBadge = match(strtolower($sistema->complexidade ?? 'indefinido')) {
            'baixa' => ['🟢 Fácil', 'bg-success text-white'],
            'media', 'média' => ['🟡 Intermediária', 'bg-warning text-dark'],
            'alta' => ['🔥 Difícil', 'bg-danger text-white'],
            default => ['⚪ Indefinido', 'bg-secondary text-white'],
        };

        $usaSanidade = $sistema->usa_sanidade ? 'Sim' : 'Não';

        return view('sistemas.show', [
            'sistema' => $sistema,
            'complexidadeBadge' => $complexidadeBadge,
            'usaSanidade' => $usaSanidade
        ]);
    }

    // Exibe formulário para criar sistema
    public function create()
    {
        return view('sistemas.create');
    }

    // Armazena novo sistema
    public function store(Request $request)
    {
        try {
            $validated = $this->validar($request);
            $data = $this->formatarDados($validated);

            Sistema::create($data);

            return redirect()->route('sistemas.index')
                             ->with('success','Sistema criado com sucesso!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    // Exibe formulário para editar sistema
    public function edit(Sistema $sistema)
    {
        return view('sistemas.edit', compact('sistema'));
    }

    // Atualiza sistema existente
    public function update(Request $request, Sistema $sistema)
    {
        try {
            $validated = $this->validar($request,$sistema->id);
            $data = $this->formatarDados($validated);

            $sistema->update($data);

            return redirect()->route('sistemas.index')
                             ->with('success','Sistema atualizado com sucesso!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    // Exclui sistema
    public function destroy(Sistema $sistema)
    {
        $sistema->delete();
        return redirect()->route('sistemas.index')
                         ->with('success','Sistema excluído com sucesso!');
    }

    // Redireciona para rotas de relacionamentos
    public function classes(Sistema $sistema)  { return redirect()->route('sistemas.classes.index', $sistema); }
    public function origens(Sistema $sistema)  { return redirect()->route('sistemas.origens.index', $sistema); }
    public function racas(Sistema $sistema)    { return redirect()->route('sistemas.racas.index', $sistema); }
    public function pericias(Sistema $sistema) { return redirect()->route('sistemas.pericias.index', $sistema); }

    // Exporta PDF com todos os sistemas
    public function exportarPdf()
    {
        $sistemas = Sistema::all();
        $pdf = Pdf::loadView('sistemas.pdf', compact('sistemas'));
        return $pdf->download('sistemas.pdf');
    }

    // Exporta PDF individual
    public function visualizarPdf(Sistema $sistema)
    {
        $sistema->load(['classes','origens','racas','pericias']);
        $pdf = Pdf::loadView('sistemas.pdf-unico', compact('sistema'));
        return $pdf->stream("sistema_{$sistema->id}.pdf");
    }

    // -------------------- MÉTODOS PRIVADOS ---------------------

    // Valida dados do sistema
    private function validar(Request $request,$id=null)
    {
        return $request->validate([
            'nome' => 'required|string|max:100|unique:sistemas,nome,' . $id,
            'descricao' => 'nullable|string',
            'foco' => 'nullable|string|max:100',
            'mecanica_principal' => 'nullable|string|max:50',
            'complexidade' => 'nullable|string|max:50',

            'usa_sanidade' => 'nullable|boolean',
            'formula_pontos_vida' => 'nullable|string|max:200',

            'recursos' => 'nullable|json',
            'regras_opcionais' => 'nullable|json',

            'max_atributos' => 'nullable|integer|min:0|max:6'
        ]);
    }

    // Formata dados antes de criar ou atualizar
    private function formatarDados($dados)
    {
        return [
            'nome'               => $dados['nome'],
            'descricao'          => $dados['descricao'] ?? null,
            'foco'               => $dados['foco'] ?? null,
            'mecanica_principal' => $dados['mecanica_principal'] ?? null,
            'complexidade'       => $dados['complexidade'] ?? null,
            'usa_sanidade'       => isset($dados['usa_sanidade']),
            'formula_pontos_vida'=> $dados['formula_pontos_vida'] ?? null,

            'atributos'         => ['FOR'=>'Força','DES'=>'Destreza'],
            'recursos'          => json_decode($dados['recursos'] ?? "[]",true),
            'regras_opcionais'  => json_decode($dados['regras_opcionais'] ?? "{}",true),
        ];
    }
}
