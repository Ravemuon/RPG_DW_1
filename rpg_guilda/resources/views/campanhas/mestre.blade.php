@extends('layouts.app')

@section('title', "Área do Mestre - {$campanha->nome}")

@section('content')
{{-- ==================================================================== --}}
{{-- ⚠️ CHECK DE SEGURANÇA: Garante que apenas o Criador/Mestre acesse esta página. --}}
@if($campanha->criador_id !== auth()->id())
    {{-- Se o usuário logado NÃO é o criador da campanha, retorna erro 403 (Proibido). --}}
    @php abort(403, 'Acesso Proibido. Apenas o Mestre pode acessar esta área.'); @endphp
@endif
{{-- ==================================================================== --}}

<div class="container py-5 text-light">
    <h2 class="fw-bold text-warning mb-4">🎩 Área do Mestre — {{ $campanha->nome }}</h2>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Cabeçalho de ações --}}
    <div class="mb-4 d-flex gap-3 flex-wrap">
        {{-- NOVO BOTÃO: Volta para a vista normal da campanha (Visão do Jogador) --}}
        <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-light rounded-pill fw-bold">👁️ Ver Campanha (Visão do Jogador)</a>

        <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-warning rounded-pill">✏️ Editar Campanha</a>
        <a href="{{ route('missoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-primary rounded-pill">➕ Criar Missão</a>
        <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-success rounded-pill">➕ Criar Sessão</a>
        <a href="{{ route('personagens.create', ['campanha' => $campanha->id]) }}" class="btn btn-info rounded-pill">➕ Criar Personagem</a>
    </div>

    {{-- PAINEL: Código de Convite (Apenas se for privada e tiver um código) --}}
    {{-- A segurança aqui é reforçada pela checagem de Mestre no topo do arquivo. --}}
    @if($campanha->privada && $campanha->codigo_convite)
    <div class="card bg-dark border-info mb-4 shadow-lg">
        <div class="card-header fw-bold text-info">🔗 Código de Convite Privado</div>
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <p class="mb-3 mb-md-0 text-muted">
                Este código permite que novos jogadores entrem diretamente na sua campanha.
            </p>

            <div class="d-flex align-items-center gap-3">
                <span id="inviteCodeDisplay" class="fs-4 fw-bold text-light bg-secondary px-3 py-1 rounded" style="user-select: none;">
                    ********
                </span>
                <button id="toggleCodeButton" class="btn btn-outline-info" data-code="{{ $campanha->codigo_convite }}">
                    <i class="fas fa-eye"></i> Mostrar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Descrição da campanha --}}
    <div class="card bg-dark mb-4 shadow-sm border-warning">
        <div class="card-header fw-bold text-warning">📜 Descrição da Campanha</div>
        <div class="card-body">
            <p class="lead mb-0 text-light">
                {{ $campanha->descricao ?? 'O mestre ainda não escreveu a descrição da campanha.' }}
            </p>
        </div>
    </div>

    {{-- Grid 2x2 --}}
    <div class="row g-4">
        {{-- Missões --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-primary">
                <div class="card-header fw-bold text-primary d-flex justify-content-between align-items-center">
                    🎯 Missões
                    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">Ver Todas</a>
                </div>
                <div class="card-body p-0">
                    @if($campanha->missoes->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($campanha->missoes->sortByDesc('data_criacao')->take(5) as $missao)
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light">
                                    <div>
                                        <strong>{{ $missao->titulo }}</strong><br>
                                        <small class="text-muted">Data limite: {{ optional($missao->data_limite)->format('d/m/Y') ?? 'Sem data' }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-primary">🔍</a>
                                        <a href="{{ route('missoes.edit', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-warning">✏️</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">Nenhuma missão criada ainda.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Jogadores --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-warning">
                <div class="card-header fw-bold text-warning">👥 Jogadores ({{ $campanha->jogadores->count() }})</div>
                <div class="card-body p-0">
                    @if($campanha->jogadores->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($campanha->jogadores->sortByDesc(fn($j) => $j->id === $campanha->criador_id) as $jogador)
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light">
                                    <div>
                                        {{ $jogador->nome }}
                                        @if($jogador->id === $campanha->criador_id)
                                            <span class="badge bg-warning text-dark ms-1">Mestre</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge
                                            {{ $jogador->pivot->status === 'ativo' ? 'bg-success' :
                                               ($jogador->pivot->status === 'pendente' ? 'bg-info text-dark' : 'bg-secondary') }}">
                                            {{ ucfirst($jogador->pivot->status) }}
                                        </span>
                                        {{-- Botões de ação para jogadores (apenas se não for o Mestre) --}}
                                        @if($jogador->id !== $campanha->criador_id)
                                            @if($jogador->pivot->status === 'pendente')
                                                {{-- Formulário para Aprovar Jogador --}}
                                                <form action="{{ route('campanhas.aprovar', $campanha->id) }}" method="POST" class="d-inline ms-2">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                                                    <input type="hidden" name="status" value="ativo">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill">✅ Aprovar</button>
                                                </form>
                                            @endif
                                            {{-- Formulário para Remover Jogador --}}
                                            <form action="{{ route('campanhas.aprovar', $campanha->id) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                                                <input type="hidden" name="status" value="remover">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Remover Jogador">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">Nenhum jogador inscrito.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sessões --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-success">
                <div class="card-header fw-bold text-success d-flex justify-content-between align-items-center">
                    🗓️ Sessões
                    <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-outline-success btn-sm rounded-pill">Ver Todas</a>
                </div>
                <div class="card-body p-0">
                    @if($campanha->sessoes->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($campanha->sessoes->sortByDesc('data')->take(5) as $sessao)
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light">
                                    <div>
                                        <strong>{{ $sessao->titulo }}</strong><br>
                                        <small class="text-muted">{{ optional($sessao->data)->format('d/m/Y H:i') ?? 'Sem data' }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-success">🔍</a>
                                        <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-warning">✏️</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">Nenhuma sessão criada ainda.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Personagens --}}
        <div class="col-md-6">
            <div class="card bg-dark shadow-sm border-info">
                <div class="card-header fw-bold text-info d-flex justify-content-between align-items-center">
                    🧙 Personagens
                    <div class="d-flex gap-2">
                        {{-- Criar personagem --}}
                        <a href="{{ route('personagens.create') }}?campanha={{ $campanha->id }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                            Criar
                        </a>
                        {{-- Index completo de personagens (visão do Mestre) --}}
                        <a href="{{ route('personagens.index') }}?campanha={{ $campanha->id }}" class="btn btn-outline-info btn-sm rounded-pill">
                            Ver Todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($campanha->personagens->count())
                        <ul class="list-group list-group-flush list-group-dark">
                            @foreach($campanha->personagens->take(5) as $personagem)
                                <li class="list-group-item bg-dark d-flex justify-content-between align-items-center text-light">
                                    {{ $personagem->nome }}
                                    <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-outline-info btn-sm rounded-pill">Ver</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">Nenhum personagem criado ainda.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-highlight { color: var(--btn-bg, #ffc107); }
.card-header { border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
.list-group-dark .list-group-item { border-color: rgba(255, 255, 255, 0.05); }
.bg-dark { background-color: #212529 !important; }
</style>

{{-- Script para mostrar/ocultar o código de convite --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('toggleCodeButton');
        const codeDisplay = document.getElementById('inviteCodeDisplay');

        if (toggleButton && codeDisplay) {
            const inviteCode = toggleButton.getAttribute('data-code');
            let isHidden = true;

            toggleButton.addEventListener('click', function() {
                if (isHidden) {
                    codeDisplay.textContent = inviteCode;
                    // Usei FontAwesome 5 aqui, que é comum em templates
                    toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar';
                    isHidden = false;
                } else {
                    codeDisplay.textContent = '********';
                    toggleButton.innerHTML = '<i class="fas fa-eye"></i> Mostrar';
                    isHidden = true;
                }
            });
        }
    });
</script>
@endsection
