<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personagem;
use App\Models\Origem;

class PersonagemOrigemController extends Controller
{
    // Adiciona uma origem a um personagem
    public function adicionar(Request $request)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'origem_id' => 'required|exists:origens,id'
        ]);

        $personagem = Personagem::findOrFail($request->personagem_id);

        // Verifica se a origem já foi atribuída
        if ($personagem->origens()->where('origem_id', $request->origem_id)->exists()) {
            return redirect()->back()->with('error', 'Origem já atribuída ao personagem.');
        }

        // Associa a origem ao personagem
        $personagem->origens()->attach($request->origem_id);

        // Atualiza os atributos do personagem com os bônus da origem
        $personagem->inicializarAtributos();

        return redirect()->back()->with('success', 'Origem adicionada ao personagem com sucesso.');
    }

    // Remove uma origem de um personagem
    public function remover(Request $request)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'origem_id' => 'required|exists:origens,id'
        ]);

        $personagem = Personagem::findOrFail($request->personagem_id);
        $personagem->origens()->detach($request->origem_id);

        // Atualiza os atributos do personagem após a remoção
        $personagem->inicializarAtributos();

        return redirect()->back()->with('success', 'Origem removida com sucesso.');
    }
}
