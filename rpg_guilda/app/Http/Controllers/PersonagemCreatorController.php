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
use App\Http\Requests\StoreStep1Request; // Assumido que este FormRequest existe

class PersonagemCreatorController extends Controller
{
    // Chave de sessão para armazenar os dados temporários
    const SESSION_KEY = 'personagem_data';

    // Lista de atributos padrão (Fallback)
    const ATRIBUTOS_PADRAO = ['forca', 'destreza', 'constituicao', 'inteligencia', 'sabedoria', 'carisma'];

    // ==============================================
    // MÉTODOS DE UTILIDADES INTERNOS
    // ==============================================

    /**
     * Retorna a rota de início do processo (Passo 1)
     */
    protected function getStep1Redirect($campanhaId = null)
    {
        // Tenta obter o ID da campanha da sessão se não for fornecido
        if (!$campanhaId) {
            $campanhaId = Session::get(self::SESSION_KEY)['campanha_id'] ?? null;
        }

        // Se tiver ID de campanha, redireciona para a criação do Step 1 com a campanha na query
        if ($campanhaId) {
            return redirect()->route('personagens.create', ['campanha' => $campanhaId])
                ->with('error', 'O processo de criação expirou ou foi interrompido. Por favor, comece novamente.');
        }

        // Caso contrário, redireciona para a lista de campanhas
        return redirect()->route('campanhas.index')
            ->with('error', 'Processo de criação não iniciado. Selecione uma campanha para começar.');
    }

    /**
     * Verifica o estado da sessão antes de qualquer passo
     */
    protected function checkSession()
    {
        if (!Session::has(self::SESSION_KEY) || !is_array(Session::get(self::SESSION_KEY))) {
            return $this->getStep1Redirect();
        }

        return Session::get(self::SESSION_KEY);
    }

    /**
     * Limpa os dados da sessão do criador de personagem
     */
    protected function clearSession()
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Obtém a lista de atributos base para o sistema atual
     */
    protected function getBaseAttributes(int $sistemaId): array
    {
        $sistema = Sistema::find($sistemaId);

        // Assumindo que o modelo Sistema tem um método getAtributosBase
        if ($sistema && method_exists($sistema, 'getAtributosBase')) {
            return $sistema->getAtributosBase();
        }

        return self::ATRIBUTOS_PADRAO;
    }

    /**
     * Calcula o progresso da criação para o Overview
     */
    private function calcularProgresso(Personagem $personagem)
    {
        // Verifica se os campos-chave de cada etapa estão preenchidos
        $basico = !empty($personagem->nome) && !empty($personagem->campanha_id) && !empty($personagem->nivel);
        $raca_classe = !empty($personagem->raca_id) && !empty($personagem->classe_id) && !empty($personagem->bonus_proficiencia);
        $atributos = !empty($personagem->atributos) && count(json_decode($personagem->atributos ?? '{}', true)) > 0;
        $vida_sanidade_sorte = !empty($personagem->vida);
        $inventario = true; // Inventário é opcional/sempre "completo" para o cálculo

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

    /**
     * Passo 1: Formulário do Coração do Personagem
     * Rota: personagens.create (GET)
     */
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

    /**
     * Processa o Passo 1 - Salva na Sessão e Redireciona para o Step 2
     * Rota: personagens.store.step1 (POST)
     */
    public function storeStep1(StoreStep1Request $request)
    {
        $validatedData = $request->validated();
        $personagemData = Session::get(self::SESSION_KEY, []);

        // 1. Processar upload de imagem temporária
        if ($request->hasFile('imagem_file')) {
            // Se já houver um arquivo temporário, apague-o antes de salvar o novo
            if (!empty($personagemData['imagem_temp_path'])) {
                Storage::disk('public')->delete($personagemData['imagem_temp_path']);
            }

            // Armazenar o arquivo em uma pasta temporária (ex: 'temp')
            $path = $request->file('imagem_file')->store('temp', 'public');
            $validatedData['imagem_temp_path'] = $path;
        } else {
            // Se não houve upload, mantém o caminho temporário anterior (se houver)
            $validatedData['imagem_temp_path'] = $personagemData['imagem_temp_path'] ?? null;
        }

        // 2. Mesclar e Salvar na Sessão
        $personagemData = array_merge($personagemData, $validatedData);

        // Garante que o sistema_id e campanha_id sejam mantidos
        $personagemData['sistema_id'] = $personagemData['sistema_id'] ?? $request->input('sistema_id');
        $personagemData['campanha_id'] = $personagemData['campanha_id'] ?? $request->input('campanha_id');

        Session::put(self::SESSION_KEY, $personagemData);

        // 3. Redirecionar para o próximo passo
        return redirect()->route('personagens.step2');
    }

    /**
     * Passo 2: Formulário de Raça, Classe e Origem
     * Rota: personagens.step2 (GET)
     */
    public function step2()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData; // Retorna o RedirectResponse
        }

        if (empty($sessionData['nome'])) {
            return redirect()->route('personagens.create', ['campanha' => $sessionData['campanha_id']])
                ->with('warning', 'Complete o nome do personagem (Passo 1) primeiro.');
        }

        $campanha = Campanha::with('sistema')->find($sessionData['campanha_id']);

        $racas = Raca::where('sistema_id', $sessionData['sistema_id'])->orderBy('nome')->get();
        $classes = Classe::where('sistema_id', $sessionData['sistema_id'])->orderBy('nome')->get();
        $origens = Origem::where('sistema_id', $sessionData['sistema_id'])->orderBy('nome')->get();

        return view('personagens.create.step2', [
            'data' => $sessionData,
            'campanha' => $campanha,
            'racas' => $racas,
            'classes' => $classes,
            'origens' => $origens
        ]);
    }

    /**
     * Processa o Passo 2 - Salva na Sessão e Redireciona para o Step 3
     * Rota: personagens.store.step2 (POST)
     */
    public function storeStep2(Request $request)
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        $validatedData = $request->validate([
            'raca_id' => ['nullable', 'exists:racas,id'],
            'classe_id' => ['nullable', 'exists:classes,id'],
            'origem_id' => ['nullable', 'exists:origens,id'],
            'bonus_proficiencia' => ['required', 'integer', 'min:1', 'max:6'],
        ]);

        $personagemData = array_merge($sessionData, $validatedData);
        Session::put(self::SESSION_KEY, $personagemData);

        return redirect()->route('personagens.step3');
    }

    /**
     * Passo 3: Atributos
     * Rota: personagens.step3 (GET)
     */
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

    /**
     * Processa o Passo 3 - Salva Atributos na Sessão e Redireciona para o Step 4
     * Rota: personagens.store.step3 (POST)
     */
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

    /**
     * Passo 4: Vida, Sanidade e Sorte
     * Rota: personagens.step4 (GET)
     */
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

    /**
     * Processa o Passo 4 - Salva na Sessão e Redireciona para o Step 5
     * Rota: personagens.store.step4 (POST)
     */
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

    /**
     * Passo 5: Inventário e Equipamento
     * Rota: personagens.step5 (GET)
     */
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

    /**
     * Processa o Passo 5 - Salva na Sessão e Redireciona para o Final
     * Rota: personagens.store.step5 (POST)
     */
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

    /**
     * Passo Final: Revisão antes de salvar
     * Rota: personagens.final (GET)
     */
    public function final()
    {
        $sessionData = $this->checkSession();
        if (!is_array($sessionData)) {
            return $sessionData;
        }

        // Verificação rápida dos dados obrigatórios
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
     * Processa o Passo Final e salva o personagem no banco
     * Rota: personagens.store.final (POST)
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
        if (isset($finalData['imagem_temp_path'])) {
            try {
                // Gera um novo nome de arquivo e move para a pasta 'personagens'
                $newFileName = time() . '-' . uniqid() . '.' . pathinfo($finalData['imagem_temp_path'], PATHINFO_EXTENSION);
                $newPath = 'personagens/' . $newFileName;

                Storage::disk('public')->move($finalData['imagem_temp_path'], $newPath);

                $finalData['imagem'] = $newPath;
            } catch (\Exception $e) {
                \Log::error('Erro ao mover imagem do personagem: ' . $e->getMessage());
                $finalData['imagem'] = null; // Falha ao mover, zera o caminho
            }
        }

        // 2. Limpeza de campos temporários
        unset($finalData['imagem_temp_path'], $finalData['sistema_id']);

        // 3. Criação da instância no banco de dados
        try {
            $personagem = DB::transaction(function () use ($finalData) {
                return Personagem::create($finalData);
            });

            $this->clearSession();

            return redirect()->route('personagens.show', $personagem)
                ->with('success', 'Personagem criado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao salvar personagem no criador: ' . $e->getMessage(), $finalData ?? []);
            return back()->with('error', 'Falha ao salvar o personagem. Por favor, revise os dados.');
        }
    }

    // ==============================================
    // FLUXO DE EDIÇÃO (EDIT - DATABASE BASED)
    // ==============================================

    /**
     * Overview da criação/edição
     * Rota: personagens.overview
     */
    public function overview(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $progresso = $this->calcularProgresso($personagem);
        return view('personagens.overview', compact('personagem', 'progresso'));
    }

    /**
     * Edição da Etapa 1: Coração do Personagem
     * Rota: personagens.edit.step1
     */
    public function editStep1(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.step1', compact('personagem'));
    }

    /**
     * Atualização da Etapa 1 - Coração do Personagem (incluindo Imagem)
     * Rota: personagens.update.step1
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
            'ativo_checkbox_only' => ['sometimes', 'accepted'], // Para capturar o checkbox
            'imagem_upload' => ['nullable', 'image', 'max:2048'],
            'remove_imagem' => ['nullable', 'boolean'],
        ]);

        $validatedData['ativo'] = $request->has('ativo_checkbox_only');
        unset($validatedData['ativo_checkbox_only']);

        $imagem_path_to_save = $personagem->imagem;

        // A. REMOVER IMAGEM EXISTENTE
        if ($request->filled('remove_imagem') && $personagem->imagem) {
            Storage::disk('public')->delete($personagem->imagem);
            $imagem_path_to_save = null;
        }

        // B. UPLOAD DE NOVA IMAGEM
        if ($request->hasFile('imagem_upload')) {
            // Se houver imagem antiga e não foi marcada para remoção, deleta a antiga
            if ($personagem->imagem && !$request->filled('remove_imagem')) {
                Storage::disk('public')->delete($personagem->imagem);
            }

            $path = $request->file('imagem_upload')->store('personagens', 'public');
            $imagem_path_to_save = $path;
        }

        $personagem->update(array_merge($validatedData, [
            'imagem' => $imagem_path_to_save,
        ]));

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Dados básicos atualizados com sucesso!');
    }

    /**
     * Edição da Etapa 2: Raça, Classe e Origem
     * Rota: personagens.edit.step2
     */
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

    /**
     * Atualização da Etapa 2 - Raça, Classe e Origem
     * Rota: personagens.update.step2
     */
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

    /**
     * Edição da Etapa 3: Atributos
     * Rota: personagens.edit.step3
     */
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

    /**
     * Atualização da Etapa 3 - Atributos
     * Rota: personagens.update.step3
     */
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

    // ... [MÉTODOS DE EDIÇÃO E UTILIDADE PARA STEP 4 E STEP 5, SORTEIOS, PERÍCIAS (não alterados, mas inclusos para completude)]

    /**
     * Edição da Etapa 4: Vida, Sanidade e Sorte
     * Rota: personagens.edit.step4
     */
    public function editStep4(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.step4', compact('personagem'));
    }

    /**
     * Atualização da Etapa 4 - Vida, Sanidade e Sorte
     * Rota: personagens.update.step4
     */
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

    /**
     * Edição da Etapa 5: Inventário e Equipamento
     * Rota: personagens.edit.step5
     */
    public function editStep5(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.step5', compact('personagem'));
    }

    /**
     * Atualização da Etapa 5 - Inventário e Equipamento
     * Rota: personagens.update.step5
     */
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

    /**
     * Finalizar criação/edição e visualizar personagem completo
     * Rota: personagens.final.personagem
     */
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

    /**
     * Sortear atributos (AJAX)
     * Rota: personagens.sortear.atributos
     */
    public function sortearAtributos(Request $request)
    {
        // NOTA: Para o modo de criação (SESSION), esta rota precisaria ser adaptada para usar sessionData
        // Por enquanto, adaptada para edição (Personagem existente) se Personagem for passado.
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

    /**
     * Sortear pontos de vida (AJAX)
     * Rota: personagens.sortear.vida
     */
    public function sortearVida(Request $request, Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Obtém a classe para usar o dado de vida correto, se a relação estiver carregada
        $classe = $personagem->classe;
        $dadoVida = $classe->dado_vida ?? 8; // Exemplo: classe com d8 de vida, ou 8 como fallback
        $nivel = $personagem->nivel;
        $vidaSorteada = 0;

        // Regra: Primeiro nível sempre tem o máximo de vida, e os demais são sorteados
        $vidaSorteada += $dadoVida; // Nível 1

        for ($i = 1; $i < $nivel; $i++) {
            $vidaSorteada += rand(1, $dadoVida);
        }

        // Adiciona modificador de constituição
        $atributos = json_decode($personagem->atributos, true);

        // Verifica se 'constituicao' existe e calcula o modificador
        $conScore = $atributos['constituicao'] ?? 10;
        $modConstituicao = floor(($conScore - 10) / 2);

        $vidaSorteada += ($modConstituicao * $nivel);

        return response()->json(['vida' => max(1, $vidaSorteada)]);
    }

    /**
     * Editar perícias
     * Rota: personagens.edit.pericias
     */
    public function editPericias(Personagem $personagem)
    {
        if ($personagem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('personagens.edit.pericias', compact('personagem'));
    }

    /**
     * Atualizar perícias
     * Rota: personagens.update.pericias
     */
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
