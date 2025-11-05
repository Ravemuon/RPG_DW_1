<?php
namespace App\Http\Controllers;

use App\Models\Missao;
use Illuminate\Http\Request;

class MissaoController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');
        $missoes = Missao::when($busca, fn($q) => $q->where('titulo', 'like', "%$busca%"))->get();
        return view('missoes.index', compact('missoes', 'busca'));
    }

    public function create()
    {
        return view('missoes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'recompensa' => 'nullable|string|max:100',
        ]);

        Missao::create($data);
        return redirect()->route('missoes.index')->with('success', 'Missão criada!');
    }

    public function edit(Missao $missao)
    {
        return view('missoes.edit', compact('missao'));
    }

    public function update(Request $request, Missao $missao)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'recompensa' => 'nullable|string|max:100',
        ]);

        $missao->update($data);
        return redirect()->route('missoes.index')->with('success', 'Missão atualizada!');
    }

    public function destroy(Missao $missao)
    {
        $missao->delete();
        return redirect()->route('missoes.index')->with('success', 'Missão excluída!');
    }
}
