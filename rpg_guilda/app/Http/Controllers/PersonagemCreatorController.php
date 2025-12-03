<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Campanha;
use App\Models\Raca;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
// Assuma que este FormRequest existe para o Passo 1
use App\Http\Requests\StoreStep1Request;

class PersonagemCreatorController extends Controller
{
    // Chave de sessão para armazenar os dados temporários
    const SESSION_KEY = 'personagem_data';

    // Lista de atributos padrão (Fallback)
    const ATRIBUTOS_PADRAO = ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];

    // ==============================================
    // MÉTODOS DE UTILIDADES INTERNOS
    // ==============================================

    protected function getStep1Redirect($campanhaId = null)
    {
        if (!$campanhaId) {
            $campanhaId = Session::get(self::SESSION_KEY)['campanha_id'] ?? null;
        }

        if ($campanhaId) {
            return redirect()->route('personagens.create', ['campanha' => $campanhaId])
                ->with('error', 'O processo de criação expirou ou foi interrompido. Por favor, comece novamente.');
        }

        return redirect()->route('campanhas.index')
            ->with('error', 'Processo de criação não iniciado. Selecione uma campanha para começar.');
    }

    protected function checkSession()
    {
        if (!Session::has(self::SESSION_KEY) || !is_array(Session::get(self::SESSION_KEY))) {
            return $this->getStep1Redirect();
        }

        return Session::get(self::SESSION_KEY);
    }

    protected function clearSession()
    {
        Session::forget(self::SESSION_KEY);
    }

    protected function getBaseAttributes(int $sistemaId): array
    {
        $sistema = Sistema::find($sistemaId);

        if ($sistema && method_exists($sistema, 'getAtributosBase')) {
            return $sistema->getAtributosBase();
        }

        return self::ATRIBUTOS_PADRAO;
    }

    private function calcularProgresso(Personagem $personagem)
    {
        $basico = !empty($personagem->nome) && !empty($personagem->campanha_id) && !empty($personagem->nivel);
        $raca_classe = !empty($personagem->raca_id) && !empty($personagem->classe_id) && !empty($personagem->bonus_proficiencia);
        $atributos = !empty($personagem->atributos) && count(json_decode($personagem->atributos ?? '{}', true)) > 0;
        // Assume-se que 'vida' é o campo principal
        $vida_sanidade_sorte = !empty($personagem->vida);
        $inventario = true;

        $etapas = [$basico, $raca_classe, $atributos, $vida_sanidade_sorte];
        $completas = count(array_filter($etapas));
        $total_etapas = count($etapas);
        $porcentagem = ($completas / $total_etapas) * 100;
        $completo = $completas === $total_etapas;

        return [
            'basico' => $basico,
            'raca_classe' => $raca_classe,
            'atributos' => $atributos,
            'vida_sanidade_sorte' => $vida_sanidade_sorte,
            'inventario' => $inventario,
            'completo' => $completo,
            'porcentagem' => round($porcentagem),
        ];
    }

    // ==============================================
    // FLUXO DE CRIAÇÃO (CREATE - SESSION BASED)
    // ==============================================

    public function create(Request $request)
    {
        $campanhaIdFromQuery = $request->query('campanha');
        $personagemData = Session::get(self::SESSION_KEY, []);

        $campanhaId = $campanhaIdFromQuery ?? $personagemData['campanha_id'] ?? null;

        if (!$campanhaId || !($campanha = Campanha::with('sistema')->find($campanhaId))) {
            return $this->getStep1Redirect($campanhaIdFromQuery);
        }

        // Inicializa dados na sessão
        if (empty($personagemData['campanha_id']) || $personagemData['campanha_id'] !== $campanha->id) {
            $personagemData = array_merge([
                'campanha_id' => $campanha->id,
                'sistema_id' => $campanha->sistema_id,
                'nivel' => 1,
                'xp' => 0,
                'ativo' => true,
                'bonus_proficiencia' => 2, // Default para D&D 5e (Nível 1)
            ], $personagemData);
            Session::put(self::SESSION_KEY, $personagemData);
        }

        return view('personagens.create.step1', [
            'campanha' => $campanha,
            'data' => $personagemData,
        ]);
    }

    public function storeStep1(StoreStep1Request $request)
    {
        $validatedData = $request->validated();
        $personagemData = Session::get(self::SESSION_KEY, []);

        // 1. Processar upload de imagem temporária
        if ($request->hasFile('imagem_file')) {
            if (!empty($personagemData['imagem_temp_path'])) {
                Storage::disk('public')->delete($personagemData['imagem_temp_path']);
            }
            // Armazenar o arquivo em uma pasta temporária (ex: 'temp')
            $path = $request->file('imagem_file')->store('temp', 'public');
            $validatedData['imagem_temp_path'] = $path;
        } else {
            $validatedData['imagem_temp_path'] = $personagemData['imagem_temp_path'] ?? null;
        }

        // 2. Mesclar e Salvar na Sessão
        $personagemData = array_merge($personagemData, $validatedData);
        $personagemData['sistema_id'] = $personagemData['sistema_id'] ?? $request->input('sistema_id');
        $personagemData['campanha_id'] = $personagemData['campanha_id'] ?? $request->input('campanha_id');

        Session::put(self::SESSION_KEY, $personagemData);

        // 3. Redirecionar para o próximo passo
        return redirect()->route('personagens.step2');
    }

    public function step2()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        if (empty($sessionData['nome'])) {
            return redirect()->route('personagens.create', ['campanha' => $sessionData['campanha_id']])
                ->with('warning', 'Complete o nome do personagem (Passo 1) primeiro.');
        }

        $campanha = Campanha::with('sistema')->find($sessionData['campanha_id']);
        $racas = Raca::where('sistema_id', $sessionData['sistema_id'])->orderBy('nome')->get();
        $classes = Classe::where('sistema_id', $sessionData['sistema_id'])->orderBy('nome')->get();
        $origens = Origem::where('sistema_id', $sessionData['sistema_id'])->orderBy('nome')->get();

        return view('personagens.create.step2', compact('sessionData', 'campanha', 'racas', 'classes', 'origens'));
    }

    public function storeStep2(StoreStep2Request $request, Personagem $personagem)
    {
        // Os dados já foram validados pelo StoreStep2Request.

        $validated = $request->validated();

        // Os IDs são diretamente atualizados
        $personagem->raca_id = $validated['raca_id'];
        $personagem->classe_id = $validated['classe_id'];
        $personagem->origem_id = $validated['origem_id'];

        // O campo de perícias precisa ser salvo como JSON ou processado.
        // Se a coluna 'pericias_selecionadas' no seu DB for um JSON/TEXT:
        $personagem->pericias_selecionadas = $validated['pericias_classe_selecionadas'];

        // Se você precisar do array PHP:
        $periciasArray = json_decode($validated['pericias_classe_selecionadas'], true);
        // ... faça o processamento de salvar perícias na tabela pivot ou outro local ...

        $personagem->save();

        // Redireciona para o próximo passo
        return redirect()->route('personagens.create.step3', $personagem->id)
                        ->with('success', 'Raça, Classe e Origem salvas com sucesso! Prossiga para o próximo passo.');
    }

    public function step3()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        if (empty($sessionData['bonus_proficiencia'])) {
            return redirect()->route('personagens.step2')->with('error', 'Por favor, complete o Passo 2 primeiro.');
        }

        $atributosPadrao = $this->getBaseAttributes($sessionData['sistema_id']);

        return view('personagens.create.step3', [
            'data' => $sessionData,
            'atributosPadrao' => $atributosPadrao
        ]);
    }

    public function storeStep3(Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        $atributosEsperados = $this->getBaseAttributes($sessionData['sistema_id']);
        $regrasValidacao = ['atributos_pontuacoes' => ['required', 'array']];

        foreach ($atributosEsperados as $atributo) {
            $regrasValidacao["atributos_pontuacoes.{$atributo}"] = ['required', 'integer', 'min:1', 'max:20'];
        }

        $request->validate($regrasValidacao);

        $atributos = $request->input('atributos_pontuacoes');

        if (count($atributos) !== count($atributosEsperados)) {
            throw ValidationException::withMessages(['atributos_pontuacoes' => 'Todos os atributos base devem ser preenchidos.']);
        }

        $personagemData = $sessionData;
        $personagemData['atributos'] = json_encode($atributos);
        Session::put(self::SESSION_KEY, $personagemData);

        return redirect()->route('personagens.step4');
    }

    public function step4()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        if (empty($sessionData['atributos'])) {
            return redirect()->route('personagens.step3')->with('error', 'Por favor, complete o Passo 3 (Atributos) primeiro.');
        }

        return view('personagens.create.step4', ['data' => $sessionData]);
    }

    public function storeStep4(Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        $validatedData = $request->validate([
            'vida' => ['required', 'integer', 'min:1'],
            'sanidade' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sorte' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $personagemData = array_merge($sessionData, $validatedData);
        Session::put(self::SESSION_KEY, $personagemData);

        return redirect()->route('personagens.step5');
    }

    public function step5()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        if (!isset($sessionData['vida'])) {
            return redirect()->route('personagens.step4')->with('error', 'Por favor, complete o Passo 4 (Vida) primeiro.');
        }

        return view('personagens.create.step5', ['data' => $sessionData]);
    }

    public function storeStep5(Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        $validatedData = $request->validate([
            'inventario' => ['nullable', 'string'],
            'equipamento' => ['nullable', 'string'],
        ]);

        $personagemData = array_merge($sessionData, $validatedData);
        Session::put(self::SESSION_KEY, $personagemData);

        return redirect()->route('personagens.final');
    }

    public function final()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        if (empty($sessionData['nome']) || empty($sessionData['atributos']) || empty($sessionData['vida'])) {
            return redirect()->route('personagens.create', ['campanha' => $sessionData['campanha_id']])
                ->with('error', 'Faltam dados obrigatórios para finalizar a criação.');
        }

        $campanha = Campanha::find($sessionData['campanha_id']);
        $sistema = Sistema::find($sessionData['sistema_id']);
        $raca = isset($sessionData['raca_id']) ? Raca::find($sessionData['raca_id']) : null;
        $classe = isset($sessionData['classe_id']) ? Classe::find($sessionData['classe_id']) : null;
        $origem = isset($sessionData['origem_id']) ? Origem::find($sessionData['origem_id']) : null;
        $atributos = json_decode($sessionData['atributos'] ?? '{}', true);

        return view('personagens.create.stepfinal', compact('sessionData', 'campanha', 'sistema', 'raca', 'classe', 'origem', 'atributos'));
    }

    /**
     * CORREÇÃO CRÍTICA: Garantir que 'sistema_id' seja persistido.
     */
    public function storeFinal(Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        $finalData = $sessionData;
        $finalData['user_id'] = Auth::id();

        // 1. Processar imagem temporária (se houver)
        if (isset($finalData['imagem_temp_path']) && Storage::disk('public')->exists($finalData['imagem_temp_path'])) {
            try {
                $directory = 'personagens/' . Auth::id();
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                $newFileName = time() . '-' . uniqid() . '.' . pathinfo($finalData['imagem_temp_path'], PATHINFO_EXTENSION);
                $newPath = $directory . '/' . $newFileName;

                Storage::disk('public')->move($finalData['imagem_temp_path'], $newPath);

                $finalData['imagem'] = $newPath;
            } catch (\Exception $e) {
                \Log::error('Erro ao mover imagem do personagem: ' . $e->getMessage());
                $finalData['imagem'] = null;
            }
        } else {
             $finalData['imagem'] = null;
        }

        // 2. Limpeza de campos temporários
        // Removendo apenas 'imagem_temp_path'. O 'sistema_id' é mantido.
        unset($finalData['imagem_temp_path']);

        // 3. Criação da instância no banco de dados
        try {
            $personagem = DB::transaction(function () use ($finalData) {
                if (is_string($finalData['atributos'])) {
                    $finalData['atributos'] = json_decode($finalData['atributos'], true);
                }
                return Personagem::create($finalData);
            });

            $this->clearSession();

            return redirect()->route('personagens.show', $personagem)
                ->with('success', 'Personagem criado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao salvar personagem no criador: ' . $e->getMessage(), $finalData ?? []);

            // Tenta limpar arquivos temporários se houve falha no DB
            if (isset($sessionData['imagem_temp_path'])) {
                 Storage::disk('public')->delete($sessionData['imagem_temp_path']);
            }

            return back()->with('error', 'Falha ao salvar o personagem. Por favor, revise os dados.');
        }
    }

    // ==============================================
    // FLUXO DE EDIÇÃO (EDIT - DATABASE BASED)
    // ==============================================

    public function overview(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $progresso = $this->calcularProgresso($personagem);
        return view('personagens.overview', compact('personagem', 'progresso'));
    }

    public function editStep1(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.step1', compact('personagem'));
    }

    /**
     * Atualização da Etapa 1 - Coração do Personagem (Lógica de imagem padronizada)
     */
    public function updateStep1(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $validatedData = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'nivel' => ['required', 'integer', 'min:1', 'max:20'],
            'xp' => ['required', 'integer', 'min:0'],
            'descricao' => ['nullable', 'string'],
            'historia' => ['nullable', 'string'],
            'personalidade' => ['nullable', 'string'],
            'pagina' => ['nullable', 'string', 'max:50'],
            'ativo_checkbox_only' => ['sometimes', 'accepted'],
            'imagem_upload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Adicionei mimes
            'remove_imagem' => ['nullable', 'boolean'],
        ]);

        $validatedData['ativo'] = $request->has('ativo_checkbox_only');
        unset($validatedData['ativo_checkbox_only']);

        $oldImagePath = $personagem->imagem;
        $imagePath = $oldImagePath;

        // 1. Novo upload de imagem
        if ($request->hasFile('imagem_upload')) {
            // Deleta o arquivo antigo, se existir
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
            // Salva o novo arquivo no diretório do usuário
            $imagePath = $request->file('imagem_upload')->store('personagens/' . Auth::id(), 'public');

        // 2. Remoção explícita da imagem existente
        } elseif ($request->boolean('remove_imagem') && $oldImagePath) {
            $imagePath = null;
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        $personagem->update(array_merge($validatedData, [
            'imagem' => $imagePath,
        ]));

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Dados básicos atualizados com sucesso!');
    }

    public function editStep2(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $sistema = $personagem->campanha->sistema;
        $racas = Raca::where('sistema_id', $sistema->id)->orderBy('nome')->get();
        $classes = Classe::where('sistema_id', $sistema->id)->orderBy('nome')->get();
        $origens = Origem::where('sistema_id', $sistema->id)->orderBy('nome')->get();

        return view('personagens.edit.step2', compact('personagem', 'racas', 'classes', 'origens'));
    }

    public function updateStep2(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $validatedData = $request->validate([
            'raca_id' => ['nullable', 'exists:racas,id'],
            'classe_id' => ['nullable', 'exists:classes,id'],
            'origem_id' => ['nullable', 'exists:origens,id'],
            'bonus_proficiencia' => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        $personagem->update($validatedData);

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Raça, classe e origem atualizados!');
    }

    public function editStep3(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $atributosAtuais = json_decode($personagem->atributos ?? '{}', true);
        $sistema = $personagem->campanha->sistema;
        $atributosSistema = $this->getBaseAttributes($sistema->id);

        return view('personagens.edit.step3', compact('personagem', 'atributosAtuais', 'atributosSistema'));
    }

    public function updateStep3(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $sistema = $personagem->campanha->sistema;
        $atributosEsperados = $this->getBaseAttributes($sistema->id);

        $regrasValidacao = ['atributos' => ['required', 'array']];
        foreach ($atributosEsperados as $atributo) {
            $regrasValidacao["atributos.{$atributo}"] = ['required', 'integer', 'min:1', 'max:20'];
        }

        $validatedData = $request->validate($regrasValidacao);

        $personagem->atributos = json_encode($validatedData['atributos']);
        $personagem->save();

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Atributos atualizados com sucesso!');
    }

    public function editStep4(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.step4', compact('personagem'));
    }

    public function updateStep4(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $validatedData = $request->validate([
            'vida' => ['required', 'integer', 'min:1'],
            'sanidade' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sorte' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $personagem->update($validatedData);

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Pontos de vida, sanidade e sorte atualizados!');
    }

    public function editStep5(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.step5', compact('personagem'));
    }

    public function updateStep5(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $validatedData = $request->validate([
            'inventario' => ['nullable', 'string'],
            'equipamento' => ['nullable', 'string'],
        ]);

        $personagem->update($validatedData);

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Inventário e equipamento atualizados!');
    }

    public function finalPersonagem(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $progresso = $this->calcularProgresso($personagem);

        if (!$progresso['completo']) {
            return redirect()->route('personagens.overview', $personagem->id)
                ->with('warning', 'Complete todas as etapas antes de finalizar!');
        }

        return redirect()->route('personagens.show', $personagem)
            ->with('success', 'Personagem completo!');
    }

    // ==============================================
    // MÉTODOS DE SORTEIO (AJAX)
    // ==============================================

    public function sortearAtributos(Request $request)
    {
        if (!$request->has('sistema_id')) {
            return response()->json(['error' => 'Sistema ID necessário para sortear atributos.'], 400);
        }

        $atributosSistema = $this->getBaseAttributes($request->input('sistema_id'));
        $atributosSorteados = [];

        // Lógica de sorteio 4d6, descarta o menor
        foreach ($atributosSistema as $atributo) {
            $dados = [];
            for ($i = 0; $i < 4; $i++) {
                $dados[] = rand(1, 6);
            }

            sort($dados);
            array_shift($dados);

            $atributosSorteados[$atributo] = array_sum($dados);
        }

        return response()->json(['atributos' => $atributosSorteados]);
    }

    public function sortearVida(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $classe = $personagem->classe;
        // Se a classe ou dado de vida não estiver definido, usa um valor padrão (e.g., d8)
        $dadoVida = $classe->dado_vida ?? 8;
        $nivel = $personagem->nivel;
        $vidaSorteada = 0;

        // Primeiro nível sempre tem o máximo de vida
        $vidaSorteada += $dadoVida;

        // Níveis subsequentes são sorteados
        for ($i = 1; $i < $nivel; $i++) {
            $vidaSorteada += rand(1, $dadoVida);
        }

        // Adiciona modificador de constituição
        $atributos = json_decode($personagem->atributos, true);

        // Calcula modificador e garante um valor padrão de 0 se o atributo não estiver configurado
        $conScore = $atributos['constituicao'] ?? 10;
        $modConstituicao = floor(($conScore - 10) / 2);

        $vidaSorteada += ($modConstituicao * $nivel);

        // Garante que a vida mínima seja 1
        return response()->json(['vida' => max(1, $vidaSorteada)]);
    }

    // ==============================================
    // PERÍCIAS
    // ==============================================

    public function editPericias(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.pericias', compact('personagem'));
    }

    public function updatePericias(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $validatedData = $request->validate([
            'pericias' => ['nullable', 'array'],
        ]);

        $personagem->pericias = json_encode($validatedData['pericias'] ?? []);
        $personagem->save();

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Perícias atualizadas!');
    }
}
