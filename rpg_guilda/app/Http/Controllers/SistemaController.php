<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;

class SistemaController extends Controller
{
    /**
     * Lista todos os sistemas com suas relações.
     */
    public function index()
    {
        $sistemas = Sistema::with(['classes', 'origens', 'racas', 'pericias'])->get();
        return view('sistemas.index', compact('sistemas'));
    }

    /**
     * Exibe formulário de criação de sistema.
     */
    public function create()
    {
        return view('sistemas.create');
    }

    /**
     * Armazena um novo sistema.
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
     * Exibe detalhes de um sistema específico.
     */
    public function show(Sistema $sistema)
    {
        $sistema->load(['classes', 'origens', 'racas', 'pericias']);
        return view('sistemas.show', compact('sistema'));
    }

    /**
     * Exibe formulário de edição do sistema.
     */
    public function edit(Sistema $sistema)
    {
        return view('sistemas.edit', compact('sistema'));
    }

    /**
     * Atualiza um sistema existente.
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
     * Remove um sistema.
     */
    public function destroy(Sistema $sistema)
    {
        $sistema->delete();

        return redirect()->route('sistemas.index')->with('success', 'Sistema excluído com sucesso!');
    }

    public function classes(Sistema $sistema)
    {
        return redirect()->route('sistemas.classes.index', $sistema->id);
    }

    public function origens(Sistema $sistema)
    {
        return redirect()->route('sistemas.origens.index', $sistema->id);
    }

    public function racas(Sistema $sistema)
    {
        return redirect()->route('sistemas.racas.index', $sistema->id);
    }

    public function pericias(Sistema $sistema)
    {
        return redirect()->route('sistemas.pericias.index', $sistema->id);
    }
    
    public function exportarPdf()
    {
        $sistemas = Sistema::all();
        $pdf = Pdf::loadView('sistemas.pdf', compact('sistemas'));
        return $pdf->download('sistemas.pdf');
    }

}
