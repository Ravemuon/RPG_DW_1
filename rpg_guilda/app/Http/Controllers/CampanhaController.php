<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampanhaController extends Controller
{
    // Listagem para mestre/jogador logado
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
        // Validação dos campos
        $request->validate([
            'nome' => 'required|string|max:100',
            'sistemaRPG' => 'required|string',
            'descricao' => 'nullable|string',
            'privada' => 'nullable|boolean',
            'codigo_convite' => 'nullable|string|max:20'
        ]);

        // Criação da campanha
        $campanha = Campanha::create([
            'nome' => $request->nome,
            'sistemaRPG' => $request->sistemaRPG,
            'descricao' => $request->descricao,
            'privada' => $request->privada ?? false,
            'codigo_convite' => $request->codigo_convite ?? null, // Será gerado automaticamente no Model se vazio
            'criador_id' => Auth::id()
        ]);

        // Adiciona o criador como mestre na tabela pivot
        $campanha->jogadores()->attach(Auth::id(), ['status' => 'mestre']);

        // Redireciona para a lista de campanhas com mensagem de sucesso
        return redirect()->route('campanhas.index')
                        ->with('success', 'Campanha criada com sucesso! Você é o mestre desta campanha.');
    }


    public function show($id)
    {
        $campanha = Campanha::with(['jogadores', 'sessoes', 'criador'])->findOrFail($id);
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
            'privada' => 'boolean',
            'codigo' => 'nullable|string|max:20'
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

    // Listagem de todas as campanhas (para visitantes e logados)
    public function listarTodas()
    {
        $campanhas = Campanha::with(['jogadores', 'missoes', 'criador'])->get();

        if (!Auth::check()) {
            // Só exibe públicas
            $campanhas = $campanhas->where('privada', false);
        }

        return view('campanhas.todas', compact('campanhas'));

    }

    // Entrar em campanha privada
    public function entrar(Request $request, $id)
    {
        $campanha = Campanha::findOrFail($id);

        if ($campanha->privada) {
            $request->validate([
                'codigo' => 'required|string'
            ]);

            if ($request->codigo !== $campanha->codigo) {
                return redirect()->back()->withErrors(['codigo' => 'Código incorreto para entrar na campanha.']);
            }
        }

        $user = Auth::user();
        $campanha->jogadores()->syncWithoutDetaching([$user->id => ['status' => 'player']]);

        return redirect()->route('campanhas.show', $campanha->id)->with('success', 'Você entrou na campanha!');
    }
}
