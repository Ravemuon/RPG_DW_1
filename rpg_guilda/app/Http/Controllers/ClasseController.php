<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classe;
use App\Models\Sistema;

class ClasseController extends Controller
{
    /**
     * Listar todas as classes de um sistema
     */
    public function index($sistemaId)
    {
        $sistema = Sistema::with('classes')->findOrFail($sistemaId);
        return view('sistemas.classes.index', compact('sistema'));
    }

    /**
     * Exibir formulário de criação de classe
     */
    public function create($sistemaId)
    {
        $sistema = Sistema::findOrFail($sistemaId);
        return view('sistemas.classes.create', compact('sistema'));
    }

    /**
     * Armazenar nova classe
     */
    public function store(Request $request, $sistemaId)
    {
        $request->validate([
            'nome' => 'required|unique:classes,nome,NULL,id,sistema_id,' . $sistemaId,
            'descricao' => 'nullable|string',
            'forca' => 'nullable|integer',
            'destreza' => 'nullable|integer',
            'constituicao' => 'nullable|integer',
            'inteligencia' => 'nullable|integer',
            'sabedoria' => 'nullable|integer',
            'carisma' => 'nullable|integer',
            'agilidade' => 'nullable|integer',
            'intelecto' => 'nullable|integer',
            'presenca' => 'nullable|integer',
            'vigor' => 'nullable|integer',
            'nex' => 'nullable|integer',
            'sanidade' => 'nullable|integer',
            'forca_cth' => 'nullable|integer',
            'destreza_cth' => 'nullable|integer',
            'poder' => 'nullable|integer',
            'constituicao_cth' => 'nullable|integer',
            'aparencia' => 'nullable|integer',
            'educacao' => 'nullable|integer',
            'tamanho' => 'nullable|integer',
            'inteligencia_cth' => 'nullable|integer',
            'sanidade_cth' => 'nullable|integer',
            'pontos_vida' => 'nullable|integer',
            'aspects' => 'nullable|json',
            'stunts' => 'nullable|json',
            'fate_points' => 'nullable|integer',
            'atributos_custom' => 'nullable|json',
            'poderes' => 'nullable|json',
        ]);

        Classe::create([
            'nome' => $request->nome,
            'sistema_id' => $sistemaId,
            'descricao' => $request->descricao,
            'forca' => $request->forca ?? 0,
            'destreza' => $request->destreza ?? 0,
            'constituicao' => $request->constituicao ?? 0,
            'inteligencia' => $request->inteligencia ?? 0,
            'sabedoria' => $request->sabedoria ?? 0,
            'carisma' => $request->carisma ?? 0,
            'agilidade' => $request->agilidade ?? 0,
            'intelecto' => $request->intelecto ?? 0,
            'presenca' => $request->presenca ?? 0,
            'vigor' => $request->vigor ?? 0,
            'nex' => $request->nex ?? 0,
            'sanidade' => $request->sanidade ?? 0,
            'forca_cth' => $request->forca_cth ?? 0,
            'destreza_cth' => $request->destreza_cth ?? 0,
            'poder' => $request->poder ?? 0,
            'constituicao_cth' => $request->constituicao_cth ?? 0,
            'aparencia' => $request->aparencia ?? 0,
            'educacao' => $request->educacao ?? 0,
            'tamanho' => $request->tamanho ?? 0,
            'inteligencia_cth' => $request->inteligencia_cth ?? 0,
            'sanidade_cth' => $request->sanidade_cth ?? 0,
            'pontos_vida' => $request->pontos_vida ?? 0,
            'aspects' => $request->aspects,
            'stunts' => $request->stunts,
            'fate_points' => $request->fate_points ?? 0,
            'atributos_custom' => $request->atributos_custom,
            'poderes' => $request->poderes,
        ]);

        return redirect()->route('sistemas.classes.index', $sistemaId)
                         ->with('success', 'Classe criada com sucesso!');
    }

    /**
     * Exibir detalhes de uma classe
     */
    public function show($sistemaId, Classe $classe)
    {
        return view('sistemas.classes.show', compact('classe'));
    }

    /**
     * Exibir formulário de edição
     */
    public function edit($sistemaId, Classe $classe)
    {
        return view('sistemas.classes.edit', compact('classe', 'sistemaId'));
    }

    /**
     * Atualizar classe existente
     */
    public function update(Request $request, $sistemaId, Classe $classe)
    {
        $request->validate([
            'nome' => 'required|unique:classes,nome,' . $classe->id . ',id,sistema_id,' . $sistemaId,
            'descricao' => 'nullable|string',
            'forca' => 'nullable|integer',
            'destreza' => 'nullable|integer',
            'constituicao' => 'nullable|integer',
            'inteligencia' => 'nullable|integer',
            'sabedoria' => 'nullable|integer',
            'carisma' => 'nullable|integer',
            'agilidade' => 'nullable|integer',
            'intelecto' => 'nullable|integer',
            'presenca' => 'nullable|integer',
            'vigor' => 'nullable|integer',
            'nex' => 'nullable|integer',
            'sanidade' => 'nullable|integer',
            'forca_cth' => 'nullable|integer',
            'destreza_cth' => 'nullable|integer',
            'poder' => 'nullable|integer',
            'constituicao_cth' => 'nullable|integer',
            'aparencia' => 'nullable|integer',
            'educacao' => 'nullable|integer',
            'tamanho' => 'nullable|integer',
            'inteligencia_cth' => 'nullable|integer',
            'sanidade_cth' => 'nullable|integer',
            'pontos_vida' => 'nullable|integer',
            'aspects' => 'nullable|json',
            'stunts' => 'nullable|json',
            'fate_points' => 'nullable|integer',
            'atributos_custom' => 'nullable|json',
            'poderes' => 'nullable|json',
        ]);

        $classe->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'forca' => $request->forca ?? 0,
            'destreza' => $request->destreza ?? 0,
            'constituicao' => $request->constituicao ?? 0,
            'inteligencia' => $request->inteligencia ?? 0,
            'sabedoria' => $request->sabedoria ?? 0,
            'carisma' => $request->carisma ?? 0,
            'agilidade' => $request->agilidade ?? 0,
            'intelecto' => $request->intelecto ?? 0,
            'presenca' => $request->presenca ?? 0,
            'vigor' => $request->vigor ?? 0,
            'nex' => $request->nex ?? 0,
            'sanidade' => $request->sanidade ?? 0,
            'forca_cth' => $request->forca_cth ?? 0,
            'destreza_cth' => $request->destreza_cth ?? 0,
            'poder' => $request->poder ?? 0,
            'constituicao_cth' => $request->constituicao_cth ?? 0,
            'aparencia' => $request->aparencia ?? 0,
            'educacao' => $request->educacao ?? 0,
            'tamanho' => $request->tamanho ?? 0,
            'inteligencia_cth' => $request->inteligencia_cth ?? 0,
            'sanidade_cth' => $request->sanidade_cth ?? 0,
            'pontos_vida' => $request->pontos_vida ?? 0,
            'aspects' => $request->aspects,
            'stunts' => $request->stunts,
            'fate_points' => $request->fate_points ?? 0,
            'atributos_custom' => $request->atributos_custom,
            'poderes' => $request->poderes,
        ]);

        return redirect()->route('sistemas.classes.index', $classe->sistema_id)
                         ->with('success', 'Classe atualizada com sucesso!');
    }

    /**
     * Remover uma classe
     */
    public function destroy(Classe $classe)
    {
        $classe->delete();

        return redirect()->route('sistemas.classes.index', $classe->sistema_id)
                         ->with('success', 'Classe removida com sucesso!');
    }
}
