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
    PersonagemAdjusterController,
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

use App\Charts\MissoesStatusChart;
use App\Charts\SessaoPresencasChart;

Route::get('/chart/missoes-status', function (MissoesStatusChart $chart) {
    return $chart->build();
});

Route::get('/chart/sessoes-presencas', function (SessaoPresencasChart $chart) {
    return $chart->build();
});

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
        Route::prefix('campanhas/{campanha}/sessoes')->name('sessoes.')->group(function () {
            Route::get('/', [SessaoController::class, 'index'])->name('index');
            Route::get('/create', [SessaoController::class, 'create'])->name('create');
            Route::post('/', [SessaoController::class, 'store'])->name('store');

            Route::prefix('{sessao}')->group(function () {
                Route::get('/', [SessaoController::class, 'show'])->name('show');
                Route::get('/edit', [SessaoController::class, 'edit'])->name('edit');
                Route::put('/', [SessaoController::class, 'update'])->name('update');
                Route::delete('/', [SessaoController::class, 'destroy'])->name('destroy');

                Route::get('/exportar-pdf', [SessaoController::class, 'exportarPdf'])->name('exportarPdf');

                // Rota para marcar presença
                Route::post('/marcar-presenca', [SessaoController::class, 'marcarPresenca'])->name('marcar_presenca');
            });
        });



    }); // Fim do prefixo 'campanhas'

    Route::prefix('personagens')->name('personagens.')->group(function () {

        // 1. LISTAGEM (INDEX)
        Route::get('/', [PersonagemController::class, 'index'])->name('index');

        // 2. FLUXO DE CRIAÇÃO (Baseado em SESSÃO - SEM {personagem} na URI)
        
        // --- C. AÇÕES E UTILITÁRIOS (Sorteios) ---
        Route::post('/sortear-atributos', [PersonagemCreatorController::class, 'sortearAtributos'])->name('sortearAtributos');
        Route::post('/sortear-vida', [PersonagemCreatorController::class, 'sortearVida'])->name('sortearVida');

        // Passo 1 (Início do fluxo)
        Route::get('/create/step1', [PersonagemCreatorController::class, 'create'])->name('create');
        Route::post('/store/step1', [PersonagemCreatorController::class, 'storeStep1'])->name('storeStep1');

        // Passos 2 a 5 (VIEW e STORE - Criação)
        Route::get('/step2', [PersonagemCreatorController::class, 'step2'])->name('step2');
        Route::post('/store/step2', [PersonagemCreatorController::class, 'storeStep2'])->name('storeStep2');

        Route::get('/step3', [PersonagemCreatorController::class, 'step3'])->name('step3');
        Route::post('/store/step3', [PersonagemCreatorController::class, 'storeStep3'])->name('storeStep3');

        Route::get('/step4', [PersonagemCreatorController::class, 'step4'])->name('step4');
        Route::post('/store/step4', [PersonagemCreatorController::class, 'storeStep4'])->name('storeStep4');

        Route::get('/step5', [PersonagemCreatorController::class, 'step5'])->name('step5');
        Route::post('/store/step5', [PersonagemCreatorController::class, 'storeStep5'])->name('storeStep5');

        // Revisão Final e Salvamento no DB
        Route::get('/final', [PersonagemCreatorController::class, 'final'])->name('final');
        Route::post('/store/final', [PersonagemCreatorController::class, 'storeFinal'])->name('storeFinal');


        // 3. ROTAS QUE DEPENDEM DO ID DO PERSONAGEM ({personagem})
        Route::prefix('{personagem}')->group(function () {

            // --- A. CRUD BÁSICO & VISUALIZAÇÃO ---
            Route::get('/', [PersonagemController::class, 'show'])->name('show');
            Route::put('/', [PersonagemController::class, 'update'])->name('update'); // Usado para update geral
            Route::delete('/', [PersonagemController::class, 'destroy'])->name('destroy');

            // NOVO: Rota de AJUSTE (para PersonagemAdjusterController)
            Route::post('/adjust', [PersonagemAdjusterController::class, 'adjust'])->name('adjust');

            // --- B. FLUXO DE EDIÇÃO DE STEPS (CreatorController) ---
            
            // VIEW da Visão Geral (Overview)
            Route::get('/edit', [PersonagemCreatorController::class, 'editOverview'])->name('editOverview'); 
            
            // NOVO: Rota de Edição Simplificada (GET - View)
            Route::get('/simple-edit', [PersonagemCreatorController::class, 'simpleEdit'])->name('simpleEdit'); 

            // ➡️ CORREÇÃO AQUI: Altere PersonagemController::class para PersonagemCreatorController::class
            Route::put('/update-simple-edit', [PersonagemCreatorController::class, 'updateSimpleEdit'])->name('updateSimpleEdit');

        });
    });

    // ============================================================================
    // SISTEMAS DE RPG
    // ============================================================================

    Route::prefix('sistemas')->name('sistemas.')->group(function () {
        // Exportação em PDF
        Route::get('/exportar-pdf', [SistemaController::class, 'exportarPdf'])->name('exportar-pdf');

        // Página principal e CRUD do sistema (sistemas.index, sistemas.show, etc.)
        Route::get('/', [SistemaController::class, 'index'])->name('index');
        Route::get('/create', [SistemaController::class, 'create'])->name('create');
        Route::post('/', [SistemaController::class, 'store'])->name('store');
        Route::get('/{sistema}', [SistemaController::class, 'show'])->name('show');
        Route::get('/{sistema}/edit', [SistemaController::class, 'edit'])->name('edit');
        Route::put('/{sistema}', [SistemaController::class, 'update'])->name('update');
        Route::delete('/{sistema}', [SistemaController::class, 'destroy'])->name('destroy');

        // Exportação em PDF do sistema específico
        Route::get('/{sistema}/pdf', [SistemaController::class, 'pdf'])->name('pdf');

        // =========================================================================
        // SUB-RECURSOS RELACIONADOS AO SISTEMA (Nomenclatura Corrigida: sistemas.recurso.ação)
        // =========================================================================

        // CLASSES
        Route::prefix('{sistema}/classes')->group(function () {
            // Rota index: sistemas.classes.index
            Route::get('/', [ClasseController::class, 'index'])->name('sistemas.classes.index');
            
            // Outras rotas (usando o prefixo sistemas.classes.)
            Route::name('sistemas.classes.')->group(function () {
                Route::get('/create', [ClasseController::class, 'create'])->name('create');
                Route::post('/', [ClasseController::class, 'store'])->name('store');
                Route::get('/{classe}', [ClasseController::class, 'show'])->name('show');
                Route::get('/{classe}/edit', [ClasseController::class, 'edit'])->name('edit');
                Route::put('/{classe}', [ClasseController::class, 'update'])->name('update');
                Route::delete('/{classe}', [ClasseController::class, 'destroy'])->name('destroy');
            });
        });


        // ORIGENS
        Route::prefix('{sistema}/origens')->group(function () {
            // Rota index: sistemas.origens.index (CORRIGIDO)
            Route::get('/', [OrigemController::class, 'index'])->name('sistemas.origens.index');
            
            // Outras rotas (usando o prefixo sistemas.origens.)
            Route::name('sistemas.origens.')->group(function () {
                Route::get('/create', [OrigemController::class, 'create'])->name('create');
                Route::post('/', [OrigemController::class, 'store'])->name('store');
                Route::get('/{origem}', [OrigemController::class, 'show'])->name('show');
                Route::get('/{origem}/edit', [OrigemController::class, 'edit'])->name('edit');
                Route::put('/{origem}', [OrigemController::class, 'update'])->name('update');
                Route::delete('/{origem}', [OrigemController::class, 'destroy'])->name('destroy');
            });
        });

        // RAÇAS
        Route::prefix('{sistema}/racas')->group(function () {
            // Rota index: sistemas.racas.index (CORRIGIDO)
            Route::get('/', [RacaController::class, 'index'])->name('sistemas.racas.index');
            
            // Outras rotas (usando o prefixo sistemas.racas.)
            Route::name('sistemas.racas.')->group(function () {
                Route::get('/create', [RacaController::class, 'create'])->name('create');
                Route::post('/', [RacaController::class, 'store'])->name('store');
                Route::get('/{raca}', [RacaController::class, 'show'])->name('show');
                Route::get('/{raca}/edit', [RacaController::class, 'edit'])->name('edit');
                Route::put('/{raca}', [RacaController::class, 'update'])->name('update');
                Route::delete('/{raca}', [RacaController::class, 'destroy'])->name('destroy');
            });
        });


        // PERÍCIAS
        Route::prefix('{sistema}/pericias')->group(function () {
            // Rota index: sistemas.pericias.index (CORRIGIDO)
            Route::get('/', [PericiaController::class, 'index'])->name('sistemas.pericias.index');

            // Outras rotas (usando o prefixo sistemas.pericias.)
            Route::name('sistemas.pericias.')->group(function () {
                Route::get('/create', [PericiaController::class, 'create'])->name('create');
                Route::post('/', [PericiaController::class, 'store'])->name('store');
                Route::get('/{pericia}', [PericiaController::class, 'show'])->name('show'); // Adicionado show
                Route::get('/{pericia}/edit', [PericiaController::class, 'edit'])->name('edit');
                Route::put('/{pericia}', [PericiaController::class, 'update'])->name('update');
                Route::delete('/{pericia}', [PericiaController::class, 'destroy'])->name('destroy');
            });
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
