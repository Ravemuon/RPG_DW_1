<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;
use App\Models\Classe;

class ClasseController extends Controller
{
    // ===================================================
    // 🔹 Lista todas as classes
    // ===================================================
    public function index($sistemaId)
    {
        $sistema = Sistema::with('classes')->findOrFail($sistemaId);
        return view('sistemas.classes.index', compact('sistema'));
    }

    // ===================================================
    // 🔹 Formulário para criar nova classe
    // ===================================================
    public function create()
    {
        return view('classes.create');
    }

    // ===================================================
    // 🔹 Armazena nova classe no banco
    // ===================================================
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'forca' => 'nullable|integer',
            'destreza' => 'nullable|integer',
            'constituicao' => 'nullable|integer',
            'inteligencia' => 'nullable|integer',
            'sabedoria' => 'nullable|integer',
            'carisma' => 'nullable|integer',
            'agilidade' => 'nullable|integer',
            'intelecto' => 'nullable|integer',
            'presenca' => 'nullable|integer',
            'vigor' => 'nullable|integer',
            'nex' => 'nullable|integer',
            'sanidade' => 'nullable|integer',
            'aspects' => 'nullable|array',
            'stunts' => 'nullable|array',
            'fate_points' => 'nullable|integer',
            'atributos_custom' => 'nullable|array',
            'poderes' => 'nullable|array'
        ]);

        Classe::create($request->all());

        return redirect()->route('classes.index')
                         ->with('success', 'Classe criada com sucesso!');
    }

    // ===================================================
    // 🔹 Exibe detalhes de uma classe
    // ===================================================
    public function show(Classe $classe)
    {
        return view('classes.show', compact('classe'));
    }

    // ===================================================
    // 🔹 Formulário de edição de classe
    // ===================================================
    public function edit(Classe $classe)
    {
        return view('classes.edit', compact('classe'));
    }

    // ===================================================
    // 🔹 Atualiza classe existente
    // ===================================================
    public function update(Request $request, Classe $classe)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'forca' => 'nullable|integer',
            'destreza' => 'nullable|integer',
            'constituicao' => 'nullable|integer',
            'inteligencia' => 'nullable|integer',
            'sabedoria' => 'nullable|integer',
            'carisma' => 'nullable|integer',
            'agilidade' => 'nullable|integer',
            'intelecto' => 'nullable|integer',
            'presenca' => 'nullable|integer',
            'vigor' => 'nullable|integer',
            'nex' => 'nullable|integer',
            'sanidade' => 'nullable|integer',
            'aspects' => 'nullable|array',
            'stunts' => 'nullable|array',
            'fate_points' => 'nullable|integer',
            'atributos_custom' => 'nullable|array',
            'poderes' => 'nullable|array'
        ]);

        $classe->update($request->all());

        return redirect()->route('classes.index')
                         ->with('success', 'Classe atualizada com sucesso!');
    }

    // ===================================================
    // 🔹 Deleta uma classe
    // ===================================================
    public function destroy(Classe $classe)
    {
        $classe->delete();

        return redirect()->route('classes.index')
                         ->with('success', 'Classe deletada com sucesso!');
    }
}
