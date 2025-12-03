<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Sistema;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    public function __construct()
    {
        // Aplica a política de autorização para o modelo Classe
        $this->authorizeResource(Classe::class, 'classe');
    }

    /**
     * Exibe uma lista de recursos.
     */
    public function index()
    {
        $classes = Classe::with('sistema')->latest()->paginate(20);
        return view('classes.index', compact('classes'));
    }

    /**
     * Mostra o formulário para criar um novo recurso.
     */
    public function create()
    {
        $sistemas = Sistema::all(['id', 'nome']);
        return view('classes.create', compact('sistemas'));
    }

    /**
     * Armazena um recurso recém-criado no armazenamento.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        // Campos JSON são salvos diretamente, pois já foram validados como opcionais ou arrays
        $classe = Classe::create($validated);

        return redirect()->route('classes.index')
            ->with('success', "Classe '{$classe->nome}' criada com sucesso!");
    }

    /**
     * Exibe o recurso especificado.
     */
    public function show(Classe $classe)
    {
        $classe->load('sistema');
        return view('classes.show', compact('classe'));
    }

    /**
     * Mostra o formulário para editar o recurso especificado.
     */
    public function edit(Classe $classe)
    {
        $sistemas = Sistema::all(['id', 'nome']);
        return view('classes.edit', compact('classe', 'sistemas'));
    }

    /**
     * Atualiza o recurso especificado no armazenamento.
     */
    public function update(Request $request, Classe $classe)
    {
        $validated = $request->validate($this->rules());

        $classe->update($validated);

        return redirect()->route('classes.index')
            ->with('success', "Classe '{$classe->nome}' atualizada com sucesso!");
    }

    /**
     * Remove o recurso especificado do armazenamento.
     */
    public function destroy(Classe $classe)
    {
        $nome = $classe->nome;
        $classe->delete();

        return redirect()->route('classes.index')
            ->with('success', "Classe '{$nome}' removida com sucesso!");
    }

    /**
     * Regras de validação para os métodos store e update.
     */
    protected function rules()
    {
        return [
            'nome' => 'required|string|max:100',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string',
            'dado_vida' => 'nullable|string|max:5',
            'pericias_iniciais' => 'nullable|json',
            'equipamento_inicial' => 'nullable|json',
            'usa_magia' => 'nullable|boolean',
            'atributos_bonus' => 'nullable|json',
            'poderes' => 'nullable|json',
            'pagina' => 'nullable|string|max:20',
        ];
    }
        public function pericias()
    {
        return $this->belongsToMany(Pericia::class, 'classe_pericia', 'classe_id', 'pericia_id');
    }
}
