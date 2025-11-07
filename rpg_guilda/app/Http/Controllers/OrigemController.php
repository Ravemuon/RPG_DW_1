<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;
use App\Models\Origem;

class OrigemController extends Controller
{
    public function index($sistemaId)
    {
        $sistema = Sistema::with('origens')->findOrFail($sistemaId);
        return view('sistemas.origens.index', compact('sistema'));
    }

    public function create()
    {
        return view('origens.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'bônus' => 'nullable|array',
        ]);

        Origem::create($request->all());

        return redirect()->route('origens.index')->with('success', 'Origem criada com sucesso!');
    }

    public function edit(Origem $origem)
    {
        return view('origens.edit', compact('origem'));
    }

    public function update(Request $request, Origem $origem)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'bônus' => 'nullable|array',
        ]);

        $origem->update($request->all());

        return redirect()->route('origens.index')->with('success', 'Origem atualizada com sucesso!');
    }

    public function destroy(Origem $origem)
    {
        $origem->delete();
        return redirect()->route('origens.index')->with('success', 'Origem deletada com sucesso!');
    }
}
