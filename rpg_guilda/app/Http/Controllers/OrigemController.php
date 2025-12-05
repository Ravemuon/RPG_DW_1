<?php

namespace App\Http\Controllers;

use App\Models\Origem;
use App\Models\Sistema;
use Illuminate\Http\Request;

class OrigemController extends Controller
{
    // Lista todas as origens de um sistema, com busca opcional
    public function index(Request $request, Sistema $sistema)
    {
        $search = $request->query('search');

        $origensQuery = $sistema->origens()->orderBy('nome');

        if ($search) {
            $origensQuery->where('nome', 'like', "%{$search}%");
        }

        $origens = $origensQuery->get();

        return view('sistemas.origens.index', compact('sistema', 'origens', 'search'));
    }

    // Mostra o formulário de criação de origem
    public function create(Sistema $sistema)
    {
        return view('sistemas.origens.create', compact('sistema'));
    }

    // Armazena uma nova origem no banco
    public function store(Request $request, Sistema $sistema)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255|unique:origens,nome,NULL,id,sistema_id,' . $sistema->id,
            'descricao' => 'nullable|string',
            'pericias_iniciais' => 'nullable|array',
            'recursos_adicionais' => 'nullable|array',
            'pagina' => 'nullable|string|max:50',
        ]);

        $data['pericias_iniciais'] = $data['pericias_iniciais'] ?? [];
        $data['recursos_adicionais'] = $data['recursos_adicionais'] ?? [];
        $data['sistema_id'] = $sistema->id;

        Origem::create($data);

        return redirect()->route('sistemas.origens.index', $sistema->id)
                         ->with('success', 'Origem criada com sucesso.');
    }

    // Mostra detalhes de uma origem específica
    public function show(Sistema $sistema, Origem $origem)
    {
        return view('sistemas.origens.show', compact('sistema', 'origem'));
    }

    // Mostra o formulário de edição de uma origem
    public function edit(Sistema $sistema, Origem $origem)
    {
        return view('sistemas.origens.edit', compact('sistema', 'origem'));
    }

    // Atualiza os dados de uma origem existente
    public function update(Request $request, Sistema $sistema, Origem $origem)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255|unique:origens,nome,' . $origem->id . ',id,sistema_id,' . $sistema->id,
            'descricao' => 'nullable|string',
            'pericias_iniciais' => 'nullable|array',
            'recursos_adicionais' => 'nullable|array',
            'pagina' => 'nullable|string|max:50',
        ]);

        $data['pericias_iniciais'] = $data['pericias_iniciais'] ?? [];
        $data['recursos_adicionais'] = $data['recursos_adicionais'] ?? [];

        $origem->update($data);

        return redirect()->route('sistemas.origens.index', $sistema->id)
                         ->with('success', 'Origem atualizada com sucesso.');
    }

    // Remove uma origem do sistema
    public function destroy(Sistema $sistema, Origem $origem)
    {
        $origem->delete();

        return redirect()->route('sistemas.origens.index', $sistema->id)
                         ->with('success', 'Origem removida com sucesso.');
    }
}
