<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Importação necessária para manipular arquivos

class PersonagemController extends Controller
{
    /**
     * Exibe uma listagem do recurso (Personagens).
     */
    public function index()
    {
        // Exemplo: Buscar personagens do usuário logado e paginar
        $personagens = Personagem::with(['campanha', 'raca', 'classe'])
                                 ->where('user_id', Auth::id())
                                 ->orderBy('nome')
                                 ->paginate(15);

        return view('personagens.index', compact('personagens'));
    }

    /**
     * Mostra o formulário para criar um novo recurso.
     */
    public function create()
    {
        // Você precisará passar dados auxiliares para a view (raças, classes, etc.)
        return view('personagens.create');
    }

    /**
     * Armazena um recurso recém-criado no armazenamento.
     * Esta função será usada se o processo de criação for em uma única página.
     * Se você usa createStep1/storeStep1, esta função deve ser adaptada.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'campanha_id' => ['required', 'exists:campanhas,id'],
            'raca_id' => ['nullable', 'exists:racas,id'],
            'classe_id' => ['nullable', 'exists:classes,id'],
            'origem_id' => ['nullable', 'exists:origens,id'],
            'sistema_id' => ['nullable', 'exists:sistemas,id'],
            'nivel' => ['required', 'integer', 'min:1'],
            'xp' => ['required', 'integer', 'min:0'],
            'bonus_proficiencia' => ['required', 'integer', 'min:1'],
            'sanidade' => ['nullable', 'integer', 'min:0'],
            'sorte' => ['nullable', 'integer', 'min:0'],
            'atributos' => ['nullable', 'json'],
            'descricao' => ['nullable', 'string'],
            'historia' => ['nullable', 'string'],
            'personalidade' => ['nullable', 'string'],
            'inventario' => ['nullable', 'string'],

            // NOVO: Regras de validação para o arquivo de imagem
            'imagem_upload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // max 2MB

            'ativo' => ['sometimes', 'boolean'],
            'pagina' => ['nullable', 'string', 'max:50'],
        ]);

        $imagePath = null;

        // 1. Lógica de Upload para Criação
        if ($request->hasFile('imagem_upload')) {
            // Salva o arquivo no disco 'public' dentro da pasta do usuário
            $imagePath = $request->file('imagem_upload')->store('personagens/' . Auth::id(), 'public');
        }

        $personagem = Personagem::create([
            'user_id' => Auth::id(), // Define o ID do usuário logado como proprietário
            ...$validatedData,
            'imagem' => $imagePath, // Armazena o path da imagem
            // Certifica-se de que 'atributos' é tratado como JSON se presente
            'atributos' => $request->has('atributos') ? json_decode($request->input('atributos'), true) : null,
        ]);

        return redirect()->route('personagens.show', $personagem)->with('success', 'Personagem criado com sucesso!');
    }

    /**
     * Exibe o recurso especificado.
     */
    public function show(Personagem $personagem)
    {
        // Garante que o usuário logado pode visualizar este personagem (se necessário)
        // abort_if($personagem->user_id !== Auth::id(), 403);

        $personagem->load(['user', 'campanha', 'raca', 'classe', 'origem', 'sistema']);
        return view('personagens.show', compact('personagem'));
    }

    /**
     * Mostra o formulário para editar o recurso especificado.
     */
    public function edit(Personagem $personagem)
    {
        // Garante que o usuário logado pode editar (se necessário)
        // abort_if($personagem->user_id !== Auth::id(), 403);

        return view('personagens.edit', compact('personagem'));
    }

    /**
     * Atualiza o recurso especificado no armazenamento, incluindo a imagem.
     */
    public function update(Request $request, Personagem $personagem)
    {
        // Garante que o usuário logado pode atualizar (se necessário)
        // abort_if($personagem->user_id !== Auth::id(), 403);

        $validatedData = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'campanha_id' => ['required', 'exists:campanhas,id'],
            'raca_id' => ['nullable', 'exists:racas,id'],
            'classe_id' => ['nullable', 'exists:classes,id'],
            'origem_id' => ['nullable', 'exists:origens,id'],
            'sistema_id' => ['nullable', 'exists:sistemas,id'],
            'nivel' => ['required', 'integer', 'min:1'],
            'xp' => ['required', 'integer', 'min:0'],
            'bonus_proficiencia' => ['required', 'integer', 'min:1'],
            'sanidade' => ['nullable', 'integer', 'min:0'],
            'sorte' => ['nullable', 'integer', 'min:0'],
            'atributos' => ['nullable', 'json'],
            'descricao' => ['nullable', 'string'],
            'historia' => ['nullable', 'string'],
            'personalidade' => ['nullable', 'string'],
            'inventario' => ['nullable', 'string'],

            // NOVO: A coluna 'imagem' no DB continua nullable
            'imagem' => ['nullable', 'string', 'max:255'],
            // NOVO: Adiciona a regra para o novo upload de arquivo
            'imagem_upload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            // NOVO: Campo opcional para indicar remoção da imagem
            'remove_imagem' => ['nullable', 'boolean'],

            'ativo' => ['sometimes', 'boolean'],
            'pagina' => ['nullable', 'string', 'max:50'],
        ]);

        // 2. Lógica de Manipulação da Imagem (Upload, Substituição e Remoção)
        $oldImagePath = $personagem->imagem;
        $imagePath = $oldImagePath; // Começa com o path atual

        // Caso 1: Novo upload de imagem
        if ($request->hasFile('imagem_upload')) {
            // Salva o novo arquivo
            $imagePath = $request->file('imagem_upload')->store('personagens/' . Auth::id(), 'public');

            // Deleta o arquivo antigo, se existir
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

        // Caso 2: Remoção explícita da imagem existente
        } elseif ($request->boolean('remove_imagem') && $oldImagePath) {
            // Define o path do DB como nulo
            $imagePath = null;

            // Deleta o arquivo do storage
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

        // Caso 3: Não há novo upload nem remoção, mantém o path existente
        } else {
            // Se o campo 'imagem' foi enviado no request e é nulo (e não houve remove_imagem),
            // isso geralmente significa que o campo de arquivo estava vazio e o campo de texto 'imagem' (se existisse) estava vazio.
            // Aqui, mantemos o valor de $personagem->imagem (que é $oldImagePath) se nenhum dos casos acima for verdadeiro.
            $imagePath = $oldImagePath;
        }

        // Atualiza o campo 'imagem' no validatedData com o path final
        $validatedData['imagem'] = $imagePath;

        // Remove a chave 'imagem_upload' e 'remove_imagem' antes de passar para o update
        if (isset($validatedData['imagem_upload'])) {
             unset($validatedData['imagem_upload']);
        }
        if (isset($validatedData['remove_imagem'])) {
             unset($validatedData['remove_imagem']);
        }

        // 3. Atualização dos Dados
        $personagem->update($validatedData);

        // Atualiza 'atributos' manualmente se necessário
        if ($request->has('atributos')) {
            $personagem->atributos = json_decode($request->input('atributos'), true);
            $personagem->save();
        }

        return redirect()->route('personagens.show', $personagem)->with('success', 'Personagem atualizado com sucesso!');
    }


    /**
     * Processa e armazena os dados da Etapa 1 (mantido do arquivo anterior).
     */
    public function storeStep1(Request $request)
    {
        // 1. Validação dos Dados
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

            // Regras de validação para o arquivo de imagem
            'imagem_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // max 2MB
        ]);

        // 2. Lógica de Upload da Imagem
        if ($request->hasFile('imagem_upload')) {
            $imageFile = $request->file('imagem_upload');
            // Define o path de onde a imagem será salva
            // Ex: personagens/1/imagem-personagem.png
            $path = $imageFile->store('personagens/' . Auth::id(), 'public');

            // Adiciona o path (caminho) ao validatedData para salvar no banco
            $validatedData['imagem'] = $path;
        } else {
            // Se não houver upload, garante que a coluna 'imagem' está presente como nula
            $validatedData['imagem'] = null;
        }

        // 3. Preparação dos Dados para a Próxima Etapa

        // Remove 'imagem_upload' antes de armazenar na sessão
        if (isset($validatedData['imagem_upload'])) {
             unset($validatedData['imagem_upload']);
        }

        // Adiciona o ID do usuário
        $validatedData['user_id'] = Auth::id();

        // Armazena os dados na sessão para a próxima etapa
        session(['personagem_data' => $validatedData]);

        // 4. Redirecionamento para a próxima etapa (Step 2)
        return redirect()->route('personagens.create.step2');
    }

    /**
     * Exibe o formulário da Etapa 1 (mantido do arquivo anterior).
     */
    public function createStep1()
    {
        // Lógica para recuperar $data e $campanha da sessão ou do DB para edição
        // ...

        // Exemplo de retorno, ajuste conforme sua lógica
        $data = session('personagem_data', []);
        // Simulando a recuperação da campanha
        $campanha = (object)['id' => 1, 'nome' => 'Campanha de Exemplo', 'sistema_id' => 1];

        return view('personagens.create.step1', compact('data', 'campanha'));
    }

    /**
     * Remove o recurso especificado do armazenamento.
     */
    public function destroy(Personagem $personagem)
    {
        // Garante que o usuário logado pode deletar (se necessário)
        // abort_if($personagem->user_id !== Auth::id(), 403);

        // Lógica para deletar o arquivo de imagem antes de deletar o registro no DB
        if ($personagem->imagem && Storage::disk('public')->exists($personagem->imagem)) {
            Storage::disk('public')->delete($personagem->imagem);
        }

        $personagem->delete();

        return redirect()->route('personagens.index')->with('success', 'Personagem excluído com sucesso!');
    }
}
