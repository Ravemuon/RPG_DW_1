<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AmizadeController,
    CampanhaController,
    UsuarioController,
    SistemaController,
    NotificacaoController,
    CampanhaUsuarioController,
    ClasseController,
    PersonagemController,
    MissaoController,
    ArquivoController
};

// -----------------------------
// ROTAS PÚBLICAS
// -----------------------------
Route::get('/', fn() => view('home'))->name('home');
Route::get('/portal', [HomeController::class, 'index'])->name('portal')->middleware('auth');


Route::get('/campanhas', [CampanhaController::class, 'index'])->name('campanhas.index');
Route::get('/campanhas-todas', [CampanhaController::class, 'listarTodas'])->name('campanhas.todas');
Route::get('/sistemas', [SistemaController::class, 'index'])->name('sistemas.index');

// Login / Registro
Route::get('/login', [UsuarioController::class, 'loginForm'])->name('login');
Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/logout', [UsuarioController::class, 'logout'])->name('logout');

Route::get('/register', [UsuarioController::class, 'registerForm'])->name('register');
Route::post('/register', [UsuarioController::class, 'register']);

// -----------------------------
// ROTAS AUTENTICADAS
// -----------------------------
Route::middleware('auth')->group(function () {

    // PERFIL E UPLOADS
    Route::prefix('usuarios')->group(function () {
        Route::get('/perfil', [UsuarioController::class, 'perfil'])->name('perfil');
        Route::post('{id}/upload-banner', [UsuarioController::class, 'uploadBanner'])->name('usuarios.uploadBanner');
        Route::post('{id}/upload-perfil', [UsuarioController::class, 'uploadPerfil'])->name('usuarios.uploadPerfil');
        Route::get('{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    });

    // NOTIFICAÇÕES
    Route::prefix('notificacoes')->group(function () {
        Route::get('/', [NotificacaoController::class, 'index'])->name('notificacoes.index');
        Route::put('/{id}/marcar', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcar');
        Route::delete('/{id}', [NotificacaoController::class, 'destroy'])->name('notificacoes.destroy');
    });

    // AMIZADES
    Route::prefix('amizade')->group(function () {
        Route::post('enviar/{friend_id}', [AmizadeController::class, 'enviarSolicitacao'])->name('amizade.enviar');
        Route::post('aceitar/{id}', [AmizadeController::class, 'aceitarSolicitacao'])->name('amizade.aceitar');
        Route::post('recusar/{id}', [AmizadeController::class, 'recusarSolicitacao'])->name('amizade.recusar');
        Route::middleware('auth')->group(function () {
            Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        });

    });

    // CAMPANHAS - qualquer usuário logado pode criar ou entrar
    Route::get('/campanhas/create', [CampanhaController::class, 'create'])->name('campanhas.create');
    Route::post('/campanhas', [CampanhaController::class, 'store'])->name('campanhas.store');
    Route::post('/campanhas/{id}/entrar', [CampanhaController::class, 'entrar'])->name('campanhas.entrar');
    Route::get('/campanhas/{id}', [CampanhaController::class, 'show'])->name('campanhas.show');

    // ROTAS DE ADMIN/MESTRE
    Route::middleware('can:admin-access')->group(function () {

        // CLASSES, PERSONAGENS, MISSÕES
        Route::resource('classes', ClasseController::class);
        Route::resource('personagens', PersonagemController::class);
        Route::post('personagens/npc', [PersonagemController::class, 'storeNPC'])->name('personagens.npc');
        Route::resource('missoes', MissaoController::class);

        // CAMPANHAS - editar/deletar
        Route::resource('campanhas', CampanhaController::class)->except(['create', 'store', 'show']);

        // Jogadores, sessões e uploads dentro de uma campanha
        Route::prefix('campanhas/{campanha}')->group(function () {
            Route::post('adicionar-jogador', [CampanhaUsuarioController::class, 'adicionar'])->name('campanhas.adicionar-jogador');
            Route::post('remover-jogador', [CampanhaUsuarioController::class, 'remover'])->name('campanhas.remover-jogador');
            Route::get('sessoes', [CampanhaController::class, 'listarSessoes'])->name('campanhas.sessoes');
            Route::post('sessoes', [CampanhaController::class, 'criarSessao'])->name('campanhas.criar-sessao');
            Route::delete('sessoes/{sessao}', [CampanhaController::class, 'removerSessao'])->name('campanhas.remover-sessao');
            Route::get('relatorio', [CampanhaController::class, 'gerarRelatorio'])->name('campanhas.relatorio');
            Route::post('upload', [ArquivoController::class, 'upload'])->name('campanhas.upload');
        });
    });


});
