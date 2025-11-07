<?php

namespace App\Http\Controllers;

use App\Models\Sistema; // Assumindo que seu Model se chama Sistema
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SistemaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Exibe uma lista de todos os sistemas.
     */
    public function index()
    {
        $sistemas = Sistema::all();
        return view('sistemas.index', compact('sistemas'));
    }

    /**
     * Show the form for creating a new resource.
     * Exibe o formulário para criar um novo sistema.
     */
    public function create()
    {
        return view('sistemas.create');
    }

    /**
     * Store a newly created resource in storage.
     * Armazena um novo sistema no banco de dados.
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
                'max_atributos' => 'required|integer|min:1|max:6', // Max 6 conforme a tabela
                'atributo1_nome' => 'nullable|string|max:50',
                'atributo2_nome' => 'nullable|string|max:50',
                'atributo3_nome' => 'nullable|string|max:50',
                'atributo4_nome' => 'nullable|string|max:50',
                'atributo5_nome' => 'nullable|string|max:50',
                'atributo6_nome' => 'nullable|string|max:50',
                'pagina' => 'nullable|string|max:50',
            ]);

            // Se o campo 'regras_opcionais' for enviado como string JSON, ele será armazenado corretamente no MySQL como JSON.
            // Para outros bancos, ou se o front-end envia um array, pode ser necessário um cast no Model.

            $sistema = Sistema::create($validatedData);

            return redirect()->route('sistemas.index')->with('success', 'Sistema criado com sucesso!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Exibe os detalhes de um sistema específico.
     */
    public function show(Sistema $sistema)
    {
        return view('sistemas.show', compact('sistema'));
    }

    /**
     * Show the form for editing the specified resource.
     * Exibe o formulário para editar um sistema existente.
     */
    public function edit(Sistema $sistema)
    {
        return view('sistemas.edit', compact('sistema'));
    }

    /**
     * Update the specified resource in storage.
     * Atualiza um sistema existente no banco de dados.
     */
    public function update(Request $request, Sistema $sistema)
    {
        try {
            $validatedData = $request->validate([
                // 'nome' deve ser único, exceto para o registro atual
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
     * Remove um sistema do banco de dados.
     */
    public function destroy(Sistema $sistema)
    {
        $sistema->delete();

        return redirect()->route('sistemas.index')->with('success', 'Sistema excluído com sucesso!');
    }
}
