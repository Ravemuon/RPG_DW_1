<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Pericia;
use App\Models\Raca;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Sistema;
use App\Models\Campanha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersonagemController extends Controller
{
    // Listar personagens do usuário
    public function index()
    {
        $personagens = Personagem::with('raca', 'classe', 'origem', 'sistema', 'pericias')
            ->where('user_id', Auth::id())
            ->get();

        return view('personagens.index', compact('personagens'));
    }

    // Formulário de criação filtrando pelo sistema da campanha
    public function create($campanha_id)
    {
        $campanha = Campanha::with('sistema.racas', 'sistema.classes', 'sistema.origens')->findOrFail($campanha_id);

        $racas = $campanha->sistema->racas;
        $classes = $campanha->sistema->classes;
        $origens = $campanha->sistema->origens;

        return view('personagens.create', compact('campanha', 'racas', 'classes', 'origens'));
    }

    // Salvar personagem
    public function store(Request $request)
    {
        $request->validate([
            'campanha_id' => 'required|exists:campanhas,id',
            'raca_id' => 'required|exists:racas,id',
            'classe_id' => 'required|exists:classes,id',
            'origem_id' => 'nullable|exists:origens,id',
            'nome' => 'nullable|string|max:100',
            'imagem' => 'nullable|image|max:2048',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
            'inventario' => 'nullable|string',
        ]);

        $data = $request->only([
            'campanha_id', 'raca_id', 'classe_id', 'origem_id',
            'nome', 'descricao', 'historia', 'personalidade', 'inventario'
        ]);

        $data['user_id'] = Auth::id();

        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('personagens', 'public');
        }

        $personagem = Personagem::create($data);

        // Inicializa atributos baseado em raça e classe
        $personagem->atributos = $this->calcularAtributos($personagem);
        $personagem->save();

        return redirect()->route('personagens.pericias.edit', $personagem->id)
                         ->with('success', 'Personagem criado! Agora defina as perícias.');
    }

    // Página para definir perícias
    public function editPericias(Personagem $personagem)
    {
        $this->authorize('update', $personagem);
        $pericias = Pericia::all();

        return view('personagens.pericias', compact('personagem', 'pericias'));
    }

    // Salvar perícias do personagem
    public function updatePericias(Request $request, Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $request->validate([
            'pericias' => 'nullable|array'
        ]);

        if ($request->has('pericias')) {
            $pericias = collect($request->pericias)
                ->mapWithKeys(fn($id) => [$id => ['nivel' => 1, 'proficiente' => false]]);
            $personagem->pericias()->sync($pericias);
        }

        return redirect()->route('personagens.show', $personagem->id)
                         ->with('success', 'Perícias definidas com sucesso!');
    }

    // Mostrar personagem
    public function show(Personagem $personagem)
    {
        $personagem->load('raca', 'classe', 'origem', 'sistema', 'pericias', 'user');
        return view('personagens.show', compact('personagem'));
    }

    // Edição completa do personagem
    public function edit(Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $campanha = $personagem->campanha()->with('sistema.racas', 'sistema.classes', 'sistema.origens')->first();

        $racas = $campanha->sistema->racas;
        $classes = $campanha->sistema->classes;
        $origens = $campanha->sistema->origens;

        return view('personagens.edit', compact('personagem', 'racas', 'classes', 'origens', 'campanha'));
    }

    // Atualizar personagem
    public function update(Request $request, Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $request->validate([
            'raca_id' => 'required|exists:racas,id',
            'classe_id' => 'required|exists:classes,id',
            'origem_id' => 'nullable|exists:origens,id',
            'nome' => 'nullable|string|max:100',
            'imagem' => 'nullable|image|max:2048',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
            'inventario' => 'nullable|string',
        ]);

        $data = $request->only([
            'raca_id', 'classe_id', 'origem_id',
            'nome', 'descricao', 'historia', 'personalidade', 'inventario'
        ]);

        if ($request->hasFile('imagem')) {
            if ($personagem->imagem) Storage::disk('public')->delete($personagem->imagem);
            $data['imagem'] = $request->file('imagem')->store('personagens', 'public');
        }

        $personagem->update($data);

        // Recalcula atributos se raça ou classe foram alteradas
        $personagem->atributos = $this->calcularAtributos($personagem);
        $personagem->save();

        return redirect()->route('personagens.show', $personagem->id)
                         ->with('success', 'Personagem atualizado com sucesso!');
    }

    // Deletar personagem
    public function destroy(Personagem $personagem)
    {
        $this->authorize('delete', $personagem);

        if ($personagem->imagem) Storage::disk('public')->delete($personagem->imagem);

        $personagem->delete();

        return redirect()->route('personagens.index')
                         ->with('success', 'Personagem deletado com sucesso!');
    }

    // Calcular atributos iniciais baseados em raça e classe
    private function calcularAtributos(Personagem $personagem)
    {
        $atributos = [
            'forca' => 10,
            'destreza' => 10,
            'constituicao' => 10,
            'inteligencia' => 10,
            'sabedoria' => 10,
            'carisma' => 10,
        ];

        if ($personagem->raca) {
            foreach ($personagem->raca->atributos_bonus ?? [] as $attr => $valor) {
                $atributos[$attr] += $valor;
            }
        }

        if ($personagem->classe) {
            foreach ($personagem->classe->atributos_bonus ?? [] as $attr => $valor) {
                $atributos[$attr] += $valor;
            }
        }

        return $atributos;
    }
}
