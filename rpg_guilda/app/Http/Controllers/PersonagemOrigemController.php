<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personagem;
use App\Models\Origem;

class PersonagemOrigemController extends Controller
{
    // Adicionar origem a um personagem
    public function adicionar(Request $request)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'origem_id' => 'required|exists:origens,id'
        ]);

        $personagem = Personagem::findOrFail($request->personagem_id);

        if ($personagem->origens()->where('origem_id', $request->origem_id)->exists()) {
            return redirect()->back()->with('error', 'Origem já atribuída ao personagem.');
        }

        $personagem->origens()->attach($request->origem_id);

        // Atualizar atributos do personagem após aplicar bônus da origem
        $personagem->inicializarAtributos();

        return redirect()->back()->with('success', 'Origem adicionada ao personagem com sucesso.');
    }

    // Remover origem
    public function remover(Request $request)
    {
        $request->validate([
            'personagem_id' => 'required|exists:personagens,id',
            'origem_id' => 'required|exists:origens,id'
        ]);

        $personagem = Personagem::findOrFail($request->personagem_id);
        $personagem->origens()->detach($request->origem_id);

        // Atualizar atributos após remoção
        $personagem->inicializarAtributos();

        return redirect()->back()->with('success', 'Origem removida com sucesso.');
    }
}
