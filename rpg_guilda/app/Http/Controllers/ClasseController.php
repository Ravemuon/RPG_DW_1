<?php
namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClasseController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');
        $classes = Classe::when($busca, fn($q) => $q->where('nome', 'like', "%$busca%"))->get();
        return view('classes.index', compact('classes', 'busca'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('imagens', 'public');
        }

        Classe::create($data);
        return redirect()->route('classes.index')->with('success', 'Classe criada com sucesso!');
    }

    public function edit(Classe $class)
    {
        return view('classes.edit', ['classe' => $class]);
    }

    public function update(Request $request, Classe $class)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'imagem' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('imagem')) {
            if ($class->imagem) Storage::disk('public')->delete($class->imagem);
            $data['imagem'] = $request->file('imagem')->store('imagens', 'public');
        }

        $class->update($data);
        return redirect()->route('classes.index')->with('success', 'Classe atualizada!');
    }

    public function destroy(Classe $class)
    {
        if ($class->imagem) Storage::disk('public')->delete($class->imagem);
        $class->delete();
        return redirect()->route('classes.index')->with('success', 'Classe excluída!');
    }
}
