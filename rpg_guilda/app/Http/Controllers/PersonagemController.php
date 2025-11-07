<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Personagem;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Pericia;
use App\Models\Campanha;
use App\Models\Raca;

class PersonagemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ===================================================
    // 🔹 Lista personagens do usuário logado
    // ===================================================
    public function index()
    {
        $user = Auth::user();

        $personagens = Personagem::with(['classeObj', 'origens', 'pericias', 'campanha', 'raca'])
            ->where('user_id', $user->id)
            ->get();

        return view('personagens.index', compact('personagens'));
    }

    // ===================================================
    // 🔹 Formulário de criação de personagem
    // ===================================================
    public function create()
    {
        $campanhas = Campanha::where('criador_id', Auth::id())
            ->orWhereHas('jogadores', fn($q) => $q->where('user_id', Auth::id()))
            ->get();

        $classes = Classe::all();
        $origens = Origem::all();
        $racas   = Raca::all();

        return view('personagens.create', compact('campanhas', 'classes', 'origens', 'racas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'classe' => 'nullable|string|exists:classes,nome',
            'campanha_id' => 'required|exists:campanhas,id',
            'raca_id' => 'nullable|exists:racas,id',
            'origens' => 'nullable|array',
            'origens.*' => 'exists:origens,id',
            'descricao' => 'nullable|string',
        ]);

        $personagem = Personagem::create([
            'nome' => $request->nome,
            'classe' => $request->classe,
            'campanha_id' => $request->campanha_id,
            'user_id' => Auth::id(),
            'raca_id' => $request->raca_id,
            'descricao' => $request->descricao,
            'sistema_rpg' => $request->classe ? Classe::where('nome', $request->classe)->first()->sistemaRPG : null,
            'ativo' => true,
        ]);

        if ($request->origens) {
            $personagem->origens()->attach($request->origens);
        }

        $personagem->inicializarAtributos();

        if ($personagem->classeObj && $personagem->classeObj->pericias()->exists()) {
            foreach ($personagem->classeObj->pericias()->get() as $pericia) {
                $personagem->pericias()->attach($pericia->id, ['valor' => null, 'definida' => false]);
            }
        }

        return redirect()->route('personagens.index')
            ->with('success', 'Personagem criado com sucesso!');
    }

    // ===================================================
    // 🔹 Mostrar personagem (dono ou mestre)
    // ===================================================
    public function show(Personagem $personagem)
    {
        $user = Auth::user();

        // Carregar relacionamentos importantes
        $personagem->load(['campanha', 'classeObj', 'origens', 'pericias', 'raca']);

        if (!$personagem->campanha) {
            abort(404, 'Campanha do personagem não encontrada.');
        }

        // Apenas dono ou mestre da campanha podem acessar
        if ($personagem->user_id !== $user->id && $user->id !== $personagem->campanha->criador_id) {
            abort(403, 'Acesso negado.');
        }

        return view('personagens.show', compact('personagem'));
    }

    // ===================================================
    // 🔹 Formulário de edição
    // ===================================================
    public function edit(Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $campanhas = Campanha::where('criador_id', Auth::id())
            ->orWhereHas('jogadores', fn($q) => $q->where('user_id', Auth::id()))
            ->get();

        $classes = Classe::all();
        $origens = Origem::all();
        $racas   = Raca::all();

        $personagem->load(['origens', 'pericias', 'raca']);

        return view('personagens.edit', compact('personagem', 'campanhas', 'classes', 'origens', 'racas'));
    }

    // ===================================================
    // 🔹 Atualizar personagem
    // ===================================================
    public function update(Request $request, Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $request->validate([
            'nome' => 'required|string|max:100',
            'classe' => 'nullable|string|exists:classes,nome',
            'campanha_id' => 'required|exists:campanhas,id',
            'raca_id' => 'nullable|exists:racas,id',
            'origens' => 'nullable|array',
            'origens.*' => 'exists:origens,id',
            'descricao' => 'nullable|string',
            'ativo' => 'nullable|boolean',
        ]);

        $personagem->nome = $request->nome;
        $personagem->classe = $request->classe;
        $personagem->campanha_id = $request->campanha_id;
        $personagem->raca_id = $request->raca_id;
        $personagem->descricao = $request->descricao;
        $personagem->ativo = $request->has('ativo');
        $personagem->sistema_rpg = $request->classe ? Classe::where('nome', $request->classe)->first()->sistemaRPG : null;

        $personagem->save();

        $personagem->origens()->sync($request->origens ?? []);
        $personagem->inicializarAtributos();

        return redirect()->route('personagens.show', $personagem->id)
            ->with('success', 'Personagem atualizado com sucesso!');
    }
}
