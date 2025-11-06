<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\{
    ClasseController,
    PersonagemController,
    MissaoController,
    UsuarioController,
    CampanhaController
};

// Página inicial (verifica login)
Route::get('/', function () {
    return auth()->check() ? view('home') : redirect()->route('login');
})->name('home');

// 🔹 LOGIN
Route::get('/login', [UsuarioController::class, 'loginForm'])->name('login');
Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/logout', [UsuarioController::class, 'logout'])->name('logout');

// 🔹 CADASTRO
Route::get('/register', [UsuarioController::class, 'registerForm'])->name('register');
Route::post('/register', [UsuarioController::class, 'register']);

// 🔹 ROTAS AUTENTICADAS
Route::middleware('auth')->group(function () {

    // PERFIL
    Route::get('/perfil', [UsuarioController::class, 'perfil'])->name('perfil');

    // CLASSES
    Route::resource('classes', ClasseController::class);

    // PERSONAGENS
    Route::resource('personagens', PersonagemController::class);
    Route::post('personagens/npc', [PersonagemController::class, 'storeNPC'])->name('personagens.npc');

    // MISSÕES
    Route::resource('missoes', MissaoController::class);

    // CAMPANHAS
    Route::resource('campanhas', CampanhaController::class);

    // Jogadores de uma campanha
    Route::prefix('campanhas/{campanha}')->group(function () {
        Route::post('adicionar-jogador', [CampanhaController::class, 'adicionarJogador'])->name('campanhas.adicionar-jogador');
        Route::post('remover-jogador', [CampanhaController::class, 'removerJogador'])->name('campanhas.remover-jogador');

        // SESSÕES
        Route::get('sessoes', [CampanhaController::class, 'listarSessoes'])->name('campanhas.sessoes');
        Route::post('sessoes', [CampanhaController::class, 'criarSessao'])->name('campanhas.criar-sessao');
        Route::delete('sessoes/{sessao}', [CampanhaController::class, 'removerSessao'])->name('campanhas.remover-sessao');

        // RELATÓRIO
        Route::get('relatorio', [CampanhaController::class, 'gerarRelatorio'])->name('campanhas.relatorio');

        // UPLOAD DE ARQUIVOS
        Route::post('upload', [ArquivoController::class, 'upload'])->name('campanhas.upload');
    });
});
