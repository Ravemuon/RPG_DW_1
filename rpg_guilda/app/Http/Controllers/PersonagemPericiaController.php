<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Pericia;
use Illuminate\Http\Request;

class PersonagemPericiaController extends Controller
{
    public function index($personagem_id)
    {
        $personagem = Personagem::with('pericias')->findOrFail($personagem_id);
        return view('personagem_pericias.index', compact('personagem'));
    }

    public function create($personagem_id)
    {
        $personagem = Personagem::findOrFail($personagem_id);
        $pericias = Pericia::where('sistemaRPG', $personagem->sistemaRPG)->get();
        return view('personagem_pericias.create', compact('personagem','pericias'));
    }

    public function store(Request $request, $personagem_id)
    {
        $request->validate([
            'pericia_id' => 'required|exists:pericias,id',
            'valor' => 'nullable|integer|min:0',
            'definida' => 'boolean',
        ]);

        $personagem = Personagem::findOrFail($personagem_id);
        $personagem->pericias()->syncWithoutDetaching([
            $request->pericia_id => [
                'valor' => $request->valor ?? 0,
                'definida' => $request->definida ?? true
            ]
        ]);

        return redirect()->route('personagem_pericias.index', $personagem_id)
                         ->with('success','Perícia adicionada ao personagem!');
    }

    public function edit($personagem_id, $pericia_id)
    {
        $personagem = Personagem::with('pericias')->findOrFail($personagem_id);
        $pericia = $personagem->pericias()->findOrFail($pericia_id);
        return view('personagem_pericias.edit', compact('personagem','pericia'));
    }

    public function update(Request $request, $personagem_id, $pericia_id)
    {
        $request->validate([
            'valor' => 'required|integer|min:0',
            'definida' => 'boolean',
        ]);

        $personagem = Personagem::findOrFail($personagem_id);
        $personagem->pericias()->updateExistingPivot($pericia_id, [
            'valor' => $request->valor,
            'definida' => $request->definida ?? true,
        ]);

        return redirect()->route('personagem_pericias.index', $personagem_id)
                         ->with('success','Perícia atualizada!');
    }

    public function destroy($personagem_id, $pericia_id)
    {
        $personagem = Personagem::findOrFail($personagem_id);
        $personagem->pericias()->detach($pericia_id);

        return redirect()->route('personagem_pericias.index', $personagem_id)
                         ->with('success','Perícia removida do personagem!');
    }
}
