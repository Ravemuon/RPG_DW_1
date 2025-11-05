<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Classe;
use App\Models\Missao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonagemController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');
        $personagens = Personagem::with(['classe', 'missao'])
            ->when($busca, fn($q) => $q->where('nome', 'like', "%$busca%"))
            ->get();

        return view('personagens.index', compact('personagens', 'busca'));
    }

    public function create()
    {
        $classes = Classe::all();
        $missoes = Missao::all();
        return view('personagens.create', compact('classes', 'missoes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'nivel' => 'required|integer|min:1',
            'classe_id' => 'required|exists:classes,id',
            'missao_id' => 'nullable|exists:missoes,id',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('imagens', 'public');
        }

        Personagem::create($data);
        return redirect()->route('personagens.index')->with('success', 'Personagem criado!');
    }

    public function edit(Personagem $personagem)
    {
        $classes = Classe::all();
        $missoes = Missao::all();
        return view('personagens.edit', compact('personagem', 'classes', 'missoes'));
    }

    public function update(Request $request, Personagem $personagem)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'nivel' => 'required|integer|min:1',
            'classe_id' => 'required|exists:classes,id',
            'missao_id' => 'nullable|exists:missoes,id',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('imagem')) {
            if ($personagem->imagem) Storage::disk('public')->delete($personagem->imagem);
            $data['imagem'] = $request->file('imagem')->store('imagens', 'public');
        }

        $personagem->update($data);
        return redirect()->route('personagens.index')->with('success', 'Personagem atualizado!');
    }

    public function destroy(Personagem $personagem)
    {
        if ($personagem->imagem) Storage::disk('public')->delete($personagem->imagem);
        $personagem->delete();
        return redirect()->route('personagens.index')->with('success', 'Personagem excluído!');
    }
}
