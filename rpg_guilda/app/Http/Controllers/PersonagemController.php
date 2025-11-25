<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonagemRequest;
use App\Models\Personagem;
use App\Models\Sistema;
use App\Models\Campanha;
use App\Models\Raca;
use App\Models\Classe;
use App\Models\Origem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonagemController extends Controller
{
    // ============================================================
    // CREATE
    // ============================================================
    public function create(Request $request)
    {
        $sistemas = Sistema::all();
        $campanhas = Campanha::where('ativo', true)->get();
        $racas = Raca::all();
        $classes = Classe::all();
        $origens = Origem::all();

        // Garante que uma campanha seja selecionada ou pega a primeira ativa
        $campanha = $request->query('campanha')
            ? Campanha::find($request->query('campanha'))
            : ($campanhas->count() ? $campanhas->first() : null);

        // O Blade depende que $campanha->sistema esteja carregado
        if ($campanha) {
            $campanha->load('sistema');
        }

        return view(
            'personagens.create',
            compact('sistemas', 'campanhas', 'racas', 'classes', 'origens', 'campanha')
        );
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(StorePersonagemRequest $request)
    {
        $data = $request->validated();

        // Convertendo campos JSON/array e novos campos do formulário
        $atributos = $data['atributos'] ?? [];
        $selected_skills = $data['selected_skills'] ?? [];
        $selected_equipment = $data['selected_equipment'] ?? [];
        $race_choices = $data['race_choices'] ?? [];
        $proficiencia_bonus = $data['proficiencia_bonus'] ?? 2; // Novo campo

        // Upload de imagem
        $imagemPath = $request->hasFile('imagem')
            ? $request->file('imagem')->store('personagens', 'public')
            : null;

        // Criação do personagem
        $personagem = Personagem::create([
            'nome'            => $data['nome'],
            'user_id'         => auth()->id(),
            'campanha_id'     => $data['campanha_id'],
            'raca_id'         => $data['raca_id'] ?? null,
            'classe_id'       => $data['classe_id'] ?? null,
            'origem_id'       => $data['origem_id'] ?? null,
            'sistema_id'      => $data['sistema_id'] ?? null,
            'atributos'       => $atributos,
            'descricao'       => $data['descricao'] ?? null,
            'historia'        => $data['historia'] ?? null,
            'personalidade'   => $data['personalidade'] ?? null,
            'inventario'      => $selected_equipment,
            'selected_skills' => $selected_skills,
            'race_choices'    => $race_choices,
            'rolled_hp'       => $data['rolled_hp'] ?? null,
            'proficiencia_bonus' => $proficiencia_bonus, // Salvando o PB
            'imagem'          => $imagemPath,
            'ativo'           => true,
        ]);

        // Redirecionamento condicional: para index ou show
        $redirectTo = $request->input('redirect_to', 'show'); // padrão: show
        if ($redirectTo === 'index') {
            return redirect()
                ->route('personagens.index')
                ->with('success', 'Personagem criado com sucesso!');
        }

        return redirect()
            ->route('personagens.show', $personagem->id)
            ->with('success', 'Personagem criado com sucesso!');
    }

    // ============================================================
    // SHOW
    // ============================================================
    public function show(Personagem $personagem)
    {
        $user = auth()->user();
        $isOwner = $personagem->user_id === ($user->id ?? null);
        $isMestre = $personagem->campanha && $personagem->campanha->criador_id === ($user->id ?? null);

        if (!$isOwner && !$isMestre) {
            abort(403, "Você não tem permissão para ver este personagem.");
        }

        return view('personagens.show', compact('personagem'));
    }

    // ============================================================
    // INDEX
    // ============================================================
    public function index()
    {
        $user = auth()->user();
        $personagens = Personagem::where('user_id', $user->id)->get();

        return view('personagens.index', compact('personagens'));
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit(Personagem $personagem)
    {
        if ($personagem->user_id !== auth()->id()) {
            abort(403);
        }

        $sistemas = Sistema::all();
        $campanhas = Campanha::all();
        $racas = Raca::all();
        $classes = Classe::all();
        $origens = Origem::all();

        return view('personagens.edit', compact(
            'personagem',
            'sistemas',
            'campanhas',
            'racas',
            'classes',
            'origens'
        ));
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== auth()->id()) {
            abort(403);
        }

        $personagem->update($request->all());

        return redirect()
            ->route('personagens.show', $personagem->id)
            ->with('success', 'Personagem atualizado!');
    }

    // ============================================================
    // DELETE
    // ============================================================
    public function destroy(Personagem $personagem)
    {
        if ($personagem->user_id !== auth()->id()) {
            abort(403);
        }

        if ($personagem->imagem) {
            Storage::disk('public')->delete($personagem->imagem);
        }

        $personagem->delete();

        return redirect()->back()->with('success', 'Personagem removido!');
    }
}
