@extends('layouts.app')

@section('title', $campanha->nome)

@section('content')
@php
    $user = auth()->user();
    $isMestre = $user && $user->id === $campanha->criador_id;
    // O status 'solicitacao' só existe se o usuário estiver logado.
    $solicitacao = $user ? $campanha->jogadores->where('id', $user->id)->first()?->pivot->status ?? null : null;
    $participa = $user && ($isMestre || $solicitacao === 'ativo');
@endphp

{{-- Botão Voltar --}}
<div class="mb-4">
    <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> ← Voltar para campanhas
    </a>
</div>

{{-- Verificação de Acesso: NÃO participa / Solicitação pendente --}}
@if(!$participa)
    <div class="container py-5 text-center">
        @if($solicitacao === 'pendente')
            <div class="alert alert-warning shadow-sm" role="alert">
                <i class="bi bi-hourglass-split me-2"></i> Sua solicitação para participar desta campanha está **pendente**. Aguarde a aprovação do mestre.
            </div>
        @else
            <div class="alert alert-danger shadow-sm" role="alert">
                <i class="bi bi-x-octagon-fill me-2"></i> Você não pode acessar esta campanha porque **não participa** dela.
                @auth
                    <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST" class="d-inline-block ms-3">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-pill px-3">
                            <i class="bi bi-person-plus-fill me-1"></i> Solicitar entrada
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary ms-3 rounded-pill px-3">Fazer Login para Entrar</a>
                @endauth
            </div>
        @endif
    </div>

{{-- Usuário participante --}}
@else
<div class="container py-4">

    {{-- CABEÇALHO DA CAMPANHA --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 pb-2 border-bottom">
        <div>
            <h1 class="fw-bolder mb-1">{{ $campanha->nome }}</h1>
            <span class="badge bg-primary fs-6">Campanha RPG</span>
        </div>

        @if($isMestre)
            <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm mt-2 mt-md-0">
                <i class="bi bi-tools me-1"></i> Área do Mestre
            </a>
        @endif
    </div>

    <div class="row g-4 mb-5">

        {{-- DESCRIÇÃO DA CAMPANHA --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-3"><i class="bi bi-book-half me-2"></i> Descrição:</h5>
                    <p class="lead mb-0 text-secondary">
                        {{ $campanha->descricao ?? 'O mestre ainda não escreveu a descrição da campanha.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- DASHBOARD SUPERIOR (Métricas) --}}
        <div class="col-12">
            <h3 class="fw-bold mb-3">Resumo Rápido</h3>
            <div class="row g-3">

                {{-- Missões --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-card shadow-sm border-start border-3 border-success">
                        <h6 class="text-muted"><i class="bi bi-flag-fill me-1 text-success"></i> MISSÕES</h6>
                        <h2 class="fw-bold">{{ $campanha->missoes->count() }}</h2>
                        <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-sm rounded-pill btn-outline-success mt-2">
                            Ver Todas
                        </a>
                    </div>
                </div>

                {{-- Sessões --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-card shadow-sm border-start border-3 border-info">
                        <h6 class="text-muted"><i class="bi bi-calendar-event-fill me-1 text-info"></i> SESSÕES</h6>
                        <h2 class="fw-bold">{{ $campanha->sessoes->count() }}</h2>
                        <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-sm rounded-pill btn-outline-info mt-2">
                            Ver Todas
                        </a>
                    </div>
                </div>

                {{-- Jogadores --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-card shadow-sm border-start border-3 border-secondary">
                        <h6 class="text-muted"><i class="bi bi-people-fill me-1"></i> JOGADORES</h6>
                        <h2 class="fw-bold">{{ $campanha->jogadores->count() }}</h2>
                        <a href="#jogadores-list" class="btn btn-sm rounded-pill btn-outline-secondary mt-2">
                            Ver Lista
                        </a>
                    </div>
                </div>

                {{-- Personagens --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="dashboard-card shadow-sm border-start border-3 border-primary">
                        <h6 class="text-muted"><i class="bi bi-person-badge-fill me-1 text-primary"></i> PERSONAGENS</h6>
                        <h2 class="fw-bold">{{ $campanha->personagens->count() }}</h2>
                        <a href="{{ route('personagens.create', ['campanha_id' => $campanha->id]) }}" class="btn btn-sm rounded-pill btn-outline-primary mt-2">
                            Criar Novo
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <hr>

    {{-- GRID PRINCIPAL: LISTAGENS --}}
    <div class="row g-4">

        {{-- MISSÕES RECENTES --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-flag-fill me-2"></i> Missões Recentes</h5>
                    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-light btn-sm rounded-pill fw-bold">
                        Todas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($campanha->missoes->count())
                        <ul class="list-group list-group-flush">
                            {{-- Limitar a 4 ou 5 para não alongar o dashboard --}}
                            @foreach($campanha->missoes->take(5) as $missao)
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $missao->titulo }}</h6>
                                        <p class="small text-muted mb-1">
                                            Data limite:
                                            <span class="fw-semibold">{{ optional($missao->data_limite)->format('d/m/Y') ?? 'Sem data definida' }}</span>
                                        </p>
                                    </div>
                                    <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}"
                                       class="btn btn-outline-dark btn-sm rounded-pill mt-2 mt-md-0">
                                        Detalhes
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted p-4 mb-0">Nenhuma missão registrada ainda.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- SESSÕES AGENDADAS --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-event-fill me-2"></i> Próximas Sessões</h5>
                    <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-light btn-sm rounded-pill fw-bold">
                        Todas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($campanha->sessoes->count())
                        <ul class="list-group list-group-flush">
                            {{-- Limitar a 4 ou 5 para não alongar o dashboard --}}
                            @foreach($campanha->sessoes->take(5) as $sessao)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $sessao->titulo }}</h6>
                                        <p class="small text-muted mb-1">
                                            Data:
                                            <span class="fw-semibold">{{ optional($sessao->data)->format('d/m/Y H:i') ?? 'Sem data definida' }}</span>
                                        </p>
                                    </div>
                                    <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}"
                                       class="btn btn-outline-dark btn-sm rounded-pill">
                                        Detalhes
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted p-4 mb-0">Nenhuma sessão agendada.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- PERSONAGENS DO GRUPO --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill me-2"></i> Personagens do Grupo</h5>
                    <a href="{{ route('personagens.create', ['campanha' => $campanha->id]) }}" class="btn btn-light btn-sm rounded-pill fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Criar Novo
                    </a>
                     <a href="{{ route('personagens.index',['campanha'=>$campanha->id]) }}"
                           class="btn btn-outline-info btn-sm rounded-pill">
                            Ver Todos
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($campanha->personagens->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->personagens->take(5) as $personagem)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ $personagem->nome }}</span>
                                    <a href="{{ route('personagens.show', $personagem->id) }}"
                                       class="btn btn-outline-dark btn-sm rounded-pill">
                                        Ver Ficha
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted p-4 mb-0">Nenhum personagem criado ainda.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- JOGADORES NA CAMPANHA --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100" id="jogadores-list">
                <div class="card-header bg-secondary text-white fw-bold">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i> Jogadores ({{ $campanha->jogadores->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($campanha->jogadores->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->jogadores->sortByDesc(fn($j) => $j->id === $campanha->criador_id) as $jogador)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-person-circle me-2"></i> {{ $jogador->nome }}
                                        @if($jogador->id === $campanha->criador_id)
                                            <span class="badge bg-warning text-dark ms-2 fw-bold">MESTRE</span>
                                        @endif
                                    </span>
                                    @if($jogador->pivot->status !== 'ativo' && $isMestre)
                                        {{-- Só exibe status/ação se for mestre ou se o status for pendente para o próprio usuário --}}
                                        <span class="badge
                                            {{ $jogador->pivot->status === 'ativo' ? 'bg-success' :
                                               ($jogador->pivot->status === 'pendente' ? 'bg-info' : 'bg-danger') }}">
                                            {{ ucfirst($jogador->pivot->status) }}
                                        </span>
                                    @elseif($jogador->pivot->status === 'ativo')
                                        <span class="badge bg-success">Ativo</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted p-4 mb-0">Nenhum jogador entrou ainda (além do mestre).</p>
                    @endif
                </div>
            </div>
        </div>

    </div> {{-- fim grid --}}
</div>
@endif

<style>
/* Estilos mantidos, mas com ajustes sutis */
.dashboard-card {
    background: #ffffff;
    border-radius: 0.5rem; /* Um pouco menos arredondado */
    padding: 15px; /* Um pouco mais compacto */
    text-align: center;
    border: 1px solid #e3e3e3;
    transition: 0.2s ease;
    height: 100%; /* Garante que todos tenham a mesma altura */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.dashboard-card:hover {
    transform: translateY(-3px); /* Efeito sutil de elevação */
    box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
    background: #fdfdfd;
}
.dashboard-card h2 {
    font-size: 2rem; /* Levemente menor */
    margin-top: 5px;
    margin-bottom: 10px;
}
.list-group-item {
    padding: 1rem 1.25rem; /* Mais espaçamento interno */
}
</style>

@endsection
