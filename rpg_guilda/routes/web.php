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
    ChatController,
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

// Rotas protegidas
Route::middleware(['auth'])->group(function () {

    /*------------------------------------
    | Usuários
    ------------------------------------*/
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/perfil', [UserController::class, 'perfil'])->name('usuarios.perfil');
        Route::get('/editar', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::post('/update', [UserController::class, 'update'])->name('usuarios.update');
        Route::put('/tema', [UserController::class, 'atualizarTema'])->name('usuarios.tema.update');
        Route::post('/upload/{tipo}', [UserController::class, 'uploadImagem'])->name('usuarios.uploadImagem');

        // Amizades
        Route::get('/amigos', [AmizadeController::class, 'index'])->name('amigos.index');
        Route::get('/pendentes', [AmizadeController::class, 'pendentes'])->name('usuarios.pendentes');
        Route::post('/adicionar/{id}', [AmizadeController::class, 'adicionar'])->name('amigos.adicionar');
        Route::post('/aceitar/{id}', [AmizadeController::class, 'aceitar'])->name('amizades.aceitar');
        Route::delete('/remover/{id}', [AmizadeController::class, 'remover'])->name('amizades.remover');

        // Perfis públicos
        Route::get('/procurar', [UserController::class, 'procurar'])->name('usuarios.procurar');
        Route::get('/perfil/{id}', [UserController::class, 'perfilPublico'])->name('usuarios.perfilpublico');

        // Usuário genérico
        Route::get('/{usuario}', [UserController::class, 'show'])->name('usuarios.show');
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

        // Gerenciamento de usuários e amigos (todas as rotas agora via CampanhaController)
        Route::middleware(['auth'])->group(function () {
            Route::post('/{campanha}/usuarios/adicionar-ajax', [CampanhaUsuarioController::class, 'adicionarAjax'])->name('campanhas.usuarios.adicionar.ajax');
            Route::post('/{campanha}/entrar', [CampanhaController::class, 'solicitarEntrada'])->name('campanhas.solicitar');
            Route::post('/{campanha}/gerenciar', [CampanhaController::class, 'gerenciarUsuarios'])->name('campanhas.usuarios.gerenciar');
            Route::post('/{campanha}/usuarios/adicionar', [CampanhaController::class, 'adicionarAmigo'])->name('campanhas.usuarios.adicionar');

            // Convidar amigos
            Route::get('/{campanha}/convidar', [CampanhaController::class, 'convidar'])->name('campanhas.convidar');
            Route::post('/{campanha}/convidar', [CampanhaController::class, 'enviarConvites'])->name('campanhas.convidar.enviar');
        });

        // Sessões
        Route::get('/{campanha}/sessoes', [SessaoController::class, 'index'])->name('sessoes.index');
        Route::get('/{campanha}/sessoes/criar', [SessaoController::class, 'create'])->name('sessoes.create');
        Route::post('/{campanha}/sessoes', [SessaoController::class, 'store'])->name('sessoes.store');
        Route::get('/sessoes/{sessao}', [SessaoController::class, 'show'])->name('sessoes.show');
        Route::get('/sessoes/{sessao}/editar', [SessaoController::class, 'edit'])->name('sessoes.edit');
        Route::put('/sessoes/{sessao}', [SessaoController::class, 'update'])->name('sessoes.update');
        Route::delete('/{campanha}/sessoes/{sessao}', [SessaoController::class, 'destroy'])->name('sessoes.destroy');
        Route::post('/sessoes/{sessao}/adicionar-personagem', [SessaoController::class, 'adicionarPersonagem'])->name('sessoes.adicionar-personagem');
        Route::put('/sessoes/{sessao}/personagem/{personagem}', [SessaoController::class, 'atualizarPersonagem'])->name('sessoes.atualizar-personagem');
        Route::get('/sessoes/{sessao}/exportar-pdf', [SessaoController::class, 'exportarPdf'])->name('sessoes.exportar-pdf');

        // Chat
        Route::get('/{campanha}/chat', [ChatController::class, 'index'])->name('campanhas.chat');
        Route::post('/{campanha}/chat/enviar', [ChatController::class, 'enviarMensagem'])->name('campanhas.chat.enviar');
        Route::put('/chat/mensagem/{mensagem}', [ChatController::class, 'atualizarMensagem'])->name('chat.mensagem.update');
        Route::delete('/chat/mensagem/{mensagem}', [ChatController::class, 'excluirMensagem'])->name('chat.mensagem.destroy');

    });

    /*------------------------------------
    | Personagens
    ------------------------------------*/
    Route::resource('personagens', PersonagemController::class);
    Route::post('/personagens/{personagem}/origens/add', [PersonagemOrigemController::class, 'store'])->name('personagens.origens.add');
    Route::delete('/personagens/{personagem}/origens/{origem}', [PersonagemOrigemController::class, 'destroy'])->name('personagens.origens.remove');
    Route::post('/personagens/{personagem}/pericias/add', [PersonagemPericiaController::class, 'store'])->name('personagens.pericias.add');
    Route::delete('/personagens/{personagem}/pericias/{pericia}', [PersonagemPericiaController::class, 'destroy'])->name('personagens.pericias.remove');

    /*------------------------------------
    | Sistemas
    ------------------------------------*/
    Route::prefix('sistemas')->name('sistemas.')->group(function () {
        // PDF primeiro, para não conflitar com {sistema}
        Route::get('/exportar-pdf', [SistemaController::class, 'exportarPdf'])->name('exportar-pdf');
        Route::get('/{sistema}/exportar-pdf', [SistemaController::class, 'visualizarPdf'])->name('exportar-pdf-unico');


        // CRUD do sistema
        Route::get('/', [SistemaController::class, 'index'])->name('index');
        Route::get('/create', [SistemaController::class, 'create'])->name('create');
        Route::post('/store', [SistemaController::class, 'store'])->name('store');
        Route::get('/{sistema}', [SistemaController::class, 'show'])->name('show');
        Route::get('/{sistema}/edit', [SistemaController::class, 'edit'])->name('edit');
        Route::put('/{sistema}', [SistemaController::class, 'update'])->name('update');
        Route::delete('/{sistema}', [SistemaController::class, 'destroy'])->name('destroy');

        // Raças
        Route::get('/{sistema}/racas', [RacaController::class, 'index'])->name('sistemas.racas.index');
        Route::get('/{sistema}/racas/create', [RacaController::class, 'create'])->name('sistemas.racas.create');
        Route::post('/{sistema}/racas', [RacaController::class, 'store'])->name('sistemas.racas.store');
        Route::get('/racas/{raca}/edit', [RacaController::class, 'edit'])->name('sistemas.racas.edit');
        Route::put('/racas/{raca}', [RacaController::class, 'update'])->name('sistemas.racas.update');
        Route::delete('/racas/{raca}', [RacaController::class, 'destroy'])->name('sistemas.racas.destroy');

        // Listagens específicas
        Route::get('/{sistema}/classes', [SistemaController::class, 'classes'])->name('classes');
        Route::get('/{sistema}/origens', [SistemaController::class, 'origens'])->name('origens');
        Route::get('/{sistema}/racas', [SistemaController::class, 'racas'])->name('racas');
        Route::get('/{sistema}/pericias', [SistemaController::class, 'pericias'])->name('pericias');

        // Recursos CRUD
        Route::resource('sistemas.classes', ClasseController::class)->shallow();
        Route::resource('sistemas.racas', RacaController::class)->shallow();
        Route::resource('sistemas.origens', OrigemController::class)->shallow();
        Route::resource('sistemas.pericias', PericiaController::class)->shallow();
    });


    /*------------------------------------
    | Missões, Chat Geral, Notificações, Rolagens, Arquivos
    ------------------------------------*/
    Route::resource('missoes', MissaoController::class);
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/{chat}/mensagem', [ChatController::class, 'enviarMensagem'])->name('mensagem.store');
        Route::get('/{chat}/buscar', [ChatController::class, 'buscarMensagens'])->name('chat.buscar');
    });

    Route::prefix('notificacoes')->group(function () {
        Route::get('/', [NotificacaoController::class, 'index'])->name('notificacoes.index');
        Route::post('/marcar/{id}', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcar');
        Route::post('/marcar-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.lidas.todas');
        Route::delete('/{notificacao}', [NotificacaoController::class, 'destroy'])->name('notificacoes.destroy');
    });

    Route::post('/rolagens', [RolagemController::class, 'store'])->name('rolagens.store');
    Route::get('/rolagens/historico', [RolagemController::class, 'index'])->name('rolagens.index');

    Route::post('/arquivos/upload', [ArquivoController::class, 'upload'])->name('arquivos.upload');
    Route::get('/arquivos/{arquivo}', [ArquivoController::class, 'download'])->name('arquivos.download');
});
