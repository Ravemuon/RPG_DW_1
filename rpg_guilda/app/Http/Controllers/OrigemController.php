<?php

namespace App\Http\Controllers;

use App\Models\Origem;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrigemController extends Controller
{
    /**
     * Lista todas as origens de um sistema.
     */
    public function index(Sistema $sistema)
    {
        $origens = $sistema->origens()->get();

        return view('origens.index', compact('sistema', 'origens'));
    }

    /**
     * Formulário para criar nova origem.
     */
    public function create(Sistema $sistema)
    {
        return view('origens.create', compact('sistema'));
    }

    /**
     * Salva uma nova origem.
     */
    public function store(Request $request, Sistema $sistema)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255|unique:origens,nome,NULL,id,sistema_id,' . $sistema->id,
                'descricao' => 'nullable|string',
                'pagina' => 'nullable|string|max:50',

                'bonus_pericias' => 'nullable|json',
                'recursos_adicionais' => 'nullable|json',
            ]);

            // Decodifica JSON caso venha como string
            if (isset($validated['bonus_pericias']) && is_string($validated['bonus_pericias'])) {
                $validated['bonus_pericias'] = json_decode($validated['bonus_pericias'], true);
            }

            if (isset($validated['recursos_adicionais']) && is_string($validated['recursos_adicionais'])) {
                $validated['recursos_adicionais'] = json_decode($validated['recursos_adicionais'], true);
            }

            $validated['sistema_id'] = $sistema->id;

            Origem::create($validated);

            return redirect()
                ->route('sistemas.origens.index', $sistema)
                ->with('success', 'Origem criada com sucesso!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Exibe uma origem.
     */
    public function show(Sistema $sistema, Origem $origem)
    {
        return view('origens.show', compact('sistema', 'origem'));
    }

    /**
     * Formulário de edição.
     */
    public function edit(Sistema $sistema, Origem $origem)
    {
        return view('origens.edit', compact('sistema', 'origem'));
    }

    /**
     * Atualiza a origem.
     */
    public function update(Request $request, Sistema $sistema, Origem $origem)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255|unique:origens,nome,' . $origem->id . ',id,sistema_id,' . $sistema->id,
                'descricao' => 'nullable|string',
                'pagina' => 'nullable|string|max:50',

                'bonus_pericias' => 'nullable|json',
                'recursos_adicionais' => 'nullable|json',
            ]);

            if (isset($validated['bonus_pericias']) && is_string($validated['bonus_pericias'])) {
                $validated['bonus_pericias'] = json_decode($validated['bonus_pericias'], true);
            }

            if (isset($validated['recursos_adicionais']) && is_string($validated['recursos_adicionais'])) {
                $validated['recursos_adicionais'] = json_decode($validated['recursos_adicionais'], true);
            }

            $origem->update($validated);

            return redirect()
                ->route('sistemas.origens.index', $sistema)
                ->with('success', 'Origem atualizada com sucesso!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove uma origem.
     */
    public function destroy(Sistema $sistema, Origem $origem)
    {
        $origem->delete();

        return redirect()
            ->route('sistemas.origens.index', $sistema)
            ->with('success', 'Origem excluída com sucesso!');
    }
}
