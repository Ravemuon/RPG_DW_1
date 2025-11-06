<?php

namespace App\Http\Controllers;

use App\Models\Pericia;
use Illuminate\Http\Request;

class PericiaController extends Controller
{
    public function index()
    {
        $pericias = Pericia::all();
        return view('pericias.index', compact('pericias'));
    }

    public function create()
    {
        return view('pericias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string|max:50',
            'automatica' => 'boolean',
            'formula' => 'nullable|json',
        ]);

        Pericia::create($request->all());

        return redirect()->route('pericias.index')->with('success', 'Perícia criada com sucesso!');
    }

    public function edit(Pericia $pericia)
    {
        return view('pericias.edit', compact('pericia'));
    }

    public function update(Request $request, Pericia $pericia)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string|max:50',
            'automatica' => 'boolean',
            'formula' => 'nullable|json',
        ]);

        $pericia->update($request->all());

        return redirect()->route('pericias.index')->with('success', 'Perícia atualizada com sucesso!');
    }

    public function destroy(Pericia $pericia)
    {
        $pericia->delete();
        return redirect()->route('pericias.index')->with('success', 'Perícia removida!');
    }
}
