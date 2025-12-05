<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersonagemController extends Controller
{
    // Lista personagens do usuário logado
    public function index()
    {
        $personagens = Personagem::with(['campanha', 'raca', 'classe'])
            ->where('user_id', Auth::id())
            ->orderBy('nome')
            ->paginate(15);

        return view('personagens.index', compact('personagens'));
    }

    // Formulário de criação
    public function create()
    {
        return view('personagens.create');
    }

    // Criação de personagem (single-step)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|exists:campanhas,id',
            'raca_id' => 'nullable|exists:racas,id',
            'classe_id' => 'nullable|exists:classes,id',
            'origem_id' => 'nullable|exists:origens,id',
            'sistema_id' => 'nullable|exists:sistemas,id',
            'nivel' => 'required|integer|min:1',
            'xp' => 'required|integer|min:0',
            'bonus_proficiencia' => 'required|integer|min:1',
            'sanidade' => 'nullable|integer|min:0',
            'sorte' => 'nullable|integer|min:0',
            'atributos' => 'nullable|json',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
            'inventario' => 'nullable|string',
            'imagem_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'ativo' => 'sometimes|boolean',
            'pagina' => 'nullable|string|max:50',
        ]);

        // Upload de imagem
        $imagePath = $request->hasFile('imagem_upload')
            ? $request->file('imagem_upload')->store('personagens/' . Auth::id(), 'public')
            : null;

        $personagem = Personagem::create([
            'user_id' => Auth::id(),
            ...$validatedData,
            'imagem' => $imagePath,
            'atributos' => $request->has('atributos') ? json_decode($request->input('atributos'), true) : null,
        ]);

        return redirect()->route('personagens.show', $personagem)
                         ->with('success', 'Personagem criado com sucesso!');
    }

    // Visualização de personagem
    public function show(Personagem $personagem)
    {
        $personagem->load(['user', 'campanha', 'raca', 'classe', 'origem', 'sistema']);
        return view('personagens.show', compact('personagem'));
    }

    // Formulário de edição
    public function edit(Personagem $personagem)
    {
        return view('personagens.edit', compact('personagem'));
    }

    // Atualização de personagem, incluindo imagem
    public function update(Request $request, Personagem $personagem)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|exists:campanhas,id',
            'raca_id' => 'nullable|exists:racas,id',
            'classe_id' => 'nullable|exists:classes,id',
            'origem_id' => 'nullable|exists:origens,id',
            'sistema_id' => 'nullable|exists:sistemas,id',
            'nivel' => 'required|integer|min:1',
            'xp' => 'required|integer|min:0',
            'bonus_proficiencia' => 'required|integer|min:1',
            'sanidade' => 'nullable|integer|min:0',
            'sorte' => 'nullable|integer|min:0',
            'atributos' => 'nullable|json',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
            'inventario' => 'nullable|string',
            'imagem_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_imagem' => 'nullable|boolean',
            'ativo' => 'sometimes|boolean',
            'pagina' => 'nullable|string|max:50',
        ]);

        $oldImagePath = $personagem->imagem;
        $imagePath = $oldImagePath;

        if ($request->hasFile('imagem_upload')) {
            $imagePath = $request->file('imagem_upload')->store('personagens/' . Auth::id(), 'public');
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } elseif ($request->boolean('remove_imagem') && $oldImagePath) {
            $imagePath = null;
            Storage::disk('public')->delete($oldImagePath);
        }

        $validatedData['imagem'] = $imagePath;
        unset($validatedData['imagem_upload'], $validatedData['remove_imagem']);

        $personagem->update($validatedData);

        if ($request->has('atributos')) {
            $personagem->atributos = json_decode($request->input('atributos'), true);
            $personagem->save();
        }

        return redirect()->route('personagens.show', $personagem)
                         ->with('success', 'Personagem atualizado com sucesso!');
    }

    // Etapa 1 da criação de personagem
    public function storeStep1(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:100',
            'nivel' => 'required|integer|min:1|max:20',
            'xp' => 'required|integer|min:0',
            'campanha_id' => 'required|exists:campanhas,id',
            'sistema_id' => 'required|exists:sistemas,id',
            'descricao' => 'nullable|string|max:1000',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string|max:1000',
            'pagina' => 'nullable|string|max:50',
            'ativo' => 'boolean',
            'imagem_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('imagem_upload')) {
            $validatedData['imagem'] = $request->file('imagem_upload')->store('personagens/' . Auth::id(), 'public');
        } else {
            $validatedData['imagem'] = null;
        }

        unset($validatedData['imagem_upload']);
        $validatedData['user_id'] = Auth::id();
        session(['personagem_data' => $validatedData]);

        return redirect()->route('personagens.create.step2');
    }

    public function createStep1()
    {
        $data = session('personagem_data', []);
        $campanha = (object)['id' => 1, 'nome' => 'Campanha de Exemplo', 'sistema_id' => 1];
        return view('personagens.create.step1', compact('data', 'campanha'));
    }

    // Exclusão de personagem, incluindo arquivo de imagem
    public function destroy(Personagem $personagem)
    {
        if ($personagem->imagem && Storage::disk('public')->exists($personagem->imagem)) {
            Storage::disk('public')->delete($personagem->imagem);
        }

        $personagem->delete();

        return redirect()->route('personagens.index')
                         ->with('success', 'Personagem excluído com sucesso!');
    }
}
