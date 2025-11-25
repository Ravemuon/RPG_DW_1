<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use App\Models\Pericia;
use Illuminate\Http\Request;

class PericiaController extends Controller
{
    /**
     * Exibe todas as perícias de um sistema específico.
     * @param Sistema $sistema
     * @return \Illuminate\View\View
     */
    public function index(Sistema $sistema)
    {
        // Garante que a lista de perícias do sistema é usada, geralmente via relacionamento.
        $pericias = $sistema->pericias()->orderBy('nome')->get();
        return view('sistemas.pericias.index', compact('sistema', 'pericias'));
    }

    /**
     * Exibe o formulário para criar uma nova perícia para o sistema.
     * @param Sistema $sistema
     * @return \Illuminate\View\View
     */
    public function create(Sistema $sistema)
    {
        return view('sistemas.pericias.create', compact('sistema'));
    }

    /**
     * Armazena a nova perícia no banco de dados.
     * @param Request $request
     * @param Sistema $sistema
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Sistema $sistema)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:pericias,nome,NULL,id,sistema_id,' . $sistema->id, // Validação de unicidade por sistema
            'atributo_relacionado' => 'required|string|max:50',
            'descricao' => 'nullable|string',
        ]);

        $pericia = new Pericia();
        $pericia->nome = $request->nome;
        $pericia->descricao = $request->descricao;
        $pericia->atributo_relacionado = $request->atributo_relacionado;
        // O atributo_nome e modificador podem ser definidos aqui com base na lógica do sistema,
        // mas é mais comum que sejam preenchidos automaticamente.
        $pericia->sistema_id = $sistema->id;
        $pericia->save();

        return redirect()->route('sistemas.pericias.index', $sistema->id)
                         ->with('success', 'Perícia criada com sucesso!');
    }

    /**
     * Exibe o formulário para editar uma perícia.
     * @param Sistema $sistema
     * @param Pericia $pericia
     * @return \Illuminate\View\View
     */
    public function edit(Sistema $sistema, Pericia $pericia)
    {
        // O Route Model Binding garante que $pericia pertence a $sistema, mas uma verificação extra é sempre boa.
        if ($pericia->sistema_id !== $sistema->id) {
            abort(404, 'Perícia não pertence a este sistema.');
        }

        return view('sistemas.pericias.edit', compact('sistema', 'pericia'));
    }

    /**
     * Atualiza a perícia no banco de dados.
     * @param Request $request
     * @param Sistema $sistema
     * @param Pericia $pericia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sistema $sistema, Pericia $pericia)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:pericias,nome,' . $pericia->id . ',id,sistema_id,' . $sistema->id,
            'atributo_relacionado' => 'required|string|max:50',
            'descricao' => 'nullable|string',
        ]);

        $pericia->nome = $request->nome;
        $pericia->descricao = $request->descricao;
        $pericia->atributo_relacionado = $request->atributo_relacionado;
        $pericia->save();

        return redirect()->route('sistemas.pericias.index', $sistema->id)
                         ->with('success', 'Perícia atualizada com sucesso!');
    }

    /**
     * Deleta uma perícia.
     * @param Sistema $sistema
     * @param Pericia $pericia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Sistema $sistema, Pericia $pericia)
    {
        if ($pericia->sistema_id !== $sistema->id) {
            abort(404, 'Perícia não pertence a este sistema.');
        }

        $pericia->delete();

        return redirect()->route('sistemas.pericias.index', $sistema->id)
                         ->with('success', 'Perícia deletada com sucesso!');
    }
}
