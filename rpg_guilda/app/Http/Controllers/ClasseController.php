<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Sistema;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    // Listar classes de um sistema com filtro por busca e paginação
    public function index(Request $request, Sistema $sistema)
    {
        $query = $sistema->classes()->orderBy('nome');

        if ($search = $request->input('search')) {
            $query->where('nome', 'like', "%{$search}%");
        }

        $classes = $query->paginate(12);
        $classes->appends($request->all());

        return view('sistemas.classes.index', compact('sistema', 'classes', 'search'));
    }

    // Mostrar formulário de criação de classe
    public function create(Sistema $sistema)
    {
        return view('sistemas.classes.create', compact('sistema'));
    }

    // Armazenar nova classe no banco
    public function store(Request $request, Sistema $sistema)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100|unique:classes,nome,NULL,id,sistema_id,' . $sistema->id,
            'descricao' => 'nullable|string',
            'dado_vida' => 'nullable|string|max:5',
            'pericias_iniciais' => 'nullable|array',
            'equipamento_inicial' => 'nullable|array',
            'usa_magia' => 'boolean',
            'atributos_bonus' => 'nullable|array',
            'poderes' => 'nullable|array',
            'pagina' => 'nullable|string|max:20',
        ]);

        $data['sistema_id'] = $sistema->id;

        $data['pericias_iniciais'] = $data['pericias_iniciais'] ?? [];
        $data['equipamento_inicial'] = $data['equipamento_inicial'] ?? [];
        $data['atributos_bonus'] = $data['atributos_bonus'] ?? [];
        $data['poderes'] = $data['poderes'] ?? [];

        Classe::create($data);

        return redirect()->route('sistemas.classes.index', $sistema->id)
                         ->with('success', 'Classe criada com sucesso.');
    }

    // Exibir detalhes de uma classe
    public function show(Sistema $sistema, Classe $classe)
    {
        return view('sistemas.classes.show', compact('sistema', 'classe'));
    }

    // Mostrar formulário de edição de classe
    public function edit(Sistema $sistema, Classe $classe)
    {
        return view('sistemas.classes.edit', compact('sistema', 'classe'));
    }

    // Atualizar classe existente
    public function update(Request $request, Sistema $sistema, Classe $classe)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:100|unique:classes,nome,' . $classe->id . ',id,sistema_id,' . $sistema->id,
            'descricao' => 'nullable|string',
            'dado_vida' => 'nullable|string|max:5',
            'pericias_iniciais' => 'nullable|array',
            'equipamento_inicial' => 'nullable|array',
            'usa_magia' => 'boolean',
            'atributos_bonus' => 'nullable|array',
            'poderes' => 'nullable|array',
            'pagina' => 'nullable|string|max:20',
        ]);

        $data['pericias_iniciais'] = $data['pericias_iniciais'] ?? [];
        $data['equipamento_inicial'] = $data['equipamento_inicial'] ?? [];
        $data['atributos_bonus'] = $data['atributos_bonus'] ?? [];
        $data['poderes'] = $data['poderes'] ?? [];

        $classe->update($data);

        return redirect()->route('sistemas.classes.index', $sistema->id)
                         ->with('success', 'Classe atualizada com sucesso.');
    }

    // Remover classe do sistema
    public function destroy(Sistema $sistema, Classe $classe)
    {
        $classe->delete();

        return redirect()->route('sistemas.classes.index', $sistema->id)
                         ->with('success', 'Classe removida com sucesso.');
    }
}
