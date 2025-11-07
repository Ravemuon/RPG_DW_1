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

// ====================
// PÁGINA INICIAL
// ====================
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth');

// ====================
// AUTENTICAÇÃO
// ====================
Route::get('/login', [UserController::class, 'loginForm'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.perform');

Route::get('/register', [UserController::class, 'registerForm'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.perform');

Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// ====================
// ROTAS AUTENTICADAS
// ====================
Route::middleware(['auth'])->group(function () {

    // ====================
    // Usuário
    // ====================
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/perfil', [UserController::class, 'perfil'])->name('usuarios.perfil');
        Route::get('/editar', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::post('/update', [UserController::class, 'update'])->name('usuarios.update');
        Route::put('/tema', [UserController::class, 'atualizarTema'])->name('usuarios.tema.update');
        Route::post('/upload/{tipo}', [UserController::class, 'uploadImagem'])->name('usuarios.uploadImagem');

        Route::get('/amigos', [UserController::class, 'friends'])->name('usuarios.amigos');
        Route::get('/pendentes', [UserController::class, 'pendingRequests'])->name('usuarios.pendentes');

        Route::get('/{usuario}', [UserController::class, 'show'])->name('usuarios.show');
    });

    // ====================
    // Amizades
    // ====================
    Route::prefix('amizades')->group(function () {
        Route::get('/', [AmizadeController::class, 'index'])->name('amigos.index');
        Route::get('/amigos', [AmizadeController::class, 'amigos'])->name('amigos.amigos');

        Route::post('/adicionar/{id}', [AmizadeController::class, 'adicionar'])->name('amigos.adicionar');
        Route::delete('/cancelar/{id}', [AmizadeController::class, 'cancelar'])->name('amigos.cancelar');
        Route::post('/aceitar/{id}', [AmizadeController::class, 'aceitar'])->name('amigos.aceitar');
        Route::delete('/rejeitar/{id}', [AmizadeController::class, 'rejeitar'])->name('amigos.rejeitar');
        Route::delete('/remover/{id}', [AmizadeController::class, 'remover'])->name('amigos.remover');
    });

    // ====================
    // Campanhas
    // ====================
    Route::prefix('campanhas')->group(function () {
        Route::get('/', [CampanhaController::class, 'index'])->name('campanhas.index');
        Route::get('/todas', [CampanhaController::class, 'todas'])->name('campanhas.todas'); // Corrigido
        Route::get('/create', [CampanhaController::class, 'create'])->name('campanhas.create');
        Route::post('/', [CampanhaController::class, 'store'])->name('campanhas.store');
        Route::get('/{campanha}', [CampanhaController::class, 'show'])->name('campanhas.show');
        Route::get('/{campanha}/edit', [CampanhaController::class, 'edit'])->name('campanhas.edit');
        Route::put('/{campanha}', [CampanhaController::class, 'update'])->name('campanhas.update');
        Route::delete('/{campanha}', [CampanhaController::class, 'destroy'])->name('campanhas.destroy');

        Route::post('/{campanha}/entrar', [CampanhaController::class, 'entrar'])->name('campanhas.entrar'); // Corrigido
        Route::post('/{campanha}/usuarios/add', [CampanhaUsuarioController::class, 'store'])->name('campanhas.usuarios.add');
        Route::delete('/{campanha}/usuarios/{usuario}', [CampanhaUsuarioController::class, 'destroy'])->name('campanhas.usuarios.remove');

        // ====================
        // Sessões da campanha
        // ====================
        Route::get('/{campanha}/sessoes', [SessaoController::class, 'index'])->name('sessoes.index');
       Route::get('/{campanha}/sessoes/create', [SessaoController::class, 'create'])->name('sessoes.create');
        Route::post('/{campanha}/sessoes', [SessaoController::class, 'store'])->name('campanhas.salvar-sessao');
        Route::get('/sessoes/{sessao}', [SessaoController::class, 'show'])->name('sessoes.show');
        Route::get('/sessoes/{sessao}/edit', [SessaoController::class, 'edit'])->name('sessoes.edit');
        Route::put('/sessoes/{sessao}', [SessaoController::class, 'update'])->name('sessoes.update');
        Route::delete('/sessoes/{sessao}', [SessaoController::class, 'destroy'])->name('sessoes.destroy');
        Route::post('/sessoes/{sessao}/personagem', [SessaoController::class, 'adicionarPersonagem'])->name('sessoes.adicionar-personagem');
        Route::put('/sessoes/{sessao}/personagem/{personagem}', [SessaoController::class, 'atualizarPersonagem'])->name('sessoes.atualizar-personagem');
        Route::get('/sessoes/{sessao}/pdf', [SessaoController::class, 'exportarPdf'])->name('sessoes.exportar-pdf');

        // ====================
        // Chat da campanha
        // ====================
        Route::get('/{campanha}/chat', [ChatController::class, 'index'])->name('campanhas.chat');
        Route::post('/{campanha}/chat/enviar', [ChatController::class, 'enviarMensagem'])->name('campanhas.chat.enviar');
        Route::put('/chat/mensagem/{mensagem}', [ChatController::class, 'atualizarMensagem'])->name('chat.mensagem.update');
        Route::delete('/chat/mensagem/{mensagem}', [ChatController::class, 'excluirMensagem'])->name('chat.mensagem.destroy');
    });

    // ====================
    // Personagens
    // ====================
    Route::resource('personagens', PersonagemController::class);
    Route::post('/personagens/{personagem}/origens/add', [PersonagemOrigemController::class, 'store'])->name('personagens.origens.add');
    Route::delete('/personagens/{personagem}/origens/{origem}', [PersonagemOrigemController::class, 'destroy'])->name('personagens.origens.remove');
    Route::post('/personagens/{personagem}/pericias/add', [PersonagemPericiaController::class, 'store'])->name('personagens.pericias.add');
    Route::delete('/personagens/{personagem}/pericias/{pericia}', [PersonagemPericiaController::class, 'destroy'])->name('personagens.pericias.remove');

    // ====================
    // Classes, Origens, Perícias, Sistemas
    // ====================
    Route::resource('classes', ClasseController::class);
    Route::resource('origens', OrigemController::class);
    Route::resource('pericias', PericiaController::class);
    Route::resource('sistemas', SistemaController::class);

    // ====================
    // Missões
    // ====================
    Route::resource('missoes', MissaoController::class);

    // ====================
    // Chat e Mensagens gerais
    // ====================
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/{chat}/mensagem', [ChatController::class, 'enviarMensagem'])->name('mensagem.store');
        Route::get('/{chat}/buscar', [ChatController::class, 'buscarMensagens'])->name('chat.buscar');
    });

    // ====================
    // Notificações
    // ====================
    Route::get('/notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes.index');
    Route::post('/notificacoes/{notificacao}/lida', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.lida');
    Route::post('/notificacoes/marcar-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.lidas.todas');
    Route::delete('/notificacoes/{notificacao}', [NotificacaoController::class, 'destroy'])->name('notificacoes.destroy');

    // ====================
    // Rolagens
    // ====================
    Route::post('/rolagens', [RolagemController::class, 'store'])->name('rolagens.store');
    Route::get('/rolagens/historico', [RolagemController::class, 'index'])->name('rolagens.index');

    // ====================
    // Arquivos
    // ====================
    Route::post('/arquivos/upload', [ArquivoController::class, 'upload'])->name('arquivos.upload');
    Route::get('/arquivos/{arquivo}', [ArquivoController::class, 'download'])->name('arquivos.download');

    // ====================
    // Sistemas
    // ====================
    Route::get('/sistemas/catalogo', [SistemaController::class, 'listarSistemas'])->name('sistemas.catalogo');
    Route::resource('sistemas', SistemaController::class);

});
