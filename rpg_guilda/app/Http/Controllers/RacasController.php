<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Raca;
use App\Models\Sistema;

class RacaController extends Controller
{
    /**
     * Listar todas as raças
     */
    public function index()
    {
        $racas = Raca::with('sistema')->orderBy('nome')->get();
        return view('racas.index', compact('racas'));
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        $sistemas = Sistema::orderBy('nome')->get();
        return view('racas.create', compact('sistemas'));
    }

    /**
     * Armazenar nova raça
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|unique:racas,nome',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'forca_bonus' => 'nullable|integer',
            'destreza_bonus' => 'nullable|integer',
            'constituicao_bonus' => 'nullable|integer',
            'inteligencia_bonus' => 'nullable|integer',
            'sabedoria_bonus' => 'nullable|integer',
            'carisma_bonus' => 'nullable|integer',
            'pagina' => 'nullable|string|max:50',
        ]);

        Raca::create([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'descricao' => $request->descricao,
            'forca_bonus' => $request->forca_bonus ?? 0,
            'destreza_bonus' => $request->destreza_bonus ?? 0,
            'constituicao_bonus' => $request->constituicao_bonus ?? 0,
            'inteligencia_bonus' => $request->inteligencia_bonus ?? 0,
            'sabedoria_bonus' => $request->sabedoria_bonus ?? 0,
            'carisma_bonus' => $request->carisma_bonus ?? 0,
            'pagina' => $request->pagina,
        ]);

        return redirect()->route('racas.index')->with('success', 'Raça criada com sucesso!');
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(Raca $raca)
    {
        $sistemas = Sistema::orderBy('nome')->get();
        return view('racas.edit', compact('raca', 'sistemas'));
    }

    /**
     * Atualizar raça existente
     */
    public function update(Request $request, Raca $raca)
    {
        $request->validate([
            'nome' => 'required|unique:racas,nome,' . $raca->id,
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'forca_bonus' => 'nullable|integer',
            'destreza_bonus' => 'nullable|integer',
            'constituicao_bonus' => 'nullable|integer',
            'inteligencia_bonus' => 'nullable|integer',
            'sabedoria_bonus' => 'nullable|integer',
            'carisma_bonus' => 'nullable|integer',
            'pagina' => 'nullable|string|max:50',
        ]);

        $raca->update([
            'nome' => $request->nome,
            'sistema_id' => $request->sistema_id,
            'descricao' => $request->descricao,
            'forca_bonus' => $request->forca_bonus ?? 0,
            'destreza_bonus' => $request->destreza_bonus ?? 0,
            'constituicao_bonus' => $request->constituicao_bonus ?? 0,
            'inteligencia_bonus' => $request->inteligencia_bonus ?? 0,
            'sabedoria_bonus' => $request->sabedoria_bonus ?? 0,
            'carisma_bonus' => $request->carisma_bonus ?? 0,
            'pagina' => $request->pagina,
        ]);

        return redirect()->route('racas.index')->with('success', 'Raça atualizada com sucesso!');
    }

    /**
     * Remover uma raça
     */
    public function destroy(Raca $raca)
    {
        $raca->delete();
        return redirect()->route('racas.index')->with('success', 'Raça removida com sucesso!');
    }
}
