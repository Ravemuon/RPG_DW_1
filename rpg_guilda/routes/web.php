<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    UserController,
    AmizadeController,
    CampanhaController,
    CampanhaUsuarioController,
    PersonagemController,
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

Route::middleware(['auth'])->prefix('personagens')->name('personagens.')->group(function () {

    // -----------------------------
    // CRUD Básico
    // -----------------------------
    Route::get('/', [PersonagemController::class, 'index'])->name('index');
    Route::get('/create', [PersonagemController::class, 'create'])->name('create');
    Route::post('/', [PersonagemController::class, 'store'])->name('store');
    Route::get('/{personagem}', [PersonagemController::class, 'show'])->name('show');
    Route::get('/{personagem}/edit', [PersonagemController::class, 'edit'])->name('edit');
    Route::put('/{personagem}', [PersonagemController::class, 'update'])->name('update');
    Route::delete('/{personagem}', [PersonagemController::class, 'destroy'])->name('destroy');

    // -----------------------------
    // Recursos Avançados
    // -----------------------------
    Route::prefix('{personagem}')->group(function () {
        // Perícias
        Route::get('/pericias', [PersonagemController::class, 'pericias'])->name('pericias');
        Route::post('/pericias', [PersonagemController::class, 'updatePericias'])->name('pericias.update');

        // XP e Nível
        Route::post('/adicionar-xp', [PersonagemController::class, 'adicionarXp'])->name('adicionar-xp');
        Route::get('/subir-nivel', [PersonagemController::class, 'subirNivel'])->name('subir-nivel');
        Route::post('/subir-nivel', [PersonagemController::class, 'processarSubidaNivel'])->name('subir-nivel.processar');

        // Exportação
        Route::get('/exportar-pdf', [PersonagemController::class, 'exportarPdf'])->name('exportar-pdf');
        Route::get('/exportar-json', [PersonagemController::class, 'exportarJson'])->name('exportar-json');

        // Restauração e Lixeira
        Route::post('/restaurar', [PersonagemController::class, 'restore'])->name('restore')->withTrashed();
        Route::delete('/forcar-exclusao', [PersonagemController::class, 'forceDelete'])->name('force-delete')->withTrashed();

        // Duplicação
        Route::post('/duplicar', [PersonagemController::class, 'duplicar'])->name('duplicar');

        // Toggle Status
        Route::post('/toggle-ativo', [PersonagemController::class, 'toggleAtivo'])->name('toggle.ativo');
        Route::post('/toggle-favorito', [PersonagemController::class, 'toggleFavorito'])->name('toggle.favorito');

        // Vida e Recursos
        Route::post('/atualizar-pv', [PersonagemController::class, 'atualizarPontosVida'])->name('atualizar-pv');
        Route::post('/usar-recurso', [PersonagemController::class, 'usarRecurso'])->name('usar-recurso');

        // Upload de Imagem/Token
        Route::post('/upload-imagem', [PersonagemController::class, 'uploadImagem'])->name('upload-imagem');
        Route::post('/upload-token', [PersonagemController::class, 'uploadToken'])->name('upload-token');
        Route::delete('/remover-imagem', [PersonagemController::class, 'removerImagem'])->name('remover-imagem');

        // Notas
        Route::post('/adicionar-nota', [PersonagemController::class, 'adicionarNota'])->name('adicionar-nota');
        Route::put('/nota/{notaId}', [PersonagemController::class, 'atualizarNota'])->name('atualizar-nota');
        Route::delete('/nota/{notaId}', [PersonagemController::class, 'removerNota'])->name('remover-nota');

        // Inventário
        Route::get('/inventario', [PersonagemController::class, 'inventario'])->name('inventario');
        Route::post('/inventario/adicionar-item', [PersonagemController::class, 'adicionarItemInventario'])->name('inventario.adicionar');
        Route::put('/inventario/{itemIndex}', [PersonagemController::class, 'atualizarItemInventario'])->name('inventario.atualizar');
        Route::delete('/inventario/{itemIndex}', [PersonagemController::class, 'removerItemInventario'])->name('inventario.remover');
        Route::post('/equipar-item', [PersonagemController::class, 'equiparItem'])->name('equipar-item');

        // Magias
        Route::get('/magias', [PersonagemController::class, 'magias'])->name('magias');
        Route::post('/magias/adicionar', [PersonagemController::class, 'adicionarMagia'])->name('magias.adicionar');
        Route::delete('/magias/{magiaIndex}', [PersonagemController::class, 'removerMagia'])->name('magias.remover');

        // Compartilhamento
        Route::get('/compartilhar', [PersonagemController::class, 'compartilhar'])->name('compartilhar');
        Route::post('/gerar-link', [PersonagemController::class, 'gerarLinkCompartilhamento'])->name('gerar-link');
        Route::delete('/revogar-link', [PersonagemController::class, 'revogarLinkCompartilhamento'])->name('revogar-link');

        // API interna
        Route::get('/api/atributos', [PersonagemController::class, 'apiAtributos'])->name('api.atributos');
        Route::get('/api/pericias', [PersonagemController::class, 'apiPericias'])->name('api.pericias');
        Route::get('/api/recursos', [PersonagemController::class, 'apiRecursos'])->name('api.recursos');
    });

    // Lixeira
    Route::get('/lixeira', [PersonagemController::class, 'lixeira'])->name('lixeira');
});

// Rotas públicas
Route::prefix('personagens')->group(function () {
    Route::get('/public/{hash}', [PersonagemController::class, 'viewPublic'])->name('personagens.public.view');
    Route::get('/public/{hash}/pdf', [PersonagemController::class, 'exportarPublicPdf'])->name('personagens.public.pdf');
});

// Rotas API AJAX
Route::middleware(['auth', 'ajax'])->prefix('api/personagens')->name('api.personagens.')->group(function () {
    Route::get('/campanha/{campanha}/sistema', [PersonagemController::class, 'getSistemaByCampanha'])->name('sistema-by-campanha');
    Route::get('/sistema/{sistema}/racas', [PersonagemController::class, 'getRacasBySistema'])->name('racas-by-sistema');
    Route::get('/sistema/{sistema}/classes', [PersonagemController::class, 'getClassesBySistema'])->name('classes-by-sistema');
    Route::get('/sistema/{sistema}/origens', [PersonagemController::class, 'getOrigensBySistema'])->name('origens-by-sistema');
    Route::get('/sistema/{sistema}/pericias', [PersonagemController::class, 'getPericiasBySistema'])->name('pericias-by-sistema');
    Route::get('/classe/{classe}/detalhes', [PersonagemController::class, 'getClasseDetalhes'])->name('classe-detalhes');
    Route::get('/raca/{raca}/detalhes', [PersonagemController::class, 'getRacaDetalhes'])->name('raca-detalhes');
    Route::get('/origem/{origem}/detalhes', [PersonagemController::class, 'getOrigemDetalhes'])->name('origem-detalhes');

    Route::get('/busca', [PersonagemController::class, 'busca'])->name('busca');
    Route::get('/autocomplete', [PersonagemController::class, 'autocomplete'])->name('autocomplete');
    Route::post('/validar-nome', [PersonagemController::class, 'validarNome'])->name('validar-nome');
    Route::post('/validar-atributos', [PersonagemController::class, 'validarAtributos'])->name('validar-atributos');
});

// Middleware para bind com Trashed
Route::bind('personagem', function ($value) {
    return \App\Models\Personagem::withTrashed()->findOrFail($value);
});
