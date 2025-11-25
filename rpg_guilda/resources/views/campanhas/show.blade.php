@extends('layouts.app')

@section('title', $campanha->nome)

@section('content')
@php
    $user = auth()->user();
    $isMestre = $user && $user->id === $campanha->criador_id;
    $participa = $user && ($isMestre || $campanha->jogadores->pluck('id')->contains($user->id));
    $solicitacao = $user ? $campanha->jogadores->where('id', $user->id)->first()?->pivot->status ?? null : null;
@endphp

{{-- Botão Voltar --}}
<div class="mb-3">
    <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-secondary rounded-pill px-3">
        ← Voltar para todas as campanhas
    </a>
</div>

{{-- Usuário não participa --}}
@if(!$participa && $solicitacao !== 'pendente')
    <div class="container py-5">
        <div class="alert alert-danger text-center">
            ⚠️ Você não pode acessar esta campanha porque não participa dela.
            @auth
                <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST" class="d-inline-block ms-2">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill">Solicitar entrada</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm ms-2">Login</a>
            @endauth
        </div>
    </div>

{{-- Solicitação pendente --}}
@elseif($solicitacao === 'pendente')
    <div class="container py-5">
        <div class="alert alert-warning text-center">
            ⏳ Sua solicitação para participar desta campanha está pendente. Aguarde aprovação do mestre.
        </div>
    </div>

{{-- Usuário participante ou mestre --}}
@else
<div class="container py-4">

    {{-- Cabeçalho --}}
    <div class="p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="fw-bolder">
                {{ $campanha->nome }}
                <span class="badge bg-secondary ms-2">Campanha</span>
            </h1>

            {{-- Botão Área do Mestre --}}
            @if($isMestre)
                <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-warning rounded-pill px-3 fw-bold">
                    Área do Mestre
                </a>
            @endif
        </div>
    </div>

    {{-- Descrição centralizada --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header text-center text-highlight fw-bold">📜 Descrição da Campanha</div>
        <div class="card-body text-center">
            <p class="lead mb-0">
                {{ $campanha->descricao ?? 'O mestre ainda não escreveu a descrição da campanha.' }}
            </p>
        </div>
    </div>

    {{-- 2x2: Missões | Jogadores / Sessões | Personagens --}}
    <div class="row g-4">

        {{-- Missões --}}
        <div class="col-lg-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-highlight">🎯 Missões</h5>
                    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if($campanha->missoes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->missoes as $missao)
                                <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap">
                                    <div class="me-3">
                                        <h6 class="fw-bold mb-1">{{ $missao->titulo }}</h6>
                                        <p class="small mb-1 text-muted">
                                            Data limite: {{ optional($missao->data_limite)->format('d/m/Y') ?? 'Sem data definida' }}
                                        </p>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-info btn-sm rounded-pill">
                                            🔍 Ver
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-secondary mb-0 text-center">
                            Nenhuma missão registrada ainda.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Jogadores --}}
        <div class="col-lg-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-highlight fw-bold">👥 Jogadores</div>
                <div class="card-body p-0">
                    @if($campanha->jogadores->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->jogadores->sortByDesc(fn($j) => $j->id === $campanha->criador_id) as $jogador)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        {{ $jogador->nome }}
                                        @if($jogador->id === $campanha->criador_id)
                                            <span class="badge bg-warning text-dark ms-2">Mestre</span>
                                        @endif
                                    </span>
                                    <span class="badge
                                        {{ $jogador->pivot->status === 'ativo' ? 'bg-success' :
                                           ($jogador->pivot->status === 'pendente' ? 'bg-info' : 'bg-secondary') }}">
                                        {{ ucfirst($jogador->pivot->status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">
                            Nenhum jogador entrou na campanha ainda.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sessões --}}
        <div class="col-lg-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-highlight">📖 Sessões</h5>
                    <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if($campanha->sessoes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->sessoes as $sessao)
                                <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap">
                                    <div class="me-3">
                                        <h6 class="fw-bold mb-1">{{ $sessao->titulo }}</h6>
                                        <p class="small mb-1 text-muted">
                                            Data: {{ optional($sessao->data)->format('d/m/Y H:i') ?? 'Sem data definida' }}
                                        </p>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-info btn-sm rounded-pill">
                                            🔍 Ver
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-secondary mb-0 text-center">
                            Nenhuma sessão registrada ainda.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Personagens --}}
        <div class="col-lg-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-highlight">🧙 Personagens</h5>
                    @auth
                        <a href="{{ route('personagens.create') }}?campanha={{ $campanha->id }}"
                           class="btn btn-outline-secondary btn-sm rounded-pill">
                            Criar
                        </a>
                    @endauth
                </div>
                <div class="card-body">
                    @if($campanha->personagens->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->personagens as $personagem)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $personagem->nome }}
                                    <a href="{{ route('personagens.show', $personagem->id) }}" class="btn btn-outline-info btn-sm rounded-pill">Ver</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-secondary mb-0 text-center">
                            Nenhum personagem criado ainda.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div> {{-- fim row 2x2 --}}
</div> {{-- fim container --}}
@endif {{-- fim else participante/ mestre --}}

<style>
.text-highlight {
    color: var(--btn-bg, #ffc107);
}
</style>
@endsection
