@extends('layouts.app')

@section('title', "Área do Mestre - {$campanha->nome}")

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-warning mb-4">🎩 Área do Mestre — {{ $campanha->nome }}</h2>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Cabeçalho de ações --}}
    <div class="mb-4 d-flex gap-3 flex-wrap">
        <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-warning rounded-pill">✏️ Editar Campanha</a>
        <a href="{{ route('missoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-primary rounded-pill">➕ Criar Missão</a>
        <a href="{{ route('sessoes.create', ['campanha' => $campanha->id]) }}" class="btn btn-success rounded-pill">➕ Criar Sessão</a>
        <a href="{{ route('personagens.create', ['campanha' => $campanha->id]) }}" class="btn btn-info rounded-pill">➕ Criar Personagem</a>
    </div>

    {{-- Descrição da campanha --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header fw-bold text-highlight">📜 Descrição da Campanha</div>
        <div class="card-body">
            <p class="lead mb-0">
                {{ $campanha->descricao ?? 'O mestre ainda não escreveu a descrição da campanha.' }}
            </p>
        </div>
    </div>

    {{-- Grid 2x2 --}}
    <div class="row g-4">
        {{-- Missões --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold text-primary d-flex justify-content-between align-items-center">
                    🎯 Missões
                    <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">Ver Todas</a>
                </div>
                <div class="card-body p-0">
                    @if($campanha->missoes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->missoes->sortByDesc('data_criacao') as $missao)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $missao->titulo }}</strong><br>
                                        <small>Data limite: {{ optional($missao->data_limite)->format('d/m/Y') ?? 'Sem data' }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-primary">🔍</a>
                                        <a href="{{ route('missoes.edit', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-outline-warning">✏️</a>
                                        <form action="{{ route('missoes.destroy', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger">🗑️</button>
                                        </form>
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
            <div class="card shadow-sm">
                <div class="card-header fw-bold text-warning">👥 Jogadores</div>
                <div class="card-body p-0">
                    @if($campanha->jogadores->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->jogadores->sortByDesc(fn($j) => $j->id === $campanha->criador_id) as $jogador)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $jogador->nome }}
                                        @if($jogador->id === $campanha->criador_id)
                                            <span class="badge bg-warning text-dark ms-1">Mestre</span>
                                        @endif
                                    </div>
                                    <span class="badge
                                        {{ $jogador->pivot->status === 'ativo' ? 'bg-success' :
                                           ($jogador->pivot->status === 'pendente' ? 'bg-info' : 'bg-secondary') }}">
                                        {{ ucfirst($jogador->pivot->status) }}
                                    </span>
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
            <div class="card shadow-sm">
                <div class="card-header fw-bold text-success d-flex justify-content-between align-items-center">
                    🗓️ Sessões
                    <a href="{{ route('sessoes.index', $campanha->id) }}" class="btn btn-outline-success btn-sm rounded-pill">Ver Todas</a>
                </div>
                <div class="card-body p-0">
                    @if($campanha->sessoes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($campanha->sessoes->sortByDesc('data') as $sessao)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $sessao->titulo }}</strong><br>
                                        <small>{{ optional($sessao->data)->format('d/m/Y H:i') ?? 'Sem data' }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('sessoes.show', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-success">🔍</a>
                                        <a href="{{ route('sessoes.edit', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" class="btn btn-outline-warning">✏️</a>
                                        <form action="{{ route('sessoes.destroy', ['campanha' => $campanha->id, 'sessao' => $sessao->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger">🗑️</button>
                                        </form>
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
            <div class="card shadow-sm">
                <div class="card-header fw-bold text-info d-flex justify-content-between align-items-center">
                    🧙 Personagens
                    <a href="{{ route('personagens.create', ['campanha' => $campanha->id]) }}" class="btn btn-outline-info btn-sm rounded-pill">Novo</a>
                </div>
                <div class="card-body p-0">
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
                        <p class="text-secondary fst-italic p-3 mb-0 text-center">Nenhum personagem criado ainda.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.text-highlight { color: var(--btn-bg, #ffc107); }
</style>
@endsection
