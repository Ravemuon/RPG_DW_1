<?php

namespace App\Http\Controllers;

use App\Models\Pericia;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PericiaController extends Controller
{
    // Lista todas as perícias com paginação
    public function index()
    {
        $pericias = Pericia::with('sistema')
            ->orderBy('nome')
            ->paginate(15);

        return view('pericias.index', compact('pericias'));
    }

    // Mostra formulário de criação de perícia
    public function create()
    {
        $sistemas = Sistema::pluck('nome', 'id');
        return view('pericias.create', compact('sistemas'));
    }

    // Armazena nova perícia no banco
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sistema_id' => [
                'required',
                'exists:sistemas,id',
                Rule::unique('pericias')->where(fn ($q) =>
                    $q->where('nome', $request->nome)
                ),
            ],
            'atributo_relacionado' => ['required', 'string', 'max:255'],
            'atributo_nome' => ['nullable', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'modificador' => ['nullable', 'integer'],
        ]);

        Pericia::create($validated);

        return redirect()->route('pericias.index')
            ->with('success', 'Perícia criada com sucesso!');
    }

    // Mostra detalhes de uma perícia
    public function show(Pericia $pericia)
    {
        $pericia->load('sistema');
        return view('pericias.show', compact('pericia'));
    }

    // Mostra formulário de edição de perícia
    public function edit(Pericia $pericia)
    {
        $sistemas = Sistema::pluck('nome', 'id');
        return view('pericias.edit', compact('pericia', 'sistemas'));
    }

    // Atualiza perícia existente
    public function update(Request $request, Pericia $pericia)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sistema_id' => [
                'required',
                'exists:sistemas,id',
                Rule::unique('pericias')
                    ->ignore($pericia->id)
                    ->where(fn ($q) =>
                        $q->where('nome', $request->nome)
                    ),
            ],
            'atributo_relacionado' => ['required', 'string', 'max:255'],
            'atributo_nome' => ['nullable', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'modificador' => ['nullable', 'integer'],
        ]);

        $pericia->update($validated);

        return redirect()->route('pericias.index')
            ->with('success', 'Perícia atualizada com sucesso!');
    }

    // Remove perícia do banco
    public function destroy(Pericia $pericia)
    {
        $pericia->delete();

        return redirect()->route('pericias.index')
            ->with('success', 'Perícia excluída com sucesso!');
    }
}
