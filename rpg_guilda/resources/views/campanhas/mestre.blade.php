@extends('layouts.app')

@section('title', "Área do Mestre - {$campanha->nome}")

@section('content')

{{-- 
    [BEST PRACTICE ALERT]
    A autorização (checar se o criador é o usuário logado) deve ser movida para 
    o Controller (usando Gates/Policies) ou para o Middleware. A View deve 
    apenas focar na apresentação.
--}}
@if($campanha->criador_id !== auth()->id())
    @php abort(403, 'Acesso Proibido. Apenas o Mestre pode acessar esta área.'); @endphp
@endif

<div class="container py-5 text-light">

    <h1 class="fw-bolder display-4 text-highlight mb-2">
        <i class="fas fa-gavel me-2"></i> {{ $campanha->nome }}
    </h1>
    <p class="text-muted fs-5 mb-4">Painel de Controle do Mestre</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <hr class="border-secondary mb-5">

    {{-- CARDS DE ESTATÍSTICAS --}}
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="card bg-secondary text-light p-3 shadow-sm border-0">
                <h6 class="text-warning mb-0"><i class="fas fa-users me-2"></i> Jogadores</h6>
                <p class="display-6 fw-bold mb-0">{{ $campanha->jogadores->count() }}</p>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card bg-secondary text-light p-3 shadow-sm border-0">
                <h6 class="text-info mb-0"><i class="fas fa-scroll me-2"></i> Missões</h6>
                <p class="display-6 fw-bold mb-0">{{ $campanha->missoes->count() }}</p>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card bg-secondary text-light p-3 shadow-sm border-0">
                <h6 class="text-success mb-0"><i class="fas fa-calendar-check me-2"></i> Sessões</h6>
                <p class="display-6 fw-bold mb-0">{{ $campanha->sessoes->count() }}</p>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card bg-secondary text-light p-3 shadow-sm border-0">
                <h6 class="text-primary mb-0"><i class="fas fa-dice-d20 me-2"></i> Sistema</h6>
                <p class="fs-4 fw-bold mb-0">{{ $campanha->sistema->nome ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    
    {{-- BOTÕES DE AÇÃO RÁPIDA --}}
    <div class="mb-5 d-flex gap-3 flex-wrap border-bottom border-secondary pb-4">
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-light rounded-pill fw-bold">
            <i class="fas fa-eye me-1"></i> Ver Campanha
        </a>

        <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-warning rounded-pill">
            <i class="fas fa-pencil-alt me-1"></i> Editar Campanha
        </a>

        <a href="{{ route('personagens.index', ['campanha' => $campanha->id]) }}" class="btn btn-secondary rounded-pill fw-bold">
            <i class="fas fa-users me-1"></i> Ver Todos os Personagens
        </a>

        <a href="{{ route('missoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-1"></i> Criar Missão
        </a>

        <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-success rounded-pill">
            <i class="fas fa-calendar-plus me-1"></i> Criar Sessão
        </a>

        <a href="{{ route('personagens.create', ['campanha' => $campanha->id]) }}" class="btn btn-info rounded-pill">
            <i class="fas fa-user-plus me-1"></i> Criar Personagem
        </a>


    </div>

    {{-- CÓDIGO DE CONVITE --}}
    @if($campanha->privada && $campanha->codigo_convite)
        <div class="card bg-dark border-info mb-5 shadow-lg">
            <div class="card-header fw-bold text-info">
                <i class="fas fa-link me-2"></i> Código de Convite (Privada)
            </div>

            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <p class="mb-3 mb-md-0 text-muted">
                    Use este código para convidar jogadores para a sua campanha:
                </p>

                <div class="d-flex align-items-center gap-3">
                    <span id="inviteCodeDisplay"
                        class="fs-4 fw-bold text-light bg-secondary px-3 py-1 rounded"
                        style="user-select:none; min-width:150px; text-align:center;">
                        ********
                    </span>

                    <button id="toggleCodeButton" class="btn btn-outline-info" data-code="{{ $campanha->codigo_convite }}">
                        <i class="fas fa-eye"></i> Mostrar
                    </button>

                    <button id="copyCodeButton" class="btn btn-info text-dark" style="display:none;">
                        <i class="fas fa-copy"></i> Copiar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PAINEL DE GESTÃO DE CONTEÚDO --}}
    <div class="row g-4 mb-5">

        {{-- MISSÕES --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-primary h-100">
                <div class="card-header fw-bold text-primary d-flex justify-content-between align-items-center">
                    <i class="fas fa-scroll me-2"></i> Missões ({{ $campanha->missoes->count() }})
                    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">Ver Todas</a>
                </div>

                <div class="card-body p-0">
                    @if($campanha->missoes->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($campanha->missoes->sortByDesc('data_criacao')->take(5) as $missao)
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light border-secondary">
                                    <div>
                                        <strong>{{ $missao->titulo }}</strong><br>
                                        <small class="text-muted">
                                            Data limite: {{ optional($missao->data_limite)->format('d/m/Y') ?? 'Sem data' }}
                                        </small>
                                    </div>

                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('missoes.show', ['campanha'=>$campanha->id, 'missao'=>$missao->id]) }}" class="btn btn-outline-primary">Ver</a>
                                        <a href="{{ route('missoes.edit', ['campanha'=>$campanha->id, 'missao'=>$missao->id]) }}" class="btn btn-outline-warning">Editar</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">
                            Nenhuma missão criada. <a href="{{ route('missoes.create', ['campanha'=>$campanha->id]) }}" class="text-primary">Crie a primeira!</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- SESSÕES --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-success h-100">
                <div class="card-header fw-bold text-success d-flex justify-content-between align-items-center">
                    <i class="fas fa-calendar-alt me-2"></i> Sessões ({{ $campanha->sessoes->count() }})
                    <a href="{{ route('sessoes.index',$campanha->id) }}" class="btn btn-outline-success btn-sm rounded-pill">Ver Todas</a>
                </div>

                <div class="card-body p-0">
                    @if($campanha->sessoes->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($campanha->sessoes->sortByDesc('data')->take(5) as $sessao)
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light border-secondary">
                                    <div>
                                        <strong>{{ $sessao->titulo }}</strong><br>
                                        <small class="text-muted">
                                            {{ optional($sessao->data)->format('d/m/Y H:i') ?? 'Sem data' }}
                                        </small>
                                    </div>

                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('sessoes.show',['campanha'=>$campanha->id,'sessao'=>$sessao->id]) }}" class="btn btn-outline-success">Ver</a>
                                        <a href="{{ route('sessoes.edit',['campanha'=>$campanha->id,'sessao'=>$sessao->id]) }}" class="btn btn-outline-warning">Editar</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">
                            Nenhuma sessão criada. <a href="{{ route('sessoes.create',['campanha'=>$campanha->id]) }}" class="text-success">Agende a primeira!</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- JOGADORES --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-warning h-100">
                <div class="card-header fw-bold text-warning d-flex justify-content-between align-items-center">
                    <i class="fas fa-user-friends me-2"></i> Jogadores ({{ $campanha->jogadores->where('pivot.status', 'ativo')->count() }})
                </div>

                <div class="card-body p-0">

                    {{-- 1. SEÇÃO DE SOLICITAÇÕES PENDENTES --}}
                    @php
                        $pendentes = $campanha->jogadores->where('pivot.status', 'pendente');
                    @endphp

                    @if($pendentes->count())
                    <div class="p-3 bg-secondary border-bottom border-dark">
                        <h5 class="text-info fw-bold mb-3"><i class="fas fa-bell me-2"></i> Solicitações Pendentes ({{ $pendentes->count() }})</h5>

                        <ul class="list-group list-group-flush">
                            @foreach($pendentes as $jogador)
                            <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light border-secondary p-3 rounded mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle me-2 text-info fs-5"></i>
                                    <strong>{{ $jogador->nome }}</strong>
                                </div>

                                {{-- BOTÕES APRIMORADOS DE AÇÃO PARA PENDENTES --}}
                                <div class="d-flex gap-2">
                                    {{-- ACEITAR --}}
                                    <form action="{{ route('campanhas.aprovar',$campanha->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                                        <input type="hidden" name="status" value="ativo">
                                        <button type="submit" class="btn btn-success btn-sm fw-bold">
                                            <i class="fas fa-check me-1"></i> Aceitar
                                        </button>
                                    </form>

                                    {{-- RECUSAR (REMOVER) --}}
                                    {{-- ATENÇÃO: Confirmação via 'onsubmit' com 'confirm()' é proibida. Use um Modal UI customizado. --}}
                                    <form action="{{ route('campanhas.aprovar',$campanha->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Recusar {{ $jogador->nome }}? Ele precisará solicitar novamente.');">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                                        <input type="hidden" name="status" value="remover"> {{-- Ou 'rejeitado' --}}
                                        <button type="submit" class="btn btn-outline-danger btn-sm fw-bold">
                                            <i class="fas fa-times me-1"></i> Recusar
                                        </button>
                                    </form>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- 2. SEÇÃO DE JOGADORES ATIVOS --}}
                    <h5 class="text-warning fw-bold my-3 px-3"><i class="fas fa-users me-2"></i> Jogadores Ativos</h5>

                    @php
                        // Garante que o mestre (criador_id) apareça primeiro na lista
                        $ativos = $campanha->jogadores->where('pivot.status', 'ativo')->sortByDesc(fn($j)=>$j->id === $campanha->criador_id);
                    @endphp

                    @if($ativos->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($ativos as $jogador)
                                @php
                                    $isMaster = $jogador->id === $campanha->criador_id;
                                @endphp

                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light border-secondary">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                        <strong>{{ $jogador->nome }}</strong>

                                        @if($isMaster)
                                            <span class="badge bg-warning text-dark ms-2">
                                                <i class="fas fa-crown"></i> Mestre
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        @if(!$isMaster)
                                            {{-- BOTÃO REMOVER NA LISTA DE ATIVOS --}}
                                            {{-- ATENÇÃO: Confirmação via 'onsubmit' com 'confirm()' é proibida. Use um Modal UI customizado. --}}
                                            <form action="{{ route('campanhas.aprovar',$campanha->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja remover {{ $jogador->nome }} da campanha?');">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                                                <input type="hidden" name="status" value="remover">

                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                                    <i class="fas fa-user-slash me-1"></i> Remover
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">
                            Nenhum jogador ativo (ou apenas o Mestre).
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- PERSONAGENS --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-info h-100">

                <div class="card-header fw-bold text-info d-flex justify-content-between align-items-center">
                    <i class="fas fa-hat-wizard me-2"></i> Personagens ({{ $campanha->personagens->count() }})

                    <div class="d-flex gap-2">
                        <a href="{{ route('personagens.create',['campanha'=>$campanha->id]) }}"
                           class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="fas fa-plus"></i> Criar
                        </a>

                        <a href="{{ route('personagens.index',['campanha'=>$campanha->id]) }}"
                           class="btn btn-outline-info btn-sm rounded-pill">
                            Ver Todos
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($campanha->personagens->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($campanha->personagens->take(5) as $personagem)
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light border-secondary">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-alt me-2 text-muted"></i>
                                        <strong>{{ $personagem->nome }}</strong>
                                        <small class="text-muted ms-2">
                                            ({{ $personagem->jogador->nome ?? 'NPC' }})
                                        </small>
                                    </div>

                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('personagens.show',$personagem->id) }}" class="btn btn-outline-info">Ver</a>
                                       <a href="{{ route('personagens.editOverview',$personagem->id) }}" class="btn btn-outline-warning">Editar</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">Nenhum personagem criado.</p>
                    @endif
                </div>

            </div>
        </div>

    </div>

    {{-- SISTEMA --}}
    @if($campanha->sistema)
    <div class="card bg-dark mb-5 shadow-lg border-primary">
        <div class="card-header fw-bold text-primary fs-5">
            <i class="fas fa-dice-d20 me-2"></i> Sistema de Regras: {{ $campanha->sistema->nome }}
        </div>

        <div class="card-body p-0">

            <div class="p-4 border-bottom border-secondary d-flex justify-content-around flex-wrap gap-3 small text-center bg-secondary">
                <span class="text-muted">
                    Foco: <strong class="text-light">{{ $campanha->sistema->foco ?? 'N/A' }}</strong>
                </span>

                <span class="text-muted">
                    Mecânica: <strong class="text-light">{{ $campanha->sistema->mecanica_principal ?? 'N/A' }}</strong>
                </span>

                <span class="text-muted">
                    Complexidade: <strong class="text-light">{{ $campanha->sistema->complexidade ?? 'N/A' }}</strong>
                </span>

                <span class="text-muted">
                    Sanidade: <strong class="text-light">{{ $campanha->sistema->usa_sanidade ? 'Sim' : 'Não' }}</strong>
                </span>
            </div>

            {{-- Navegação de Tabs do Sistema --}}
            <ul class="nav nav-tabs nav-justified" id="sistemaTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active text-light bg-dark" id="atributos-tab" data-bs-toggle="tab" data-bs-target="#atributos" type="button">
                        Atributos & Perícias
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link text-light bg-dark" id="regras-tab" data-bs-toggle="tab" data-bs-target="#regras" type="button">
                        Regras & Recursos
                    </button>
                </li>
            </ul>

            {{-- Conteúdo das Tabs --}}
            <div class="tab-content p-4 bg-secondary">

                {{-- ATRIBUTOS E PERÍCIAS --}}
                <div class="tab-pane fade show active" id="atributos">
                    <div class="row">

                        {{-- 
                            [BEST PRACTICE ALERT]
                            Se 'atributos' for armazenado como JSON no banco de dados, adicione 
                            `'atributos' => 'array'` ou `'atributos' => 'json'` no array 
                            `$casts` do seu modelo `Sistema` para que o Laravel decodifique 
                            automaticamente, eliminando o bloco de checagem abaixo.
                        --}}
                        @php
                            $atributos = $campanha->sistema->atributos;
                            if (is_string($atributos)) {
                                $atributos = json_decode($atributos, true);
                            }
                            $atributos = is_array($atributos) ? $atributos : [];
                        @endphp

                        {{-- ATRIBUTOS --}}
                        <div class="col-md-6 border-end border-dark mb-3 mb-md-0">
                            <h5 class="text-info fw-bold mb-3">
                                <i class="fas fa-star me-2"></i> Atributos Base
                            </h5>

                            @if(!empty($atributos))
                                <ul class="list-group list-group-flush">
                                    @foreach($atributos as $sigla => $nome)
                                        <li class="list-group-item bg-dark text-light d-flex justify-content-between py-2 rounded mb-1">
                                            <span class="fw-bold">{{ $nome }}</span>
                                            <span class="badge bg-primary fs-6">{{ strtoupper($sigla) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted fst-italic p-3 bg-dark rounded text-center">
                                    Nenhum atributo definido.
                                </p>
                            @endif
                        </div>

                        {{-- PERÍCIAS AMOSTRA --}}
                        <div class="col-md-6">
                            <h5 class="text-success fw-bold mb-3">
                                <i class="fas fa-hand-rock me-2"></i> Perícias (Amostra)
                            </h5>

                            @if(isset($campanha->sistema->pericias) && $campanha->sistema->pericias->count())
                                <ul class="list-group list-group-flush">
                                    @foreach($campanha->sistema->pericias->take(6) as $pericia)
                                        <li class="list-group-item bg-dark text-light d-flex justify-content-between py-2 rounded mb-1">
                                            <span class="fw-bold">{{ $pericia->nome }}</span>
                                            <span class="badge bg-success">{{ strtoupper($pericia->atributo_relacionado) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted fst-italic p-3 bg-dark rounded text-center">
                                    Nenhuma perícia cadastrada para este sistema.
                                </p>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- REGRAS --}}
                <div class="tab-pane fade" id="regras">
                    <div class="p-3 bg-dark text-light rounded">
                        {!! nl2br(e($campanha->sistema->descricao ?? 'Nenhuma descrição do sistema.')) !!}
                    </div>
                </div>

            </div>

        </div>
    </div>
    @endif

</div>

{{-- JavaScript para a funcionalidade do código de convite --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('toggleCodeButton');
        const copyButton = document.getElementById('copyCodeButton');
        const codeDisplay = document.getElementById('inviteCodeDisplay');

        if (toggleButton && copyButton && codeDisplay) {
            const inviteCode = toggleButton.getAttribute('data-code');
            let isHidden = true;

            toggleButton.addEventListener('click', function () {
                if (isHidden) {
                    // Mostrar código
                    codeDisplay.textContent = inviteCode;
                    toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar';
                    copyButton.style.display = 'inline-block';
                } else {
                    // Ocultar código
                    codeDisplay.textContent = '********';
                    toggleButton.innerHTML = '<i class="fas fa-eye"></i> Mostrar';
                    copyButton.style.display = 'none';
                }
                isHidden = !isHidden;
            });

            copyButton.addEventListener('click', function () {
                // Tenta usar a API do Clipboard, com fallback para execCommand('copy')
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(inviteCode).then(() => {
                        showCopySuccess();
                    }).catch(err => {
                        console.error('Falha ao copiar usando Clipboard API:', err);
                        fallbackCopyTextToClipboard(inviteCode);
                    });
                } else {
                    fallbackCopyTextToClipboard(inviteCode);
                }
            });

            function fallbackCopyTextToClipboard(text) {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";  // Evita que a rolagem seja alterada
                textArea.style.opacity = "0";      // Oculta o elemento
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    document.execCommand('copy');
                    showCopySuccess();
                } catch (err) {
                    console.error('Falha ao copiar usando execCommand:', err);
                    // Opcional: mostrar uma mensagem de erro na UI
                }

                document.body.removeChild(textArea);
            }

            function showCopySuccess() {
                const originalText = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="fas fa-check"></i> Copiado!';
                copyButton.classList.remove('btn-info');
                copyButton.classList.add('btn-success');
                setTimeout(() => {
                    copyButton.innerHTML = originalText;
                    copyButton.classList.remove('btn-success');
                    copyButton.classList.add('btn-info');
                }, 1500);
            }
        }
    });
</script>

@endsection