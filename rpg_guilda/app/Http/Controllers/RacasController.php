<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Raca;

class RacaController extends Controller
{
    public function index()
    {
        $racas = Raca::all();
        return view('racas.index', compact('racas'));
    }

    public function create()
    {
        return view('racas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|unique:racas,nome',
            'sistema_rpg' => 'required',
        ]);

        Raca::create($request->all());

        return redirect()->route('racas.index')->with('success', 'Raça criada com sucesso!');
    }

    public function edit(Raca $raca)
    {
        return view('racas.edit', compact('raca'));
    }

    public function update(Request $request, Raca $raca)
    {
        $request->validate([
            'nome' => 'required|unique:racas,nome,' . $raca->id,
            'sistema_rpg' => 'required',
        ]);

        $raca->update($request->all());

        return redirect()->route('racas.index')->with('success', 'Raça atualizada com sucesso!');
    }

    public function destroy(Raca $raca)
    {
        $raca->delete();
        return redirect()->route('racas.index')->with('success', 'Raça removida!');
    }
}
