<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Campanha;
use App\Models\Classe;
use App\Models\Raca;
use App\Models\Origem;
use App\Models\Sistema;
use App\Models\Pericia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonagemController extends Controller
{
    /**
     * Listar todos os personagens do usuário
     */
    public function index()
    {
        $personagens = Personagem::with(['classe', 'raca', 'origem', 'campanha'])
            ->where('user_id', auth()->id())
            ->get();

        return view('personagens.index', compact('personagens'));
    }

    /**
     * Mostrar formulário de criação - Passo 1: Dados Básicos
     */
    public function create(Request $request)
    {
        $campanha = null;
        if ($request->has('campanha')) {
            $campanha = Campanha::with('sistema')->findOrFail($request->campanha);
        }

        $campanhas = Campanha::where('user_id', auth()->id())->get();

        return view('personagens.create', compact('campanha', 'campanhas'));
    }

    /**
     * Processar criação - Passo 1: Dados Básicos
     */
    public function storeStep1(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|exists:campanhas,id',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
        ]);

        $campanha = Campanha::with('sistema')->findOrFail($request->campanha_id);

        // Cria personagem com dados básicos
        $personagem = Personagem::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'historia' => $request->historia,
            'personalidade' => $request->personalidade,
            'user_id' => auth()->id(),
            'sistema_id' => $campanha->sistema_id,
            'campanha_id' => $campanha->id,
            'nivel' => 1,
            'xp' => 0,
            'bonus_proficiencia' => 2,
            'ativo' => true
        ]);

        return redirect()->route('personagens.overview', $personagem->id);
    }

    /**
     * Overview - Menu principal de criação com navegação livre
     */
    public function overview($id)
    {
        $personagem = Personagem::with(['sistema', 'campanha', 'raca', 'classe', 'origem'])->findOrFail($id);

        // Verificar progresso
        $progresso = $this->calcularProgresso($personagem);

        return view('personagens.overview', compact('personagem', 'progresso'));
    }

    /**
     * Step 2: Escolha de Classe, Raça e Origem com informações detalhadas
     */
    public function step2($id)
    {
        $personagem = Personagem::with(['sistema'])->findOrFail($id);

        $classes = Classe::where('sistema_id', $personagem->sistema_id)->get();
        $racas = Raca::where('sistema_id', $personagem->sistema_id)->get();
        $origens = Origem::where('sistema_id', $personagem->sistema_id)->get();

        return view('personagens.step2', compact('personagem', 'classes', 'racas', 'origens'));
    }

    /**
     * Processar Step 2: Salvar Classe, Raça e Origem
     */
    public function storeStep2(Request $request, $id)
    {
        $personagem = Personagem::findOrFail($id);

        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'raca_id' => 'required|exists:racas,id',
            'origem_id' => 'nullable|exists:origens,id'
        ]);

        $personagem->update($request->only(['classe_id', 'raca_id', 'origem_id']));

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Raça, classe e origem definidas com sucesso!');
    }

    /**
     * Step 3: Distribuição de Atributos do Sistema
     */
    public function step3($id)
    {
        $personagem = Personagem::with(['sistema', 'classe', 'raca', 'origem'])->findOrFail($id);
        $atributosSistema = $personagem->sistema->atributos ?? [];

        return view('personagens.step3', compact('personagem', 'atributosSistema'));
    }

    /**
     * Processar Step 3: Salvar Atributos
     */
    public function storeStep3(Request $request, $id)
    {
        $personagem = Personagem::with(['sistema'])->findOrFail($id);

        $atributosSistema = $personagem->sistema->atributos ?? [];
        $rules = [];

        // Regras para atributos do sistema
        foreach ($atributosSistema as $key => $nome) {
            $rules[$key] = 'required|integer|min:1|max:20';
        }

        // Regras para atributos especiais
        if ($personagem->sistema->usa_sanidade) {
            $rules['sanidade'] = 'required|integer|min:0|max:100';
        }
        if ($personagem->sistema->usa_sorte) {
            $rules['sorte'] = 'required|integer|min:1|max:100';
        }

        $request->validate($rules);

        // Preparar dados dos atributos
        $atributos = [];
        foreach ($atributosSistema as $key => $nome) {
            $atributos[$key] = $request->$key;
        }

        $updateData = ['atributos' => $atributos];

        // Adicionar atributos especiais
        if ($personagem->sistema->usa_sanidade) {
            $updateData['sanidade'] = $request->sanidade;
        }
        if ($personagem->sistema->usa_sorte) {
            $updateData['sorte'] = $request->sorte;
        }

        $personagem->update($updateData);

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Atributos definidos com sucesso!');
    }

    /**
     * Step 4: Vida e Equipamento Inicial
     */
    public function step4($id)
    {
        $personagem = Personagem::with(['classe', 'sistema'])->findOrFail($id);

        // Processar equipamento inicial
        $equipamentoInicial = $this->processarEquipamentoClasse($personagem->classe);

        // Calcular vida base
        $vidaBase = $this->calcularVidaBase($personagem);

        return view('personagens.step4', compact('personagem', 'equipamentoInicial', 'vidaBase'));
    }

    /**
     * Processar Step 4: Salvar Vida e Equipamento
     */
    public function storeStep4(Request $request, $id)
    {
        $personagem = Personagem::findOrFail($id);

        $request->validate([
            'vida' => 'required|integer|min:1',
            'equipamento_escolhido' => 'nullable|array'
        ]);

        $updateData = [
            'vida' => $request->vida,
            'vida_maxima' => $request->vida
        ];

        // Salvar equipamento escolhido
        if ($request->equipamento_escolhido) {
            $inventario = [
                'equipamento_inicial' => $request->equipamento_escolhido,
                'itens_adicionais' => []
            ];
            $updateData['inventario'] = $inventario;
        }

        $personagem->update($updateData);

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Vida e equipamento definidos com sucesso!');
    }

    /**
     * Step 5: Perícias e Proficiências
     */
    public function step5($id)
    {
        $personagem = Personagem::with(['sistema', 'classe', 'raca', 'origem'])->findOrFail($id);

        // Obter perícias do sistema
        $periciasSistema = $this->getPericiasBySistema($personagem->sistema_id);

        // Processar perícias da classe
        $periciasClasse = $this->processarPericiasClasse($personagem->classe);

        // Processar perícias da origem
        $periciasOrigem = $this->processarPericiasOrigem($personagem->origem);

        return view('personagens.step5', compact('personagem', 'periciasSistema', 'periciasClasse', 'periciasOrigem'));
    }

    /**
     * Processar Step 5: Salvar Perícias
     */
    public function storeStep5(Request $request, $id)
    {
        $personagem = Personagem::findOrFail($id);

        $request->validate([
            'pericias_escolhidas' => 'nullable|array',
            'pericias_escolhidas.*' => 'string'
        ]);

        $personagem->update([
            'pericias' => $request->pericias_escolhidas ?? []
        ]);

        return redirect()->route('personagens.overview', $personagem->id)
            ->with('success', 'Perícias definidas com sucesso!');
    }

    /**
     * Final: Dashboard do Personagem com gráfico e análise
     */
    public function final($id)
    {
        $personagem = Personagem::with(['classe', 'raca', 'origem', 'sistema', 'campanha'])
            ->findOrFail($id);

        // Calcular todas as informações finais
        $dadosCalculados = $this->calcularDadosFinais($personagem);

        return view('personagens.final', compact('personagem', 'dadosCalculados'));
    }

    /**
     * Mostrar personagem (View completa)
     */
    public function show($id)
    {
        $personagem = Personagem::with(['classe', 'raca', 'origem', 'sistema', 'campanha'])
            ->findOrFail($id);

        $dadosCalculados = $this->calcularDadosFinais($personagem);

        return view('personagens.show', compact('personagem', 'dadosCalculados'));
    }

    /**
     * Editar personagem
     */
    public function edit($id)
    {
        $personagem = Personagem::findOrFail($id);
        $campanhas = Campanha::where('user_id', auth()->id())->get();
        $classes = Classe::where('sistema_id', $personagem->sistema_id)->get();
        $racas = Raca::where('sistema_id', $personagem->sistema_id)->get();
        $origens = Origem::where('sistema_id', $personagem->sistema_id)->get();

        return view('personagens.edit', compact('personagem', 'campanhas', 'classes', 'racas', 'origens'));
    }

    /**
     * Atualizar personagem
     */
    public function update(Request $request, $id)
    {
        $personagem = Personagem::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|exists:campanhas,id',
            'classe_id' => 'required|exists:classes,id',
            'raca_id' => 'required|exists:racas,id',
            'origem_id' => 'nullable|exists:origens,id',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
        ]);

        $personagem->update($request->all());

        return redirect()->route('personagens.show', $personagem->id)
            ->with('success', 'Personagem atualizado com sucesso!');
    }

    /**
     * Deletar personagem
     */
    public function destroy($id)
    {
        $personagem = Personagem::findOrFail($id);
        $personagem->delete();

        return redirect()->route('personagens.index')
            ->with('success', 'Personagem deletado com sucesso!');
    }

    /**
     * Sortear atributos via AJAX
     */
    public function sortearAtributos(Request $request, $id)
    {
        $personagem = Personagem::with(['sistema'])->findOrFail($id);
        $atributosSistema = $personagem->sistema->atributos ?? [];

        $valoresSorteados = [];
        foreach ($atributosSistema as $key => $nome) {
            $valoresSorteados[$key] = $this->rolar4d6();
        }

        // Sortear sorte se o sistema usar
        if ($personagem->sistema->usa_sorte) {
            $valoresSorteados['sorte'] = rand(1, 100);
        }

        // Sortear sanidade se o sistema usar
        if ($personagem->sistema->usa_sanidade) {
            $valoresSorteados['sanidade'] = rand(50, 100);
        }

        return response()->json($valoresSorteados);
    }

    /**
     * Sortear vida via AJAX
     */
    public function sortearVida(Request $request, $id)
    {
        $personagem = Personagem::with(['classe'])->findOrFail($id);

        if (!$personagem->classe) {
            return response()->json(['error' => 'Classe não definida'], 400);
        }

        $dadoVida = $personagem->classe->dado_vida ?? 'd6';
        $constituicao = $personagem->atributos['constituicao'] ?? 10;
        $modificador = floor(($constituicao - 10) / 2);

        $rolagem = $this->rolarDado($dadoVida);
        $vidaTotal = max(1, $rolagem + $modificador);

        return response()->json([
            'rolagem' => $rolagem,
            'modificador' => $modificador,
            'total' => $vidaTotal
        ]);
    }

    /**
     * ============================================
     * MÉTODOS PRIVADOS AUXILIARES
     * ============================================
     */

    /**
     * Calcular progresso da criação do personagem
     */
    private function calcularProgresso($personagem)
    {
        $progresso = [
            'basico' => !empty($personagem->nome) && !empty($personagem->campanha_id),
            'raca_classe' => !empty($personagem->raca_id) && !empty($personagem->classe_id),
            'atributos' => !empty($personagem->atributos),
            'vida_equipamento' => !empty($personagem->vida),
            'pericias' => !empty($personagem->pericias)
        ];

        $progresso['completo'] = array_sum($progresso) === count($progresso);
        $progresso['porcentagem'] = (array_sum($progresso) / count($progresso)) * 100;

        return $progresso;
    }

    /**
     * Processar equipamento inicial da classe
     */
    private function processarEquipamentoClasse($classe)
    {
        if (!$classe) return ['fixas' => [], 'opcoes' => []];

        $equipamento = is_string($classe->equipamento_inicial) ?
            json_decode($classe->equipamento_inicial, true) :
            ($classe->equipamento_inicial ?? []);

        return [
            'fixas' => $equipamento['fixas'] ?? [],
            'opcoes' => $equipamento['opcoes'] ?? []
        ];
    }

    /**
     * Processar perícias iniciais da classe
     */
    private function processarPericiasClasse($classe)
    {
        if (!$classe) return ['fixas' => [], 'lista' => [], 'escolha' => 0];

        $pericias = is_string($classe->pericias_iniciais) ?
            json_decode($classe->pericias_iniciais, true) :
            ($classe->pericias_iniciais ?? []);

        return [
            'fixas' => $pericias['fixas'] ?? [],
            'lista' => $pericias['lista'] ?? [],
            'escolha' => $pericias['escolha'] ?? 0
        ];
    }

    /**
     * Processar perícias da origem
     */
    private function processarPericiasOrigem($origem)
    {
        if (!$origem) return [];

        $pericias = is_string($origem->bonus_pericias) ?
            json_decode($origem->bonus_pericias, true) :
            ($origem->bonus_pericias ?? []);

        return $pericias;
    }

    /**
     * Calcular vida base do personagem
     */
    private function calcularVidaBase($personagem)
    {
        if (!$personagem->classe) return 0;

        $dadoVida = $personagem->classe->dado_vida ?? 'd6';
        $constituicao = $personagem->atributos['constituicao'] ?? 10;
        $modificador = floor(($constituicao - 10) / 2);

        // Valor médio do dado (metade + 1)
        $valorDado = ceil((intval(str_replace('d', '', $dadoVida)) + 1) / 2);

        return max(1, $valorDado + $modificador);
    }

    /**
     * Obter perícias do sistema
     */
    private function getPericiasBySistema($sistema_id)
    {
        $sistema = Sistema::find($sistema_id);

        // Buscar perícias do banco ou usar padrão
        $pericias = Pericia::where('sistema_id', $sistema_id)->get();

        if ($pericias->isEmpty()) {
            // Fallback para perícias padrão D&D
            return [
                'Acrobacia' => 'destreza',
                'Arcanismo' => 'inteligencia',
                'Atletismo' => 'forca',
                'Atuação' => 'carisma',
                'Enganação' => 'carisma',
                'Furtividade' => 'destreza',
                'História' => 'inteligencia',
                'Intimidação' => 'carisma',
                'Intuição' => 'sabedoria',
                'Investigação' => 'inteligencia',
                'Lidar com Animais' => 'sabedoria',
                'Medicina' => 'sabedoria',
                'Natureza' => 'inteligencia',
                'Percepção' => 'sabedoria',
                'Persuasão' => 'carisma',
                'Prestidigitação' => 'destreza',
                'Religião' => 'inteligencia',
                'Sobrevivência' => 'sabedoria'
            ];
        }

        $periciasArray = [];
        foreach ($pericias as $pericia) {
            $periciasArray[$pericia->nome] = $pericia->atributo_relacionado;
        }

        return $periciasArray;
    }

    /**
     * Calcular dados finais do personagem (modificadores e perícias)
     */
    private function calcularDadosFinais($personagem)
    {
        $atributos = $personagem->atributos ?? [];
        $periciasSistema = $this->getPericiasBySistema($personagem->sistema_id);
        $periciasPersonagem = $personagem->pericias ?? [];
        $bonusProficiencia = $personagem->bonus_proficiencia ?? 2;

        // Calcular modificadores de atributos
        $modificadores = [];
        foreach ($atributos as $atributo => $valor) {
            $modificadores[$atributo] = floor(($valor - 10) / 2);
        }

        // Calcular perícias
        $periciasCalculadas = [];
        foreach ($periciasSistema as $pericia => $atributo) {
            $modificador = $modificadores[$atributo] ?? 0;
            $proficiente = in_array($pericia, $periciasPersonagem);
            $bonus = $modificador + ($proficiente ? $bonusProficiencia : 0);

            $periciasCalculadas[$pericia] = [
                'atributo' => $atributo,
                'modificador' => $modificador,
                'proficiente' => $proficiente,
                'bonus' => $bonus,
                'bonus_display' => $bonus >= 0 ? "+{$bonus}" : $bonus
            ];
        }

        return [
            'modificadores' => $modificadores,
            'pericias' => $periciasCalculadas,
            'bonus_proficiencia' => $bonusProficiencia
        ];
    }

    /**
     * Rolagem 4d6 (descartar menor)
     */
    private function rolar4d6()
    {
        $dados = [rand(1, 6), rand(1, 6), rand(1, 6), rand(1, 6)];
        sort($dados);
        array_shift($dados); // Remove o menor
        return array_sum($dados);
    }

    /**
     * Rolagem de dado genérico
     */
    private function rolarDado($dado)
    {
        $match = [];
        if (preg_match('/d(\d+)/i', $dado, $match)) {
            return rand(1, intval($match[1]));
        }
        return 1;
    }
}
