<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personagem;
use App\Models\Raca;

class PersonagemRacaController extends Controller
{
    // Adicionar raça a um personagem
    public function store(Request $request, Personagem $personagem)
    {
        $request->validate([
            'raca_id' => 'required|exists:racas,id',
            'nivel' => 'nullable|integer|min:1',
            'descricao_personalizada' => 'nullable|string',
        ]);

        $personagem->racas()->syncWithoutDetaching([
            $request->raca_id => [
                'nivel' => $request->nivel ?? 1,
                'descricao_personalizada' => $request->descricao_personalizada
            ]
        ]);

        return redirect()->back()->with('success', 'Raça adicionada ao personagem com sucesso!');
    }

    // Atualizar dados da raça no personagem
    public function update(Request $request, Personagem $personagem, Raca $raca)
    {
        $request->validate([
            'nivel' => 'nullable|integer|min:1',
            'descricao_personalizada' => 'nullable|string',
        ]);

        $personagem->racas()->updateExistingPivot($raca->id, [
            'nivel' => $request->nivel ?? 1,
            'descricao_personalizada' => $request->descricao_personalizada
        ]);

        return redirect()->back()->with('success', 'Raça do personagem atualizada!');
    }

    // Remover raça do personagem
    public function destroy(Personagem $personagem, Raca $raca)
    {
        $personagem->racas()->detach($raca->id);
        return redirect()->back()->with('success', 'Raça removida do personagem!');
    }
}
