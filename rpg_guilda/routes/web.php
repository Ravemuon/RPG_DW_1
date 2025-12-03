<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    UserController,
    AmizadeController,
    CampanhaController,
    CampanhaUsuarioController,
    PersonagemController,
    PersonagemOrigemController,
    PersonagemPericiaController,
    PersonagemCreatorController,
    ClasseController,
    OrigemController,
    PericiaController,
    MissaoController,
    SessaoController,
    SistemaController,
    NotificacaoController,
    RolagemController,
    ArquivoController,
    RacaController,
    ChatPrivadoController
};
use ConsoleTVs\Charts\Charts;
use App\Charts\MissoesStatusChart;
use App\Charts\SessaoPresencasChart;

Charts::routes();

Route::get('/chart/missoes-status', [MissoesStatusChart::class, 'handler'])->name('chart.missoes');
Route::get('/chart/sessoes-presencas', [SessaoPresencasChart::class, 'handler'])->name('chart.sessoes');

// Página inicial
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Autenticação
Route::get('/login', [UserController::class, 'loginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.perform');
Route::get('/register', [UserController::class, 'registerForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.perform');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/home/dicionario', [HomeController::class, 'dicionario'])->name('home.dicionario');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {

    /*------------------------------------
    | Usuários
    ------------------------------------*/
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/perfil', [UserController::class, 'perfil'])->name('usuarios.perfil');
        Route::get('/editar', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/update', [UserController::class, 'update'])->name('usuarios.update');
        Route::put('/tema', [UserController::class, 'atualizarTema'])->name('usuarios.tema.update');
        Route::post('/upload/{tipo}', [UserController::class, 'uploadImagem'])->name('usuarios.uploadImagem');

        // Procurar e visualizar perfis
        Route::get('/procurar', [UserController::class, 'procurar'])->name('usuarios.procurar');
        Route::get('/perfil/{id}', [UserController::class, 'perfilPublico'])->name('usuarios.perfilpublico');

        /*------------------------------------
        | Amizades
        ------------------------------------*/
        Route::prefix('amizades')->group(function () {
            // VISUALIZAÇÃO E LISTAGEM
            Route::get('/', [AmizadeController::class, 'index'])->name('amizades.index');
            Route::get('/pendentes', [AmizadeController::class, 'pendentes'])->name('amizades.pendentes');
            Route::get('/procurar', [AmizadeController::class, 'procurar'])->name('amizades.procurar');
            Route::get('/amigos', [AmizadeController::class, 'amigos'])->name('amizades.amigos');
            Route::get('/usuario/{id}', [AmizadeController::class, 'perfilPublico'])->name('amizades.perfilpublico');

            // AÇÕES (POST/DELETE)
            Route::post('/adicionar/{id}', [AmizadeController::class, 'adicionar'])->name('amizades.adicionar');
            Route::post('/aceitar/{id}', [AmizadeController::class, 'aceitar'])->name('amizades.aceitar');

            // CORREÇÃO: Usando DELETE para operações de remoção/destruição (melhor prática RESTful)
            Route::delete('/remover/{id}', [AmizadeController::class, 'remover'])->name('amizades.remover');
        });
    });

    /*-------------------------------
    | Campanhas, Missões e Sessões
    |-------------------------------*/
    Route::prefix('campanhas')->group(function () {
        Route::get('/', [CampanhaController::class, 'index'])->name('campanhas.index');
        Route::get('/todas', [CampanhaController::class, 'todas'])->name('campanhas.todas');
        Route::get('/minhas', [CampanhaController::class, 'minhas'])->name('campanhas.minhas');
        Route::get('/create', [CampanhaController::class, 'create'])->name('campanhas.create');
        Route::post('/', [CampanhaController::class, 'store'])->name('campanhas.store');

        // Rotas específicas com {campanha} devem vir primeiro
        Route::post('/{campanha}/solicitar', [CampanhaController::class, 'solicitarEntrada'])->name('campanhas.solicitar');
        Route::get('/{campanha}/edit', [CampanhaController::class, 'edit'])->name('campanhas.edit');
        Route::put('/{campanha}', [CampanhaController::class, 'update'])->name('campanhas.update');
        Route::delete('/{campanha}', [CampanhaController::class, 'destroy'])->name('campanhas.destroy');

        Route::get('/{campanha}', [CampanhaController::class, 'show'])->name('campanhas.show');

        // Área do mestre
        Route::get('/{campanha}/mestre', [CampanhaController::class, 'mestre'])->name('campanhas.mestre');
        Route::post('/{campanha}/usuarios/gerenciar', [CampanhaController::class, 'gerenciarUsuario'])->name('campanhas.gerenciar');
        Route::post('/{campanha}/usuarios/aprovar', [CampanhaController::class, 'aprovarUsuario'])->name('campanhas.aprovar');
        Route::post('/{campanha}/usuarios/adicionar', [CampanhaController::class, 'adicionarAmigo'])->name('campanhas.adicionar');

        // Missões
        Route::prefix('{campanha}/missoes')->name('missoes.')->group(function () {
            Route::get('/', [MissaoController::class, 'index'])->name('index');
            Route::get('/create', [MissaoController::class, 'create'])->name('create');
            Route::post('/', [MissaoController::class, 'store'])->name('store');
            Route::get('{missao}', [MissaoController::class, 'show'])->name('show');
            Route::get('{missao}/edit', [MissaoController::class, 'edit'])->name('edit');
            Route::put('{missao}', [MissaoController::class, 'update'])->name('update');
            Route::delete('{missao}', [MissaoController::class, 'destroy'])->name('destroy');

            Route::get('{missao}/pdf', [MissaoController::class, 'exportarPdf'])->name('exportarPdf');
        });

        // Sessões
        Route::prefix('{campanha}/sessoes')->name('sessoes.')->group(function () {
            Route::get('/', [SessaoController::class, 'index'])->name('index');
            Route::get('/criar', [SessaoController::class, 'create'])->name('create');
            Route::post('/', [SessaoController::class, 'store'])->name('store');

            Route::get('/{sessao}', [SessaoController::class, 'show'])->name('show');
            Route::get('/{sessao}/editar', [SessaoController::class, 'edit'])->name('edit');
            Route::put('/{sessao}', [SessaoController::class, 'update'])->name('update');
            Route::delete('/{sessao}', [SessaoController::class, 'destroy'])->name('destroy');

            Route::post('/{sessao}/adicionar-personagem', [SessaoController::class, 'adicionarPersonagem'])->name('adicionar-personagem');
            Route::post('/{sessao}/confirmar-personagem', [SessaoController::class, 'confirmarPersonagem'])->name('confirmar-personagem');
            Route::put('/{sessao}/personagem/{personagem}', [SessaoController::class, 'atualizarPersonagem'])->name('atualizar-personagem');
            Route::get('/{sessao}/exportar-pdf', [SessaoController::class, 'exportarPdf'])->name('exportar-pdf');

            Route::post('/{sessao}/marcar-presenca', [SessaoController::class, 'marcarPresenca'])->name('marcar_presenca');

        });

    }); // Fim do prefixo 'campanhas'


    /*--------------------------------------------------------------------------
    | Rotas do Módulo de Personagens
    |--------------------------------------------------------------------------
    | Organizado por prefixo 'personagens' e nome de rota 'personagens.'
    */

    Route::prefix('personagens')->name('personagens.')->group(function () {

        // 1. LISTAGEM (INDEX)
        Route::get('/', [PersonagemController::class, 'index'])->name('index');

        // 2. FLUXO DE CRIAÇÃO (Baseado em SESSÃO - SEM {personagem} na URI)

        // Passo 1
        // VIEW de criação (Início do fluxo - nome: 'personagens.create')
        Route::get('/create/step1', [PersonagemCreatorController::class, 'create'])->name('create');
        // Submissão do Passo 1
        Route::post('/store/step1', [PersonagemCreatorController::class, 'storeStep1'])->name('store.step1');

        // Passos 2 a 5 (VIEW e STORE - Criança)
        Route::get('/step2', [PersonagemCreatorController::class, 'step2'])->name('step2');
        Route::post('/store/step2', [PersonagemCreatorController::class, 'storeStep2'])->name('store.step2');

        Route::get('/step3', [PersonagemCreatorController::class, 'step3'])->name('step3');
        Route::post('/store/step3', [PersonagemCreatorController::class, 'storeStep3'])->name('store.step3');

        Route::get('/step4', [PersonagemCreatorController::class, 'step4'])->name('step4');
        Route::post('/store/step4', [PersonagemCreatorController::class, 'storeStep4'])->name('store.step4');

        Route::get('/step5', [PersonagemCreatorController::class, 'step5'])->name('step5');
        Route::post('/store/step5', [PersonagemCreatorController::class, 'storeStep5'])->name('store.step5');

        // Revisão Final e Salvamento no DB
        Route::get('/final', [PersonagemCreatorController::class, 'final'])->name('final');
        Route::post('/store/final', [PersonagemCreatorController::class, 'storeFinal'])->name('store.final');


        // 3. ROTAS QUE DEPENDEM DO ID DO PERSONAGEM ({personagem})
        // Todas as rotas abaixo usam o parâmetro {personagem} na URL
        Route::prefix('{personagem}')->group(function () {

            // --- A. CRUD BÁSICO & VISUALIZAÇÃO (Mantido no PersonagemController) ---
            // SHOW (Visualização de Detalhes - nome: 'personagens.show')
            Route::get('/', [PersonagemController::class, 'show'])->name('show');
            // UPDATE (Edição geral, não de passo - nome: 'personagens.update')
            Route::put('/', [PersonagemController::class, 'update'])->name('update');
            // DESTROY (Excluir - nome: 'personagens.destroy')
            Route::delete('/', [PersonagemController::class, 'destroy'])->name('destroy');
            // EDIT (VIEW do formulário de edição geral - nome: 'personagens.edit')
            Route::get('/edit', [PersonagemController::class, 'edit'])->name('edit');


            // --- B. FLUXO DE EDIÇÃO DE STEPS (Delegado ao CreatorController) ---
            // Rotas VIEW: overview (Redireciona para show)
            Route::get('/overview', [PersonagemCreatorController::class, 'overview'])->name('overview');

            // Passo 1 (Dados Básicos)
            Route::get('/edit/step1', [PersonagemCreatorController::class, 'editStep1'])->name('edit.step1');
            Route::put('/update/step1', [PersonagemCreatorController::class, 'updateStep1'])->name('update.step1');

            // Passo 2 (Raça, Classe, Origem)
            Route::get('/edit/step2', [PersonagemCreatorController::class, 'editStep2'])->name('edit.step2');
            Route::put('/update/step2', [PersonagemCreatorController::class, 'updateStep2'])->name('update.step2');

            // Passo 3 (Atributos)
            Route::get('/edit/step3', [PersonagemCreatorController::class, 'editStep3'])->name('edit.step3');
            Route::put('/update/step3', [PersonagemCreatorController::class, 'updateStep3'])->name('update.step3');

            // Passo 4 (Pontos e XP)
            Route::get('/edit/step4', [PersonagemCreatorController::class, 'editStep4'])->name('edit.step4');
            Route::put('/update/step4', [PersonagemCreatorController::class, 'updateStep4'])->name('update.step4');

            // Passo 5 (Inventário, Imagem)
            Route::get('/edit/step5', [PersonagemCreatorController::class, 'editStep5'])->name('edit.step5');
            Route::put('/update/step5', [PersonagemCreatorController::class, 'updateStep5'])->name('update.step5');


            // --- C. AÇÕES E UTILITÁRIOS (Sorteios - Delegado ao CreatorController) ---
            Route::post('/sortear-atributos', [PersonagemCreatorController::class, 'sortearAtributos'])->name('sortear.atributos');
            Route::post('/sortear-vida', [PersonagemCreatorController::class, 'sortearVida'])->name('sortear.vida');


            // --- D. SUB-RECURSOS (Origens e Perícias) ---

            // Rotas de relacionamento N:N para Origens
            Route::post('/origens/add', [PersonagemOrigemController::class, 'store'])->name('origens.add');
            Route::delete('/origens/{origem}', [PersonagemOrigemController::class, 'destroy'])->name('origens.remove');

            // Rotas de relacionamento N:N para Perícias
            // Edição/Atualização Massiva de Perícias (VIEW e UPDATE)
            Route::get('/pericias/edit', [PersonagemCreatorController::class, 'editPericias'])->name('pericias.edit');
            Route::put('/pericias', [PersonagemCreatorController::class, 'updatePericias'])->name('pericias.update');

            // Ações CRUD Individuais (Se necessário)
            // Route::post('/pericias/add', [PersonagemPericiaController::class, 'store'])->name('pericias.add');
            // Route::delete('/pericias/{pericia}', [PersonagemPericiaController::class, 'destroy'])->name('pericias.remove');
        });
    }); // Fim do prefixo 'personagens''


    // ============================================================================
    // SISTEMAS DE RPG
    // ============================================================================

    Route::prefix('sistemas')->name('sistemas.')->group(function () {
        // Exportação em PDF
        Route::get('/exportar-pdf', [SistemaController::class, 'exportarPdf'])->name('exportar-pdf');

        // Página principal e CRUD do sistema
        Route::get('/', [SistemaController::class, 'index'])->name('index');
        Route::get('/create', [SistemaController::class, 'create'])->name('create');
        Route::post('/', [SistemaController::class, 'store'])->name('store');
        Route::get('/{sistema}', [SistemaController::class, 'show'])->name('show');
        Route::get('/{sistema}/edit', [SistemaController::class, 'edit'])->name('edit');
        Route::put('/{sistema}', [SistemaController::class, 'update'])->name('update');
        Route::delete('/{sistema}', [SistemaController::class, 'destroy'])->name('destroy');

        // Exportação em PDF do sistema
        Route::get('/{sistema}/pdf', [SistemaController::class, 'pdf'])->name('pdf');

        // =========================================================================
        // SUB-RECURSOS RELACIONADOS AO SISTEMA
        // =========================================================================

        // CLASSES
        Route::prefix('{sistema}/classes')->name('classes.')->group(function () {
            Route::get('/', [ClasseController::class, 'index'])->name('index'); // Lista de classes
            Route::get('/create', [ClasseController::class, 'create'])->name('create'); // Formulário de criação de classe
            Route::post('/', [ClasseController::class, 'store'])->name('store'); // Armazenar nova classe
            Route::get('/{classe}', [ClasseController::class, 'show'])->name('show'); // Detalhes da classe
            Route::get('/{classe}/edit', [ClasseController::class, 'edit'])->name('edit'); // Formulário de edição de classe
            Route::put('/{classe}', [ClasseController::class, 'update'])->name('update'); // Atualizar classe
            Route::delete('/{classe}', [ClasseController::class, 'destroy'])->name('destroy');
        });


    // ORIGENS
        Route::prefix('sistemas/{sistema}/origens')->name('origens.')->group(function () {
            Route::get('/', [OrigemController::class, 'index'])->name('index');
            Route::get('/create', [OrigemController::class, 'create'])->name('create');
            Route::post('/', [OrigemController::class, 'store'])->name('store');
            Route::get('/{origem}', [OrigemController::class, 'show'])->name('show');
            Route::get('/{origem}/edit', [OrigemController::class, 'edit'])->name('edit');
            Route::put('/{origem}', [OrigemController::class, 'update'])->name('update');
            Route::delete('/{origem}', [OrigemController::class, 'destroy'])->name('destroy');
        });


        // RAÇAS
        Route::prefix('{sistema}/racas')->name('racas.')->group(function () {
            Route::get('/', [RacaController::class, 'index'])->name('index');
            Route::get('/create', [RacaController::class, 'create'])->name('create');
            Route::post('/', [RacaController::class, 'store'])->name('store');
            Route::get('/{raca}', [RacaController::class, 'show'])->name('show');
            Route::get('/{raca}/edit', [RacaController::class, 'edit'])->name('edit');
            Route::put('/{raca}', [RacaController::class, 'update'])->name('update');
            Route::delete('/{raca}', [RacaController::class, 'destroy'])->name('destroy');
        });

        // PERÍCIAS
            Route::prefix('{sistema}/pericias')->name('pericias.')->group(function () {
                Route::get('/', [PericiaController::class, 'index'])->name('index');
                Route::get('/create', [PericiaController::class, 'create'])->name('create');
                Route::post('/', [PericiaController::class, 'store'])->name('store');
                Route::get('/{pericia}/edit', [PericiaController::class, 'edit'])->name('edit');
                Route::put('/{pericia}', [PericiaController::class, 'update'])->name('update');
                Route::delete('/{pericia}', [PericiaController::class, 'destroy'])->name('destroy');
            });


    }); // Fim do prefixo 'sistemas'


    /*------------------------------------
    | Missões, Notificações, Rolagens, Arquivos
    ------------------------------------*/
    // Missões (A rota resource original está OK aqui)
    Route::resource('missoes', MissaoController::class);

    // Notificações
    Route::prefix('notificacoes')->middleware('auth')->group(function () {
        Route::get('/', [NotificacaoController::class, 'index'])->name('notificacoes.index');
        Route::post('/marcar/{id}', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcar');
        Route::post('/marcar-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.marcarTodas');
        Route::delete('/limpar-todas', [NotificacaoController::class, 'limparTodas'])->name('notificacoes.limparTodas');
        Route::delete('/{id}', [NotificacaoController::class, 'destroy'])->name('notificacoes.destroy');
    });

    // Rolagens
    Route::post('/rolagens', [RolagemController::class, 'store'])->name('rolagens.store');
    Route::get('/rolagens/historico', [RolagemController::class, 'index'])->name('rolagens.index');

    // Arquivos
    Route::post('/arquivos/upload', [ArquivoController::class, 'upload'])->name('arquivos.upload');
    Route::get('/arquivos/{arquivo}', [ArquivoController::class, 'download'])->name('arquivos.download');


    /*------------------------------------
    | Chat Privado
    ------------------------------------*/
    // Exibe a lista de amigos e chats privados
    Route::get('/chat/privado', [ChatPrivadoController::class, 'index'])->name('chat.privado.index');

    // Exibe o chat privado entre dois usuários
    Route::get('/chat-privado/{user}', [ChatPrivadoController::class, 'mostrar'])->name('chat.privado.mostrar');

    // Envia uma nova mensagem no chat privado
    Route::post('/chat-privado/{chat}/mensagem', [ChatPrivadoController::class, 'store'])->name('chat.privado.store');

}); // Fim do middleware 'auth'
