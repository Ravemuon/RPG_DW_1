<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use App\Models\Origem;
use Illuminate\Http\Request;

class OrigemController extends Controller
{
    /**
     * Exibir a lista de origens de um sistema.
     */
    public function index($sistemaId)
    {
        $sistema = Sistema::with('origens')->findOrFail($sistemaId);
        return view('sistemas.origens.index', compact('sistema'));
    }

    /**
     * Exibir o formulário para criar uma nova origem.
     */
    public function create($sistemaId)
    {
        $sistema = Sistema::findOrFail($sistemaId);
        return view('sistemas.origens.create', compact('sistema'));
    }

    /**
     * Armazenar uma nova origem.
     */
    public function store(Request $request, $sistemaId)
    {
        $request->validate([
            'nome' => 'required|unique:origens,nome,NULL,id,sistema_id,' . $sistemaId,
            'descricao' => 'nullable|string',
            'pagina' => 'nullable|string|max:50',
        ]);

        Origem::create([
            'nome' => $request->nome,
            'sistema_id' => $sistemaId,
            'descricao' => $request->descricao,
            'pagina' => $request->pagina,
        ]);

        return redirect()->route('origens.index', $sistemaId)
                         ->with('success', 'Origem criada com sucesso!');
    }

    /**
     * Exibir detalhes de uma origem.
     */
    public function show($sistemaId, Origem $origem)
    {
        return view('sistemas.origens.show', compact('sistemaId', 'origem'));
    }

    /**
     * Exibir o formulário para editar uma origem existente.
     */
    public function edit($sistemaId, Origem $origem)
    {
        return view('sistemas.origens.edit', compact('sistemaId', 'origem'));
    }

    /**
     * Atualizar uma origem existente.
     */
    public function update(Request $request, $sistemaId, Origem $origem)
    {
        $request->validate([
            'nome' => 'required|unique:origens,nome,' . $origem->id . ',id,sistema_id,' . $sistemaId,
            'descricao' => 'nullable|string',
            'pagina' => 'nullable|string|max:50',
        ]);

        $origem->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'pagina' => $request->pagina,
        ]);

        return redirect()->route('origens.index', $sistemaId)
                         ->with('success', 'Origem atualizada com sucesso!');
    }

    /**
     * Remover uma origem.
     */
    public function destroy($sistemaId, Origem $origem)
    {
        $origem->delete();

        return redirect()->route('origens.index', $sistemaId)
                         ->with('success', 'Origem removida com sucesso!');
    }
}
