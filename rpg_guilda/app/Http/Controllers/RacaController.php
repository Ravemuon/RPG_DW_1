<?php

namespace App\Http\Controllers;

use App\Models\Raca;
use App\Models\Sistema;
use Illuminate\Http\Request;

class RacaController extends Controller
{
    // Exibir lista de raças de um sistema, com pesquisa e paginação
    public function index(Request $request, Sistema $sistema)
    {
        $query = $sistema->racas()->orderBy('nome');

        // Aplicar filtro de pesquisa por nome
        if ($search = $request->input('search')) {
            $query->where('nome', 'like', "%{$search}%");
        }

        // Paginar resultados (12 por página) e manter parâmetros de busca
        $racas = $query->paginate(12);
        $racas->appends($request->all());

        return view('sistemas.racas.index', compact('sistema', 'racas', 'search'));
    }

    // Exibir formulário de criação de nova raça
    public function create(Sistema $sistema)
    {
        return view('sistemas.racas.create', compact('sistema'));
    }

    // Armazenar nova raça
    public function store(Request $request, Sistema $sistema)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255|unique:racas,nome,NULL,id,sistema_id,' . $sistema->id,
            'descricao' => 'nullable|string',
            'modificadores_atributos' => 'nullable|array',
            'tipo_bonus' => 'required|in:flat,multiplicador,escolha',
            'bonus_livre' => 'nullable|integer|min:0',
            'pagina' => 'nullable|string|max:50',
        ]);

        $data['modificadores_atributos'] = $data['modificadores_atributos'] ?? [];
        $data['sistema_id'] = $sistema->id;

        Raca::create($data);

        return redirect()->route('sistemas.racas.index', $sistema->id)
                         ->with('success', 'Raça criada com sucesso.');
    }

    // Exibir detalhes de uma raça
    public function show(Sistema $sistema, Raca $raca)
    {
        return view('sistemas.racas.show', compact('sistema', 'raca'));
    }

    // Exibir formulário de edição de uma raça existente
    public function edit(Sistema $sistema, Raca $raca)
    {
        return view('sistemas.racas.edit', compact('sistema', 'raca'));
    }

    // Atualizar dados de uma raça existente
    public function update(Request $request, Sistema $sistema, Raca $raca)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255|unique:racas,nome,' . $raca->id . ',id,sistema_id,' . $sistema->id,
            'descricao' => 'nullable|string',
            'modificadores_atributos' => 'nullable|array',
            'tipo_bonus' => 'required|in:flat,multiplicador,escolha',
            'bonus_livre' => 'nullable|integer|min:0',
            'pagina' => 'nullable|string|max:50',
        ]);

        $data['modificadores_atributos'] = $data['modificadores_atributos'] ?? [];

        $raca->update($data);

        return redirect()->route('sistemas.racas.index', $sistema->id)
                         ->with('success', 'Raça atualizada com sucesso.');
    }

    // Remover uma raça
    public function destroy(Sistema $sistema, Raca $raca)
    {
        $raca->delete();

        return redirect()->route('sistemas.racas.index', $sistema->id)
                         ->with('success', 'Raça removida com sucesso.');
    }
}
