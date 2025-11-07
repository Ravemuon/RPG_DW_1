<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personagem;
use App\Models\Pericia;

class PersonagemPericiaController extends Controller
{
    // Adicionar perícia a um personagem
    public function adicionar(Request $request)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'pericia_id' => 'required|exists:pericias,id',
            'valor' => 'nullable|integer',
            'definida' => 'nullable|boolean'
        ]);

        $personagem = Personagem::findOrFail($request->personagem_id);

        // Verifica se já possui a perícia
        if ($personagem->pericias()->where('pericia_id', $request->pericia_id)->exists()) {
            return redirect()->back()->with('error', 'Perícia já atribuída ao personagem.');
        }

        $personagem->pericias()->attach($request->pericia_id, [
            'valor' => $request->valor,
            'definida' => $request->definida ?? false
        ]);

        return redirect()->back()->with('success', 'Perícia adicionada ao personagem com sucesso.');
    }

    // Remover perícia de um personagem
    public function remover(Request $request)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'pericia_id' => 'required|exists:pericias,id'
        ]);

        $personagem = Personagem::findOrFail($request->personagem_id);
        $personagem->pericias()->detach($request->pericia_id);

        return redirect()->back()->with('success', 'Perícia removida com sucesso.');
    }

    // Atualizar valor de uma perícia
    public function atualizar(Request $request)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'pericia_id' => 'required|exists:pericias,id',
            'valor' => 'nullable|integer',
            'definida' => 'nullable|boolean'
        ]);

        $personagem = Personagem::findOrFail($request->personagem_id);

        if (!$personagem->pericias()->where('pericia_id', $request->pericia_id)->exists()) {
            return redirect()->back()->with('error', 'Perícia não encontrada para este personagem.');
        }

        $personagem->pericias()->updateExistingPivot($request->pericia_id, [
            'valor' => $request->valor,
            'definida' => $request->definida ?? false
        ]);

        return redirect()->back()->with('success', 'Perícia atualizada com sucesso.');
    }
}
