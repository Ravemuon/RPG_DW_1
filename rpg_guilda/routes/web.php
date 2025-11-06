<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\{
    ClasseController,
    PersonagemController,
    MissaoController,
    UsuarioController,
    CampanhaController,
    SistemaController,
    NotificacaoController,
    CampanhaUsuarioController
};

// -----------------------------
// ROTAS PÚBLICAS
// -----------------------------
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/campanhas', [CampanhaController::class, 'index'])->name('campanhas.index');
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

    // Perfil do usuário
    Route::get('/perfil', [UsuarioController::class, 'perfil'])->name('perfil');
    Route::middleware('auth')->group(function () {
    // Upload do banner
    Route::post('/usuarios/{id}/upload-banner', [UsuarioController::class, 'uploadBanner'])->name('usuarios.uploadBanner');

    // Upload do perfil
    Route::post('/usuarios/{id}/upload-perfil', [UsuarioController::class, 'uploadPerfil'])->name('usuarios.uploadPerfil');

    });

    // Usuário
    Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');


    // Notificações
    Route::prefix('notificacoes')->group(function () {
        Route::get('/', [NotificacaoController::class, 'index'])->name('notificacoes.index');
        Route::put('/{id}/marcar', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcar');
        Route::delete('/{id}', [NotificacaoController::class, 'destroy'])->name('notificacoes.destroy');
    });

    // Rotas de Admin / Mestre
    Route::middleware('can:admin-access')->group(function () {

        // CLASSES
        Route::resource('classes', ClasseController::class);

        // PERSONAGENS
        Route::resource('personagens', PersonagemController::class);
        Route::post('personagens/npc', [PersonagemController::class, 'storeNPC'])->name('personagens.npc');

        // MISSÕES
        Route::resource('missoes', MissaoController::class);

        // CAMPANHAS
        Route::resource('campanhas', CampanhaController::class);

        // Jogadores, sessões e uploads dentro de uma campanha
        Route::prefix('campanhas/{campanha}')->group(function () {

            // Adicionar/Remover jogadores via pivot
            Route::post('adicionar-jogador', [CampanhaUsuarioController::class, 'adicionar'])->name('campanhas.adicionar-jogador');
            Route::post('remover-jogador', [CampanhaUsuarioController::class, 'remover'])->name('campanhas.remover-jogador');

            // Sessões
            Route::get('sessoes', [CampanhaController::class, 'listarSessoes'])->name('campanhas.sessoes');
            Route::post('sessoes', [CampanhaController::class, 'criarSessao'])->name('campanhas.criar-sessao');
            Route::delete('sessoes/{sessao}', [CampanhaController::class, 'removerSessao'])->name('campanhas.remover-sessao');

            // Relatórios e uploads
            Route::get('relatorio', [CampanhaController::class, 'gerarRelatorio'])->name('campanhas.relatorio');
            Route::post('upload', [ArquivoController::class, 'upload'])->name('campanhas.upload');
        });
    });
});
