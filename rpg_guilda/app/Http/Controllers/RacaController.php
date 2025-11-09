<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Raca;
use App\Models\Sistema;

class RacaController extends Controller
{
    // Lista todas as raças de um sistema
    public function index($sistemaId)
    {
        $sistema = Sistema::with('racas')->findOrFail($sistemaId);
        return view('sistemas.racas.index', compact('sistema'));
    }

    // Exibe o formulário para criar uma nova raça
    public function create($sistemaId)
    {
        $sistema = Sistema::findOrFail($sistemaId);
        return view('sistemas.racas.create', compact('sistema'));
    }

    // Armazena uma nova raça no sistema
    public function store(Request $request, $sistemaId)
    {
        $request->validate([
            'nome' => 'required|unique:racas,nome,NULL,id,sistema_id,' . $sistemaId,
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
            'sistema_id' => $sistemaId,
            'descricao' => $request->descricao,
            'forca_bonus' => $request->forca_bonus ?? 0,
            'destreza_bonus' => $request->destreza_bonus ?? 0,
            'constituicao_bonus' => $request->constituicao_bonus ?? 0,
            'inteligencia_bonus' => $request->inteligencia_bonus ?? 0,
            'sabedoria_bonus' => $request->sabedoria_bonus ?? 0,
            'carisma_bonus' => $request->carisma_bonus ?? 0,
            'pagina' => $request->pagina,
        ]);

        return redirect()->route('sistemas.racas.index', $sistemaId)
                         ->with('success', 'Raça criada com sucesso!');
    }

    // Exibe os detalhes de uma raça
    public function show(Raca $raca)
    {
        return view('sistemas.racas.show', compact('raca'));
    }

    // Exibe o formulário de edição de uma raça
    public function edit(Raca $raca)
    {
        $sistema = $raca->sistema;
        return view('sistemas.racas.edit', compact('raca', 'sistema'));
    }

    // Atualiza os dados de uma raça
    public function update(Request $request, Raca $raca)
    {
        $request->validate([
            'nome' => 'required|unique:racas,nome,' . $raca->id . ',id,sistema_id,' . $raca->sistema_id,
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
            'descricao' => $request->descricao,
            'forca_bonus' => $request->forca_bonus ?? 0,
            'destreza_bonus' => $request->destreza_bonus ?? 0,
            'constituicao_bonus' => $request->constituicao_bonus ?? 0,
            'inteligencia_bonus' => $request->inteligencia_bonus ?? 0,
            'sabedoria_bonus' => $request->sabedoria_bonus ?? 0,
            'carisma_bonus' => $request->carisma_bonus ?? 0,
            'pagina' => $request->pagina,
        ]);

        return redirect()->route('sistemas.racas.index', $raca->sistema_id)
                         ->with('success', 'Raça atualizada com sucesso!');
    }

    // Remove uma raça do sistema
    public function destroy(Raca $raca)
    {
        $sistemaId = $raca->sistema_id;
        $raca->delete();

        return redirect()->route('sistemas.racas.index', $sistemaId)
                         ->with('success', 'Raça removida com sucesso!');
    }
}
