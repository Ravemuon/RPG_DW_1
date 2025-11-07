<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SistemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Carrega apenas relações existentes: classes, origens, raças e pericias
        $sistemas = Sistema::with(['classes', 'origens', 'racas', 'pericias'])->get();

        return view('sistemas.index', compact('sistemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sistemas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nome' => 'required|string|max:100|unique:sistemas,nome',
                'descricao' => 'nullable|string',
                'foco' => 'nullable|string|max:100',
                'mecanica_principal' => 'nullable|string|max:50',
                'complexidade' => 'nullable|string|max:50',
                'regras_opcionais' => 'nullable|json',
                'max_atributos' => 'required|integer|min:1|max:6',
                'atributo1_nome' => 'nullable|string|max:50',
                'atributo2_nome' => 'nullable|string|max:50',
                'atributo3_nome' => 'nullable|string|max:50',
                'atributo4_nome' => 'nullable|string|max:50',
                'atributo5_nome' => 'nullable|string|max:50',
                'atributo6_nome' => 'nullable|string|max:50',
                'pagina' => 'nullable|string|max:50',
            ]);

            Sistema::create($validatedData);

            return redirect()->route('sistemas.index')->with('success', 'Sistema criado com sucesso!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sistema $sistema)
    {
        // Carrega relações necessárias para o show
        $sistema->load(['classes', 'origens', 'racas', 'pericias']);
        return view('sistemas.show', compact('sistema'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sistema $sistema)
    {
        return view('sistemas.edit', compact('sistema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sistema $sistema)
    {
        try {
            $validatedData = $request->validate([
                'nome' => 'required|string|max:100|unique:sistemas,nome,' . $sistema->id,
                'descricao' => 'nullable|string',
                'foco' => 'nullable|string|max:100',
                'mecanica_principal' => 'nullable|string|max:50',
                'complexidade' => 'nullable|string|max:50',
                'regras_opcionais' => 'nullable|json',
                'max_atributos' => 'required|integer|min:1|max:6',
                'atributo1_nome' => 'nullable|string|max:50',
                'atributo2_nome' => 'nullable|string|max:50',
                'atributo3_nome' => 'nullable|string|max:50',
                'atributo4_nome' => 'nullable|string|max:50',
                'atributo5_nome' => 'nullable|string|max:50',
                'atributo6_nome' => 'nullable|string|max:50',
                'pagina' => 'nullable|string|max:50',
            ]);

            $sistema->update($validatedData);

            return redirect()->route('sistemas.index')->with('success', 'Sistema atualizado com sucesso!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sistema $sistema)
    {
        $sistema->delete();

        return redirect()->route('sistemas.index')->with('success', 'Sistema excluído com sucesso!');
    }
}
