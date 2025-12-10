<?php

namespace App\Http\Controllers;

use App\Models\Personagem;
use App\Models\Campanha;
use App\Models\Sistema;
use App\Models\Raca;
use App\Models\Classe;
use App\Models\Origem;
use App\Models\Pericia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class PersonagemController extends Controller
{
    /**
     * Middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view,personagem')->only(['show', 'edit', 'update', 'destroy']);
    }

    /**
     * Lista personagens do usuário
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ====================
        // CONSTRUIR QUERY BASE
        // ====================
        $query = Personagem::with([
            'campanha:id,nome,status',
            'raca:id,nome',
            'classe:id,nome,dado_vida',
            'origem:id,nome',
            'sistema:id,nome,usa_sanidade',
            'user:id,name'
        ]);

        // Usuários comuns veem apenas seus personagens
        if (!$user->isMestre()) {
            $query->where('user_id', $user->id);
        }

        // ====================
        // FILTROS AVANÇADOS
        // ====================
        
        // Filtro por campanha
        if ($request->filled('campanha_id')) {
            $campanhaId = $request->campanha_id;
            $query->where('campanha_id', $campanhaId);
        }

        // Filtro por sistema
        if ($request->filled('sistema_id')) {
            $sistemaId = $request->sistema_id;
            $query->where('sistema_id', $sistemaId);
        }

        // Filtro por status (ativo/inativo)
        if ($request->filled('ativo')) {
            $ativo = $request->ativo === 'true';
            $query->where('ativo', $ativo);
        }

        // Filtro por raça
        if ($request->filled('raca_id')) {
            $query->where('raca_id', $request->raca_id);
        }

        // Filtro por classe
        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }

        // Filtro por origem
        if ($request->filled('origem_id')) {
            $query->where('origem_id', $request->origem_id);
        }

        // Filtro por nível mínimo e máximo
        if ($request->filled('nivel_min')) {
            $query->where('nivel', '>=', $request->nivel_min);
        }
        
        if ($request->filled('nivel_max')) {
            $query->where('nivel', '<=', $request->nivel_max);
        }

        // Filtro por XP mínimo e máximo
        if ($request->filled('xp_min')) {
            $query->where('xp', '>=', $request->xp_min);
        }
        
        if ($request->filled('xp_max')) {
            $query->where('xp', '<=', $request->xp_max);
        }

        // Filtro de busca textual
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                ->orWhere('descricao', 'LIKE', "%{$search}%")
                ->orWhere('historia', 'LIKE', "%{$search}%")
                ->orWhere('personalidade', 'LIKE', "%{$search}%")
                ->orWhereHas('campanha', function ($q) use ($search) {
                    $q->where('nome', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('raca', function ($q) use ($search) {
                    $q->where('nome', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('classe', function ($q) use ($search) {
                    $q->where('nome', 'LIKE', "%{$search}%");
                });
            });
        }

        // Ordenação personalizada
        $ordenacao = $request->filled('ordenar') ? $request->ordenar : 'created_at_desc';
        
        switch ($ordenacao) {
            case 'nome_asc':
                $query->orderBy('nome', 'asc');
                break;
            case 'nome_desc':
                $query->orderBy('nome', 'desc');
                break;
            case 'nivel_asc':
                $query->orderBy('nivel', 'asc');
                break;
            case 'nivel_desc':
                $query->orderBy('nivel', 'desc');
                break;
            case 'xp_asc':
                $query->orderBy('xp', 'asc');
                break;
            case 'xp_desc':
                $query->orderBy('xp', 'desc');
                break;
            case 'atualizado_desc':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'atualizado_asc':
                $query->orderBy('updated_at', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        // ====================
        // CALCULAR ESTATÍSTICAS (ANTES DA PAGINAÇÃO)
        // ====================
        $estatisticas = [
            'total' => 0,
            'ativos' => 0,
            'inativos' => 0,
            'por_nivel' => [],
            'por_campanha' => [],
            'por_sistema' => [],
            'media_nivel' => 0,
            'total_xp' => 0,
            'personagem_maior_nivel' => null,
            'personagem_mais_xp' => null,
        ];

        // Clonar a query para estatísticas
        $queryEstatisticas = clone $query;
        $todosPersonagens = $queryEstatisticas->get();
        
        if ($todosPersonagens->isNotEmpty()) {
            $estatisticas['total'] = $todosPersonagens->count();
            $estatisticas['ativos'] = $todosPersonagens->where('ativo', true)->count();
            $estatisticas['inativos'] = $todosPersonagens->where('ativo', false)->count();
            $estatisticas['media_nivel'] = round($todosPersonagens->avg('nivel'), 1);
            $estatisticas['total_xp'] = $todosPersonagens->sum('xp');
            
            // Salvar o total para uso nos cálculos de porcentagem
            $totalPersonagens = $todosPersonagens->count();
            
            // Distribuição por nível
            $estatisticas['por_nivel'] = $todosPersonagens->groupBy('nivel')
                ->map(function ($group, $nivel) use ($totalPersonagens) {
                    return [
                        'nivel' => $nivel,
                        'quantidade' => $group->count(),
                        'porcentagem' => round(($group->count() / $totalPersonagens) * 100, 1)
                    ];
                })
                ->sortBy('nivel')
                ->values();
            
            // Distribuição por campanha
            $estatisticas['por_campanha'] = $todosPersonagens->groupBy('campanha.nome')
                ->map(function ($group, $campanhaNome) use ($totalPersonagens) {
                    return [
                        'campanha' => $campanhaNome ?? 'Sem Campanha',
                        'quantidade' => $group->count(),
                        'porcentagem' => round(($group->count() / $totalPersonagens) * 100, 1)
                    ];
                })
                ->sortByDesc('quantidade')
                ->take(5)
                ->values();
            
            // Distribuição por sistema
            $estatisticas['por_sistema'] = $todosPersonagens->groupBy('sistema.nome')
                ->map(function ($group, $sistemaNome) use ($totalPersonagens) {
                    return [
                        'sistema' => $sistemaNome ?? 'Sistema Desconhecido',
                        'quantidade' => $group->count(),
                        'porcentagem' => round(($group->count() / $totalPersonagens) * 100, 1)
                    ];
                })
                ->sortByDesc('quantidade')
                ->values();
            
            // Personagem com maior nível
            $estatisticas['personagem_maior_nivel'] = $todosPersonagens->sortByDesc('nivel')->first();
            
            // Personagem com mais XP
            $estatisticas['personagem_mais_xp'] = $todosPersonagens->sortByDesc('xp')->first();
            
            // Níveis mais comuns
            $estatisticas['niveis_mais_comuns'] = $todosPersonagens->groupBy('nivel')
                ->map(function ($group) {
                    return $group->count();
                })
                ->sortDesc()
                ->take(3)
                ->map(function ($quantidade, $nivel) {
                    return [
                        'nivel' => $nivel,
                        'quantidade' => $quantidade
                    ];
                })
                ->values();
        }

        // ====================
        // PAGINAÇÃO (APÓS ESTATÍSTICAS)
        // ====================
        $porPagina = $request->filled('por_pagina') ? $request->por_pagina : 12;
        $personagens = $query->paginate($porPagina)->withQueryString();

        // ====================
        // DADOS PARA FILTROS - VERSÃO SEGURA
        // ====================
        $campanhas = Campanha::where(function ($q) use ($user) {
            if (!$user->isMestre()) {
                $q->where('criador_id', $user->id)
                ->orWhereHas('jogadores', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        })->get(['id', 'nome', 'status']);

        // Obter sistemas disponíveis - usando subquery segura
        $sistemasDisponiveis = Sistema::whereIn('id', function ($query) use ($user) {
            $query->select('sistema_id')
                ->from('personagens')
                ->whereNotNull('sistema_id')
                ->when(!$user->isMestre(), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->distinct();
        })->get(['id', 'nome']);

        // Obter raças disponíveis - usando subquery segura
        $racasDisponiveis = Raca::whereIn('id', function ($query) use ($user) {
            $query->select('raca_id')
                ->from('personagens')
                ->whereNotNull('raca_id')
                ->when(!$user->isMestre(), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->distinct();
        })->get(['id', 'nome', 'sistema_id']);

        // Obter classes disponíveis - usando subquery segura
        $classesDisponiveis = Classe::whereIn('id', function ($query) use ($user) {
            $query->select('classe_id')
                ->from('personagens')
                ->whereNotNull('classe_id')
                ->when(!$user->isMestre(), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->distinct();
        })->get(['id', 'nome', 'sistema_id']);

        // Obter origens disponíveis - usando subquery segura
        $origensDisponiveis = Origem::whereIn('id', function ($query) use ($user) {
            $query->select('origem_id')
                ->from('personagens')
                ->whereNotNull('origem_id')
                ->when(!$user->isMestre(), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->distinct();
        })->get(['id', 'nome', 'sistema_id']);

        // Opções de ordenação
        $opcoesOrdenacao = [
            'created_at_desc' => 'Mais Recentes',
            'created_at_asc' => 'Mais Antigos',
            'nome_asc' => 'Nome (A-Z)',
            'nome_desc' => 'Nome (Z-A)',
            'nivel_desc' => 'Maior Nível',
            'nivel_asc' => 'Menor Nível',
            'xp_desc' => 'Mais XP',
            'xp_asc' => 'Menos XP',
            'atualizado_desc' => 'Recentes Atualizações',
            'atualizado_asc' => 'Antigas Atualizações',
        ];

        // Opções de itens por página
        $opcoesPorPagina = [6, 12, 24, 48, 96];

        // ====================
        // LOG DE CONSULTA (opcional - para debug)
        // ====================
        if (config('app.debug')) {
            \Log::debug('Consulta de personagens', [
                'user_id' => $user->id,
                'is_mestre' => $user->isMestre(),
                'filtros' => $request->all(),
                'total_resultados' => $personagens->total(),
                'total_estatisticas' => $todosPersonagens->count()
            ]);
        }

        // ====================
        // RETORNAR VIEW
        // ====================
        return view('personagens.index', compact(
            'personagens',
            'campanhas',
            'sistemasDisponiveis',
            'racasDisponiveis',
            'classesDisponiveis',
            'origensDisponiveis',
            'estatisticas',
            'opcoesOrdenacao',
            'opcoesPorPagina'
        ));
    }

    public function create(Request $request)
    {
        $campanha = null;
        $sistema = null;
        $racas = collect();
        $classes = collect();
        $origens = collect();

        $campanhaId = $request->input('campanha_id') ?? $request->input('campanha');
        
        if ($campanhaId) {
            $campanha = Campanha::with('sistema')->findOrFail($campanhaId);
            $this->authorize('criarPersonagem', $campanha);
            
            $sistema = $campanha->sistema;
            
            $racas = Raca::where('sistema_id', $sistema->id)->get();
            $classes = Classe::where('sistema_id', $sistema->id)->get();
            $origens = Origem::where('sistema_id', $sistema->id)->get();
        }

        $campanhas = Campanha::where('status', 'ativa')
            ->where(function ($query) {
                $user = Auth::user();
                $query->where('criador_id', $user->id)
                    ->orWhereHas('jogadores', function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->whereIn('campanha_usuario.status', ['ativo', 'mestre']);
                    })
                    ->orWhere(function ($q) {
                        $q->where('privada', false);
                    });
            })
            ->with('sistema')
            ->orderBy('nome')
            ->get();

        return view('personagens.create', compact(
            'campanhas', 
            'campanha', 
            'sistema',
            'racas',
            'classes',
            'origens'
        ));
    }

    /**
     * Armazena novo personagem
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'campanha_id' => 'required|exists:campanhas,id',
            'raca_id' => 'nullable|exists:racas,id',
            'classe_id' => 'nullable|exists:classes,id',
            'origem_id' => 'nullable|exists:origens,id',
            'nivel' => 'integer|min:1|max:20',
            'xp' => 'integer|min:0',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
            'imagem' => 'nullable|image|max:2048',
            'atributos' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verificar permissão
        $campanha = Campanha::findOrFail($request->campanha_id);
        $this->authorize('criarPersonagem', $campanha);

        // Crie o personagem com sistema_id da campanha
        $personagemData = [
            'nome' => $request->nome,
            'user_id' => Auth::id(),
            'campanha_id' => $request->campanha_id,
            'sistema_id' => $campanha->sistema_id,
            'raca_id' => $request->raca_id,
            'classe_id' => $request->classe_id,
            'origem_id' => $request->origem_id,
            'nivel' => $request->nivel ?? 1,
            'xp' => $request->xp ?? 0,
            'bonus_proficiencia' => 2,
            'descricao' => $request->descricao,
            'historia' => $request->historia,
            'personalidade' => $request->personalidade,
            'ativo' => true,
        ];

        // Adicionar atributos se existirem
        if ($request->has('atributos')) {
            $personagemData['atributos'] = json_encode($request->atributos);
        }

        $personagem = Personagem::create($personagemData);

        // Processar imagem se existir
        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('personagens', 'public');
            $personagem->update(['imagem' => $path]);
        }

        // Criar perícias iniciais
        $this->criarPericiasIniciais($personagem);

        return redirect()->route('personagens.show', $personagem)
            ->with('success', 'Personagem criado com sucesso!');
    }

    /**
     * Mostra detalhes do personagem
     */
    public function show(Personagem $personagem)
    {
        $this->authorize('view', $personagem);

        $personagem->load([
            'campanha', 
            'raca', 
            'classe', 
            'origem', 
            'sistema',
            'pericias.pericia'
        ]);

        $atributosCompletos = $personagem->atributosCompletos();
        $pontosVida = $personagem->calcularPontosVida();
        $progressoNivel = $personagem->progressoNivel();

        return view('personagens.show', compact(
            'personagem',
            'atributosCompletos',
            'pontosVida',
            'progressoNivel'
        ));
    }

    /**
     * Mostra formulário de edição
     */
    public function edit(Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        // CORREÇÃO: Mude 'participantes' para 'jogadores'
        $campanhas = Campanha::where('status', 'ativa')
            ->where(function ($query) {
                $query->where('criador_id', Auth::id())
                      ->orWhereHas('jogadores', function ($q) {
                          $q->where('user_id', Auth::id())
                            ->whereIn('campanha_usuario.status', ['ativo', 'mestre']);
                      });
            })
            ->get();

        $sistema = $personagem->sistema;
        $racas = Raca::where('sistema_id', $sistema->id)->get();
        $classes = Classe::where('sistema_id', $sistema->id)->get();
        $origens = Origem::where('sistema_id', $sistema->id)->get();

        return view('personagens.edit', compact(
            'personagem',
            'campanhas',
            'racas',
            'classes',
            'origens',
            'sistema'
        ));
    }

    /**
     * Atualiza personagem - VERSÃO FUNCIONAL
     */
    public function update(Request $request, Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        // DEBUG: Log dos dados recebidos
        \Log::info('UPDATE PERSONAGEM - INÍCIO', [
            'personagem_id' => $personagem->id,
            'nome' => $request->nome,
            'nivel' => $request->nivel,
            'atributos_count' => $request->has('atributos') ? count($request->atributos) : 0,
            'has_imagem' => $request->hasFile('imagem'),
            'remover_imagem' => $request->has('remover_imagem')
        ]);

        // Validação completa
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'nivel' => 'required|integer|min:1|max:20',
            'xp' => 'required|integer|min:0',
            'bonus_proficiencia' => 'required|integer|min:0|max:10',
            'descricao' => 'nullable|string',
            'historia' => 'nullable|string',
            'personalidade' => 'nullable|string',
            'sanidade' => 'nullable|integer|min:0|max:100',
            'sorte' => 'nullable|integer|min:0|max:100',
            'imagem' => 'nullable|image|max:2048',
            'atributos' => 'required|array',
            'atributos.*' => 'required|integer|min:1|max:20',
            'inventario' => 'nullable|string',
            'ativo' => 'sometimes|boolean',
            'remover_imagem' => 'sometimes|boolean'
        ], [
            'nome.required' => 'O nome do personagem é obrigatório.',
            'nivel.required' => 'O nível é obrigatório.',
            'xp.required' => 'O XP é obrigatório.',
            'bonus_proficiencia.required' => 'O bônus de proficiência é obrigatório.',
            'atributos.required' => 'Os atributos são obrigatórios.',
            'atributos.array' => 'Os atributos devem ser um array válido.',
            'atributos.*.required' => 'Cada atributo é obrigatório.',
            'atributos.*.integer' => 'Cada atributo deve ser um número inteiro.',
            'atributos.*.min' => 'Cada atributo deve ser no mínimo 1.',
            'atributos.*.max' => 'Cada atributo deve ser no máximo 20.',
            'imagem.image' => 'O arquivo deve ser uma imagem.',
            'imagem.max' => 'A imagem não pode ter mais que 2MB.'
        ]);

        if ($validator->fails()) {
            \Log::error('UPDATE PERSONAGEM - VALIDAÇÃO FALHOU', [
                'errors' => $validator->errors()->toArray()
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor, corrija os erros no formulário.');
        }

        try {
            // Iniciar transação para garantir consistência
            DB::beginTransaction();

            // 1. Preparar dados básicos
            $dadosAtualizacao = [
                'nome' => $request->nome,
                'nivel' => $request->nivel,
                'xp' => $request->xp,
                'bonus_proficiencia' => $request->bonus_proficiencia,
                'descricao' => $request->descricao,
                'historia' => $request->historia,
                'personalidade' => $request->personalidade,
                'sanidade' => $request->sanidade ?? null,
                'sorte' => $request->sorte ?? null,
                'ativo' => $request->boolean('ativo'),
                'updated_at' => now(),
            ];

            \Log::info('Dados básicos preparados', $dadosAtualizacao);

            // 2. Processar atributos com bônus de raça
            if ($request->has('atributos') && is_array($request->atributos)) {
                $atributosBase = $request->atributos;
                
                // Aplicar bônus da raça se existir
                if ($personagem->raca && $personagem->raca->modificadores_atributos) {
                    $bonusRaca = is_string($personagem->raca->modificadores_atributos) 
                        ? json_decode($personagem->raca->modificadores_atributos, true)
                        : $personagem->raca->modificadores_atributos;
                    
                    if (is_array($bonusRaca)) {
                        foreach ($bonusRaca as $atributo => $bonus) {
                            if (isset($atributosBase[$atributo]) && is_numeric($bonus)) {
                                $atributosBase[$atributo] = (int)$atributosBase[$atributo] + (int)$bonus;
                                \Log::debug("Aplicado bônus de raça para $atributo: +$bonus");
                            }
                        }
                    }
                }
                
                $dadosAtualizacao['atributos'] = json_encode($atributosBase, JSON_UNESCAPED_UNICODE);
                \Log::info('Atributos processados', ['atributos' => $atributosBase]);
            }

            // 3. Processar inventário
            if ($request->filled('inventario')) {
                try {
                    $inventarioText = trim($request->inventario);
                    
                    // Verificar se é JSON válido
                    if ($this->isValidJson($inventarioText)) {
                        $inventarioDecodificado = json_decode($inventarioText, true);
                        $dadosAtualizacao['inventario'] = json_encode($inventarioDecodificado, JSON_UNESCAPED_UNICODE);
                        \Log::info('Inventário processado como JSON');
                    } else {
                        // Converter texto simples para array
                        $itens = array_filter(
                            array_map('trim', explode("\n", $inventarioText)),
                            function($item) { return !empty($item); }
                        );
                        
                        $dadosAtualizacao['inventario'] = json_encode($itens, JSON_UNESCAPED_UNICODE);
                        \Log::info('Inventário convertido de texto', ['itens' => $itens]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erro ao processar inventário, salvando como texto simples', [
                        'error' => $e->getMessage()
                    ]);
                    $dadosAtualizacao['inventario'] = json_encode([$request->inventario], JSON_UNESCAPED_UNICODE);
                }
            } else {
                $dadosAtualizacao['inventario'] = null;
            }

            // 4. Processar remoção de imagem
            if ($request->has('remover_imagem') && $request->boolean('remover_imagem')) {
                if ($personagem->imagem) {
                    try {
                        Storage::disk('public')->delete($personagem->imagem);
                        \Log::info('Imagem removida: ' . $personagem->imagem);
                    } catch (\Exception $e) {
                        \Log::warning('Erro ao remover imagem antiga: ' . $e->getMessage());
                    }
                }
                $dadosAtualizacao['imagem'] = null;
            }

            // 5. Processar nova imagem (se não foi marcada para remoção)
            if (!$request->boolean('remover_imagem') && $request->hasFile('imagem')) {
                if ($request->file('imagem')->isValid()) {
                    // Remover imagem antiga se existir
                    if ($personagem->imagem) {
                        try {
                            Storage::disk('public')->delete($personagem->imagem);
                        } catch (\Exception $e) {
                            \Log::warning('Erro ao remover imagem antiga: ' . $e->getMessage());
                        }
                    }
                    
                    // Salvar nova imagem
                    $path = $request->file('imagem')->store('personagens', 'public');
                    $dadosAtualizacao['imagem'] = $path;
                    \Log::info('Nova imagem salva: ' . $path);
                } else {
                    \Log::warning('Upload de imagem falhou - arquivo inválido');
                }
            }

            // 6. Atualizar personagem
            \Log::info('Atualizando personagem com dados:', $dadosAtualizacao);
            
            $personagem->update($dadosAtualizacao);
            
            // Verificar se subiu de nível com base no XP
            $this->verificarSubidaNivel($personagem);

            DB::commit();

            \Log::info('UPDATE PERSONAGEM - SUCESSO', [
                'personagem_id' => $personagem->id,
                'nome' => $personagem->nome,
                'nivel' => $personagem->nivel
            ]);

            // Redirecionar com mensagem de sucesso
            return redirect()->route('personagens.show', $personagem)
                ->with('success', 'Personagem atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('UPDATE PERSONAGEM - ERRO', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['imagem']) // Não logar arquivos
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao atualizar personagem: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Verifica se o personagem subiu de nível com base no XP
     */
    private function verificarSubidaNivel(Personagem $personagem)
    {
        $xpPorNivel = [
            1 => 0, 2 => 300, 3 => 900, 4 => 2700, 5 => 6500,
            6 => 14000, 7 => 23000, 8 => 34000, 9 => 48000,
            10 => 64000, 11 => 85000, 12 => 100000, 13 => 120000,
            14 => 140000, 15 => 165000, 16 => 195000,
            17 => 225000, 18 => 265000, 19 => 305000, 20 => 355000
        ];

        $nivelAtual = $personagem->nivel;
        
        // Verificar se atingiu XP para próximo nível
        if ($nivelAtual < 20 && isset($xpPorNivel[$nivelAtual + 1])) {
            $xpNecessario = $xpPorNivel[$nivelAtual + 1];
            
            if ($personagem->xp >= $xpNecessario) {
                $personagem->nivel = $nivelAtual + 1;
                $personagem->bonus_proficiencia = floor(($personagem->nivel - 1) / 4) + 2;
                $personagem->save();
                
                \Log::info('Personagem subiu de nível', [
                    'personagem_id' => $personagem->id,
                    'novo_nivel' => $personagem->nivel,
                    'novo_bonus_proficiencia' => $personagem->bonus_proficiencia
                ]);
            }
        }
    }

    /**
     * Verifica se uma string é JSON válido
     */
    private function isValidJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Remove personagem
     */
    public function destroy(Personagem $personagem)
    {
        $this->authorize('delete', $personagem);

        // Remover imagem
        if ($personagem->imagem) {
            Storage::disk('public')->delete($personagem->imagem);
        }

        $personagem->delete();

        return redirect()->route('personagens.index')
            ->with('success', 'Personagem removido com sucesso!');
    }

    /**
     * Restaura personagem deletado
     */
    public function restore($id)
    {
        $personagem = Personagem::withTrashed()->findOrFail($id);
        $this->authorize('restore', $personagem);

        $personagem->restore();

        return redirect()->route('personagens.show', $personagem)
            ->with('success', 'Personagem restaurado com sucesso!');
    }

    /**
     * Mostra formulário de gerenciamento de perícias
     */
    public function pericias(Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $pericias = Pericia::where('sistema_id', $personagem->sistema_id)->get();
        
        $personagemPericias = $personagem->pericias()->with('pericia')->get()->keyBy('pericia_id');

        return view('personagens.pericias', compact('personagem', 'pericias', 'personagemPericias'));
    }

    /**
     * Atualiza perícias do personagem
     */
    public function updatePericias(Request $request, Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $request->validate([
            'pericias' => 'nullable|array',
            'pericias.*.proficiente' => 'boolean',
            'pericias.*.bonus_especial' => 'integer'
        ]);

        foreach ($request->pericias ?? [] as $periciaId => $data) {
            $personagem->pericias()->updateOrCreate(
                ['pericia_id' => $periciaId],
                [
                    'proficiente' => $data['proficiente'] ?? false,
                    'bonus_especial' => $data['bonus_especial'] ?? 0
                ]
            );
        }

        return redirect()->route('personagens.show', $personagem)
            ->with('success', 'Perícias atualizadas com sucesso!');
    }

    /**
     * Adiciona XP ao personagem
     */
    public function adicionarXp(Request $request, Personagem $personagem)
    {
        $this->authorize('update', $personagem);

        $request->validate([
            'xp' => 'required|integer|min:1'
        ]);

        $personagem->increment('xp', $request->xp);

        // Verificar se subiu de nível
        $xpProximoNivel = $personagem->xpProximoNivel();
        if ($personagem->xp >= $xpProximoNivel) {
            $personagem->increment('nivel');
            $personagem->bonus_proficiencia = floor(($personagem->nivel - 1) / 4) + 2;
            $personagem->save();

            return redirect()->route('personagens.show', $personagem)
                ->with('success', "{$request->xp} XP adicionados! Personagem subiu para nível {$personagem->nivel}!");
        }

        return redirect()->route('personagens.show', $personagem)
            ->with('success', "{$request->xp} XP adicionados!");
    }

    /**
     * Exporta personagem como PDF
     */
    public function exportarPdf(Personagem $personagem)
    {
        $this->authorize('view', $personagem);

        $personagem->load(['campanha', 'raca', 'classe', 'origem', 'pericias.pericia']);
        $atributosCompletos = $personagem->atributosCompletos();
        $pontosVida = $personagem->calcularPontosVida();

        // --- Cálculo do progresso de XP para o próximo nível ---
        $xpAtual = $personagem->xp;
        $xpProximo = $personagem->xpProximoNivel();
        $progressoNivel = $xpProximo > 0 ? ($xpAtual / $xpProximo) * 100 : 100;

        $pdf = \PDF::loadView('personagens.pdf.ficha', compact(
            'personagem',
            'atributosCompletos',
            'pontosVida',
            'progressoNivel'
        ));

        return $pdf->download("ficha-{$personagem->nome}.pdf");
    }

    /**
     * Método auxiliar: cria perícias iniciais para o personagem
     */
    private function criarPericiasIniciais(Personagem $personagem)
    {
        if (!$personagem->sistema) {
            return;
        }

        $periciasSistema = Pericia::where('sistema_id', $personagem->sistema_id)->get();

        foreach ($periciasSistema as $pericia) {
            try {
                $personagem->pericias()->create([
                    'pericia_id' => $pericia->id,
                    'proficiente' => false,
                    'bonus_especial' => 0
                ]);
            } catch (\Exception $e) {
                \Log::error('Erro ao criar perícia inicial: ' . $e->getMessage());
            }
        }
    }
}