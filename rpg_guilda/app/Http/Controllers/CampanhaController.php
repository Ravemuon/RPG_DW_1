<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampanhaController extends Controller
{
    public function index()
    {
        $campanhas = Auth::user()->tipo === 'mestre'
            ? Campanha::where('criador_id', Auth::id())->get()
            : Auth::user()->campanhasComoJogador;

        return view('campanhas.index', compact('campanhas'));
    }

    public function create()
    {
        return view('campanhas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string',
            'descricao' => 'nullable|string',
            'privada' => 'boolean'
        ]);

        $campanha = Campanha::create([
            'nome' => $request->nome,
            'sistemaRPG' => $request->sistemaRPG,
            'descricao' => $request->descricao,
            'privada' => $request->privada ?? false,
            'criador_id' => Auth::id()
        ]);

        // Adiciona o criador como mestre na tabela pivot
        $campanha->jogadores()->attach(Auth::id(), ['status' => 'mestre']);

        return redirect()->route('campanhas.index')->with('success', 'Campanha criada com sucesso!');
    }

    public function show($id)
    {
        $campanha = Campanha::findOrFail($id);
        return view('campanhas.show', compact('campanha'));
    }

    public function edit($id)
    {
        $campanha = Campanha::findOrFail($id);
        return view('campanhas.edit', compact('campanha'));
    }

    public function update(Request $request, $id)
    {
        $campanha = Campanha::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'status' => 'required|in:ativa,inativa',
            'privada' => 'boolean'
        ]);

        $campanha->update($request->all());

        return redirect()->route('campanhas.index')->with('success', 'Campanha atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $campanha = Campanha::findOrFail($id);
        $campanha->delete();

        return redirect()->route('campanhas.index')->with('success', 'Campanha deletada com sucesso!');
    }
}
