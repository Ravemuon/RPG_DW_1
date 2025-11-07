<?php

use Illuminate\Support\Facades\Route;

// Controllers
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
    ArquivoController
};

/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth');


/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
*/
Route::get('/login', [UserController::class, 'loginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.perform');

Route::get('/register', [UserController::class, 'registerForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.perform');

Route::post('/logout', [UserController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Rotas autenticadas
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {


/*
|--------------------------------------------------------------------------
| Usuários
|--------------------------------------------------------------------------
*/
Route::prefix('usuarios')->group(function () {

    // Páginas principais
    Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('/perfil', [UserController::class, 'perfil'])->name('usuarios.perfil');
    Route::get('/editar', [UserController::class, 'edit'])->name('usuarios.edit');

    // Atualizações
    Route::post('/update', [UserController::class, 'update'])->name('usuarios.update');
    Route::put('/tema', [UserController::class, 'atualizarTema'])->name('usuarios.tema.update');
    Route::post('/upload/{tipo}', [UserController::class, 'uploadImagem'])->name('usuarios.uploadImagem');

    // Amizades dentro do usuário
    // Página principal de amigos do usuário
    Route::get('/amigos', [AmizadeController::class, 'index'])->name('amigos.index');
    Route::get('/pendentes', [AmizadeController::class, 'pendentes'])->name('usuarios.pendentes');
    Route::post('/adicionar/{id}', [AmizadeController::class, 'adicionar'])->name('amigos.adicionar');

    // Busca e perfil público
    Route::get('/procurar', [UserController::class, 'procurar'])->name('usuarios.procurar');
    Route::get('/perfil/{id}', [UserController::class, 'perfilPublico'])->name('usuarios.perfilpublico');

    // Por último → rota dinâmica
    Route::get('/{usuario}', [UserController::class, 'show'])->name('usuarios.show');

    // Usuário solicita entrada
    Route::post('campanhas/{campanha}/solicitar', [CampanhaUsuarioController::class, 'solicitarEntrada'])
        ->name('campanhas.solicitar');

    // Mestre gerencia status
    Route::post('campanhas/{campanha}/gerenciar', [CampanhaUsuarioController::class, 'gerenciar'])
        ->name('campanhas.gerenciar');

    // Mestre remove jogador
    Route::post('campanhas/{campanha}/remover', [CampanhaUsuarioController::class, 'remover'])
        ->name('campanhas.remover');

        Route::post('/campanhas/{campanha}/usuarios/adicionar', [CampanhaUsuarioController::class, 'adicionar'])
    ->name('campanhas.usuarios.adicionar')
    ->middleware(['auth']);

});


/*
|--------------------------------------------------------------------------
| Amizades
|--------------------------------------------------------------------------
*/
Route::prefix('amizades')->group(function () {

    // Listagens
    Route::get('/amigos', [AmizadeController::class, 'amigos'])->name('amizades.amigos');
    Route::get('/amigos', [App\Http\Controllers\AmizadeController::class, 'index'])->name('amigos.index');
    Route::get('/pendentes', [App\Http\Controllers\AmizadeController::class, 'pendentes'])->name('amizades.pendentes');

    // Ações (ajustadas aos métodos do teu controller)
    Route::post('/enviar/{friendId}', [AmizadeController::class, 'enviar'])->name('amigos.enviar');

    Route::post('/aceitar/{id}', [App\Http\Controllers\AmizadeController::class, 'aceitar'])->name('amizades.aceitar');
    Route::delete('/remover/{id}', [App\Http\Controllers\AmizadeController::class, 'remover'])->name('amigos.remover');
    Route::post('/amizades/aceitar/{id}', [AmizadeController::class, 'aceitar'])->name('amizades.aceitar');
    Route::post('/adicionar/{id}', [App\Http\Controllers\AmizadeController::class, 'adicionar'])->name('amigos.adicionar');
});


    /*
    |--------------------------------------------------------------------------
    | Campanhas
    |--------------------------------------------------------------------------
    */
    Route::prefix('campanhas')->group(function () {

        // Rotas Estáticas (sem parâmetros)
        Route::get('/', [CampanhaController::class, 'index'])->name('campanhas.index');
        Route::get('/todas', [CampanhaController::class, 'todas'])->name('campanhas.todas');
        Route::get('/create', [CampanhaController::class, 'create'])->name('campanhas.create');
        Route::post('/', [CampanhaController::class, 'store'])->name('campanhas.store');

        // Minhas campanhas (DEVE VIR AQUI para evitar a colisão com '/{campanha}')
        Route::get('/minhas', [CampanhaController::class, 'minhas'])
            ->name('campanhas.minhas');

        // As outras rotas estáticas ou mais longas que dependem do parâmetro {campanha}
        Route::get('/{campanha}/edit', [CampanhaController::class, 'edit'])->name('campanhas.edit');
        // ... outras rotas como entrar, adicionar/remover usuário

        // Rota Genérica (DEVE SER A ÚLTIMA GET/PUT/DELETE que usa apenas o parâmetro)
        Route::get('/{campanha}', [CampanhaController::class, 'show'])->name('campanhas.show');
        Route::put('/{campanha}', [CampanhaController::class, 'update'])->name('campanhas.update');
        Route::delete('/{campanha}', [CampanhaController::class, 'destroy'])->name('campanhas.destroy');
        // Usuários da campanha
        Route::post('/{campanha}/entrar', [CampanhaController::class, 'entrar'])->name('campanhas.entrar');
        Route::post('/{campanha}/usuarios/add', [CampanhaUsuarioController::class, 'store'])->name('campanhas.usuarios.add');
        Route::delete('/{campanha}/usuarios/{usuario}', [CampanhaUsuarioController::class, 'destroy'])->name('campanhas.usuarios.remove');


        /*
        |--------------------------------------------------------------------------
        | Sessões da campanha
        |--------------------------------------------------------------------------
        */
        Route::prefix('campanhas')->group(function () {

            // Resource duplicado (mantido)
            Route::resource('/', CampanhaController::class);

            // Sessões
            Route::get('/{campanha}/sessoes', [SessaoController::class, 'index'])->name('sessoes.index');
            Route::get('/{campanha}/sessoes/criar', [SessaoController::class, 'create'])->name('sessoes.create');
            Route::post('/{campanha}/sessoes', [SessaoController::class, 'store'])->name('sessoes.store');

            Route::get('/sessoes/{sessao}', [SessaoController::class, 'show'])->name('sessoes.show');
            Route::get('/sessoes/{sessao}/editar', [SessaoController::class, 'edit'])->name('sessoes.edit');
            Route::put('/sessoes/{sessao}', [SessaoController::class, 'update'])->name('sessoes.update');
            Route::delete('/{campanha}/sessoes/{sessao}', [SessaoController::class, 'destroy'])->name('sessoes.destroy');

            // Extras
            Route::post('/sessoes/{sessao}/adicionar-personagem', [SessaoController::class, 'adicionarPersonagem'])->name('sessoes.adicionar-personagem');
            Route::put('/sessoes/{sessao}/personagem/{personagem}', [SessaoController::class, 'atualizarPersonagem'])->name('sessoes.atualizar-personagem');
            Route::get('/sessoes/{sessao}/exportar-pdf', [SessaoController::class, 'exportarPdf'])->name('sessoes.exportar-pdf');
        });

        /*
        |--------------------------------------------------------------------------
        | Chat da campanha
        |--------------------------------------------------------------------------
        */
        Route::get('/{campanha}/chat', [ChatController::class, 'index'])->name('campanhas.chat');
        Route::post('/{campanha}/chat/enviar', [ChatController::class, 'enviarMensagem'])->name('campanhas.chat.enviar');
        Route::put('/chat/mensagem/{mensagem}', [ChatController::class, 'atualizarMensagem'])->name('chat.mensagem.update');
        Route::delete('/chat/mensagem/{mensagem}', [ChatController::class, 'excluirMensagem'])->name('chat.mensagem.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Personagens
    |--------------------------------------------------------------------------
    */
    Route::resource('personagens', PersonagemController::class);
    Route::post('/personagens/{personagem}/origens/add', [PersonagemOrigemController::class, 'store'])->name('personagens.origens.add');
    Route::delete('/personagens/{personagem}/origens/{origem}', [PersonagemOrigemController::class, 'destroy'])->name('personagens.origens.remove');
    Route::post('/personagens/{personagem}/pericias/add', [PersonagemPericiaController::class, 'store'])->name('personagens.pericias.add');
    Route::delete('/personagens/{personagem}/pericias/{pericia}', [PersonagemPericiaController::class, 'destroy'])->name('personagens.pericias.remove');


    /*
    |--------------------------------------------------------------------------
    | Classes, Origens, Perícias, Sistemas
    |--------------------------------------------------------------------------
    */
    Route::resource('classes', ClasseController::class);
    Route::resource('origens', OrigemController::class);
    Route::resource('pericias', PericiaController::class);
    Route::resource('sistemas', SistemaController::class);

    // Catálogo extra
    Route::get('/sistemas/catalogo', [SistemaController::class, 'listarSistemas'])->name('sistemas.catalogo');


    /*
    |--------------------------------------------------------------------------
    | Missões
    |--------------------------------------------------------------------------
    */
    Route::resource('missoes', MissaoController::class);


    /*
    |--------------------------------------------------------------------------
    | Chat geral
    |--------------------------------------------------------------------------
    */
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/{chat}/mensagem', [ChatController::class, 'enviarMensagem'])->name('mensagem.store');
        Route::get('/{chat}/buscar', [ChatController::class, 'buscarMensagens'])->name('chat.buscar');
    });


    /*
    |--------------------------------------------------------------------------
    | Notificações
    |--------------------------------------------------------------------------
    */
    Route::get('/notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes.index');
    Route::post('/notificacoes/{notificacao}/lida', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.lida');
    Route::post('/notificacoes/marcar-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.lidas.todas');
    Route::delete('/notificacoes/{notificacao}', [NotificacaoController::class, 'destroy'])->name('notificacoes.destroy');
    Route::prefix('notificacoes')->group(function () {
        // Marca uma notificação como lida
        Route::post('/marcar/{id}', [NotificacaoController::class, 'marcarComoLida'])
            ->name('notificacoes.marcar');
        // Listar notificações (opcional)
        Route::get('/', [NotificacaoController::class, 'index'])->name('notificacoes.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Rolagens
    |--------------------------------------------------------------------------
    */
    Route::post('/rolagens', [RolagemController::class, 'store'])->name('rolagens.store');
    Route::get('/rolagens/historico', [RolagemController::class, 'index'])->name('rolagens.index');


    /*
    |--------------------------------------------------------------------------
    | Arquivos
    |--------------------------------------------------------------------------
    */
    Route::post('/arquivos/upload', [ArquivoController::class, 'upload'])->name('arquivos.upload');
    Route::get('/arquivos/{arquivo}', [ArquivoController::class, 'download'])->name('arquivos.download');

});
