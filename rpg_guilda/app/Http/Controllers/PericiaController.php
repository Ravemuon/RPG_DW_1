<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use App\Models\Pericia;
use Illuminate\Http\Request;

class PericiaController extends Controller
{
    // Exibe todas as perícias de um sistema
    public function index(Sistema $sistema)
    {
        $pericias = $sistema->pericias;
        return view('sistemas.pericias.index', compact('sistema', 'pericias'));
    }

    // Exibe o formulário para criar uma nova perícia
    public function create(Sistema $sistema)
    {
        return view('sistemas.pericias.create', compact('sistema'));
    }
    // Exemplo no controlador
    public function show($id)
    {
        $sistema = Sistema::findOrFail($id);
        return view('sistemas.show', compact('sistema'));
    }


    // Armazena a nova perícia
    public function store(Request $request, Sistema $sistema)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $pericia = new Pericia();
        $pericia->nome = $request->nome;
        $pericia->descricao = $request->descricao;
        $pericia->sistema_id = $sistema->id;
        $pericia->save();

        return redirect()->route('sistemas.pericias.index', $sistema->id)
                         ->with('success', 'Perícia criada com sucesso!');
    }

    // Exibe o formulário para editar uma perícia
    public function edit(Sistema $sistema, Pericia $pericia)
    {
        return view('sistemas.pericias.edit', compact('sistema', 'pericia'));
    }

    // Atualiza a perícia no banco de dados
    public function update(Request $request, Sistema $sistema, Pericia $pericia)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $pericia->nome = $request->nome;
        $pericia->descricao = $request->descricao;
        $pericia->save();

        return redirect()->route('sistemas.pericias.index', $sistema->id)
                         ->with('success', 'Perícia atualizada com sucesso!');
    }

    // Deleta uma perícia
    public function destroy(Sistema $sistema, Pericia $pericia)
    {
        $pericia->delete();

        return redirect()->route('sistemas.pericias.index', $sistema->id)
                         ->with('success', 'Perícia deletada com sucesso!');
    }
}
