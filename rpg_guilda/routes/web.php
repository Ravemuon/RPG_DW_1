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
    ClasseController,
    OrigemController,
    PericiaController,
    MissaoController,
    SessaoController,
    SistemaController,
    NotificacaoController,
    RolagemController,
    ArquivoController,
    RacaController
};

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
            Route::get('/', [AmizadeController::class, 'index'])->name('amizades.index');
            Route::get('/pendentes', [AmizadeController::class, 'pendentes'])->name('amizades.pendentes');
            Route::get('/procurar', [AmizadeController::class, 'procurar'])->name('amizades.procurar');
            Route::post('/adicionar/{id}', [AmizadeController::class, 'adicionar'])->name('amizades.adicionar');
            Route::post('/aceitar/{id}', [AmizadeController::class, 'aceitar'])->name('amizades.aceitar');
            Route::delete('/remover/{id}', [AmizadeController::class, 'remover'])->name('amizades.remover');
            Route::get('/amigos', [AmizadeController::class, 'amigos'])->name('amizades.amigos');
            Route::get('/usuario/{id}', [AmizadeController::class, 'perfilPublico'])->name('amizades.perfilpublico');
        });
    });

    /*------------------------------------
    | Campanhas
    ------------------------------------*/
    Route::prefix('campanhas')->group(function () {

        // CRUD básico
        Route::get('/', [CampanhaController::class, 'index'])->name('campanhas.index');
        Route::get('/todas', [CampanhaController::class, 'todas'])->name('campanhas.todas');
        Route::get('/minhas', [CampanhaController::class, 'minhas'])->name('campanhas.minhas');
        Route::get('/create', [CampanhaController::class, 'create'])->name('campanhas.create');
        Route::post('/', [CampanhaController::class, 'store'])->name('campanhas.store');
        Route::get('/{campanha}/edit', [CampanhaController::class, 'edit'])->name('campanhas.edit');
        Route::put('/{campanha}', [CampanhaController::class, 'update'])->name('campanhas.update');
        Route::delete('/{campanha}', [CampanhaController::class, 'destroy'])->name('campanhas.destroy');
        Route::get('/{campanha}', [CampanhaController::class, 'show'])->name('campanhas.show');

        // Área do mestre
        Route::get('/{campanha}/mestre', [CampanhaController::class, 'mestre'])->name('campanhas.mestre');
        Route::post('/{campanha}/usuarios/gerenciar', [CampanhaController::class, 'gerenciarUsuario'])->name('campanhas.usuarios.gerenciar');
        Route::post('/{campanha}/usuarios/aprovar', [CampanhaController::class, 'aprovarUsuario'])->name('campanhas.usuarios.aprovar');
        Route::patch('/personagens/{personagem}/toggle-publico', [PersonagemController::class, 'togglePublico'])->name('personagens.togglePublico')->middleware('auth');

        // Gerenciamento de usuários e amigos
        Route::middleware(['auth'])->group(function () {
            Route::post('/{campanha}/usuarios/adicionar-ajax', [CampanhaUsuarioController::class, 'adicionarAjax'])->name('campanhas.usuarios.adicionar.ajax');
            Route::post('/{campanha}/entrar', [CampanhaController::class, 'solicitarEntrada'])->name('campanhas.solicitar');
            Route::post('/{campanha}/gerenciar', [CampanhaController::class, 'gerenciarUsuarios'])->name('campanhas.usuarios.gerenciar');
            Route::post('/{campanha}/usuarios/adicionar', [CampanhaController::class, 'adicionarAmigo'])->name('campanhas.usuarios.adicionar');

            // Convidar amigos
            Route::get('/{campanha}/convidar', [CampanhaController::class, 'convidar'])->name('campanhas.convidar');
            Route::post('/{campanha}/convidar', [CampanhaController::class, 'enviarConvites'])->name('campanhas.convidar.enviar');
        });
        // Rota para mostrar o formulário de criação de missão
        Route::get('/campanha/{campanha}/missoes/create', [MissaoController::class, 'create'])->name('missoes.create');

        // Rota para armazenar a missão
        Route::post('/campanha/{campanha}/missoes', [MissaoController::class, 'store'])->name('missoes.store');

        // Rota para exibir a lista de missões
        Route::get('/campanha/{campanha}/missoes', [MissaoController::class, 'index'])->name('missoes.index');

        // Rota para mostrar detalhes da missão
        Route::get('/campanha/{campanha}/missoes/{missao}', [MissaoController::class, 'show'])->name('missoes.show');

        // Rota para editar a missão
        Route::get('/campanha/{campanha}/missoes/{missao}/edit', [MissaoController::class, 'edit'])->name('missoes.edit');

        // Rota para atualizar a missão
        Route::put('/campanha/{campanha}/missoes/{missao}', [MissaoController::class, 'update'])->name('missoes.update');

        // Rota para deletar a missão
        Route::delete('/campanha/{campanha}/missoes/{missao}', [MissaoController::class, 'destroy'])->name('missoes.destroy');

        // Sessões
        Route::get('/{campanha}/sessoes', [SessaoController::class, 'index'])->name('sessoes.index');
        Route::get('/{campanha}/sessoes/criar', [SessaoController::class, 'create'])->name('sessoes.create');
        Route::post('/{campanha}/sessoes', [SessaoController::class, 'store'])->name('sessoes.store');
        Route::get('/sessoes/{sessao}', [SessaoController::class, 'show'])->name('sessoes.show');
        Route::get('/sessoes/{sessao}/editar', [SessaoController::class, 'edit'])->name('sessoes.edit');
        Route::put('/sessoes/{sessao}', [SessaoController::class, 'update'])->name('sessoes.update');
        Route::delete('/{campanha}/sessoes/{sessao}', [SessaoController::class, 'destroy'])->name('sessoes.destroy');
        Route::post('/sessoes/{sessao}/adicionar-personagem', [SessaoController::class, 'adicionarPersonagem'])->name('sessoes.adicionar-personagem');
        Route::post('/sessoes/{sessao}/confirmar-personagem', [SessaoController::class, 'confirmarPersonagem'])->name('sessoes.confirmar-personagem')->middleware('auth');
        Route::put('/sessoes/{sessao}/personagem/{personagem}', [SessaoController::class, 'atualizarPersonagem'])->name('sessoes.atualizar-personagem');
        Route::get('/sessoes/{sessao}/exportar-pdf', [SessaoController::class, 'exportarPdf'])->name('sessoes.exportar-pdf');
        Route::post('/sessoes/{sessao}/confirmar-presenca', [SessaoController::class, 'adicionarPersonagem'])->middleware('auth')->name('sessoes.confirmar-presenca');

                // Missões
        Route::prefix('{campanha}/missoes')->name('missoes.')->group(function () {
            // Lista todas as missões de uma campanha
            Route::get('/', [MissaoController::class, 'index'])->name('index');

            // Formulário para criar uma nova missão
            Route::get('/create', [MissaoController::class, 'create'])->name('create');

            // Armazena uma nova missão
            Route::post('/', [MissaoController::class, 'store'])->name('store');

            // Exibe detalhes de uma missão específica
            Route::get('{missao}', [MissaoController::class, 'show'])->name('show');

            // Formulário para editar uma missão
            Route::get('{missao}/edit', [MissaoController::class, 'edit'])->name('edit');

            // Atualiza uma missão
            Route::put('{missao}', [MissaoController::class, 'update'])->name('update');

            // Deleta uma missão
            Route::delete('{missao}', [MissaoController::class, 'destroy'])->name('destroy');
        });

    });

    /*------------------------------------
    | Personagens
    ------------------------------------*/
    Route::resource('personagens', PersonagemController::class);
    Route::post('/personagens/{personagem}/origens/add', [PersonagemOrigemController::class, 'store'])->name('personagens.origens.add');
    Route::delete('/personagens/{personagem}/origens/{origem}', [PersonagemOrigemController::class, 'destroy'])->name('personagens.origens.remove');
    Route::post('/personagens/{personagem}/pericias/add', [PersonagemPericiaController::class, 'store'])->name('personagens.pericias.add');
    Route::delete('/personagens/{personagem}/pericias/{pericia}', [PersonagemPericiaController::class, 'destroy'])->name('personagens.pericias.remove');


    // ============================================================================
    // SISTEMAS DE RPG
    // ============================================================================

    Route::prefix('sistemas')->name('sistemas.')->group(function () {
        // Expx ortação em PDF
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


    });



    /*------------------------------------
    | Missões, Notificações, Rolagens, Arquivos
    ------------------------------------*/
    Route::resource('missoes', MissaoController::class);

    Route::prefix('notificacoes')->middleware('auth')->group(function () {
        Route::get('/', [NotificacaoController::class, 'index'])->name('notificacoes.index');
        Route::post('/marcar/{id}', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcar');
        Route::post('/marcar-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.marcarTodas');
        Route::delete('/limpar-todas', [NotificacaoController::class, 'limparTodas'])->name('notificacoes.limparTodas');
        Route::delete('/{id}', [NotificacaoController::class, 'destroy'])->name('notificacoes.destroy');
    });

    Route::post('/rolagens', [RolagemController::class, 'store'])->name('rolagens.store');
    Route::get('/rolagens/historico', [RolagemController::class, 'index'])->name('rolagens.index');

    Route::post('/arquivos/upload', [ArquivoController::class, 'upload'])->name('arquivos.upload');
    Route::get('/arquivos/{arquivo}', [ArquivoController::class, 'download'])->name('arquivos.download');
});

Route::middleware(['auth'])->group(function () {

    // Exibe a lista de amigos e chats privados
    Route::get('/chat/privado', [ChatPrivadoController::class, 'index'])->name('chat.privado.index');

    // Exibe o chat privado entre dois usuários
    Route::get('/chat-privado/{user}', [ChatPrivadoController::class, 'mostrar'])->name('chat.privado.mostrar');

    // Envia uma nova mensagem no chat privado
    Route::post('/chat-privado/{chat}/mensagem', [ChatPrivadoController::class, 'store'])->name('chat.privado.store');
});
