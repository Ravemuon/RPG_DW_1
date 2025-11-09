@extends('layouts.app')

@section('title', $campanha->nome)

@section('content')
<div class="container py-5">
    @php
        $user = auth()->user();
        $isMestre = $user && $user->id === $campanha->criador_id;
        $proximaSessao = $campanha->sessoes->where('data', '>=', now())->sortBy('data')->first();
        $jogadoresAtivos = $campanha->jogadores->where('pivot.status', 'ativo')->pluck('nome')->implode(', ');
        $userStatus = $isMestre ? 'mestre' : ($campanha->jogadores->firstWhere('id', $user?->id)?->pivot->status ?? null);
    @endphp

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-warning mb-1">{{ $campanha->nome }}</h2>
            <p class="text-light mb-0 small">Mestre: {{ $campanha->criador->nome ?? 'Desconhecido' }}</p>
            <p class="text-light mb-0 small">📅 Próx. Sessão: {{ $proximaSessao ? $proximaSessao->data->format('d/m/Y H:i') : 'N/A' }}</p>
            <p class="text-light mb-0 small">👥 Jogadores ativos: {{ $jogadoresAtivos ?: 'Nenhum ativo' }}</p>
            <p class="text-light mb-0 small">Sistema: {{ $campanha->sistema->nome ?? 'Desconhecido' }}</p>
            <p class="text-light mt-2">{{ $campanha->descricao ?? 'Sem descrição.' }}</p>
        </div>

        {{-- Botões --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('campanhas.todas') }}" class="btn btn-outline-light btn-sm">⬅️ Voltar</a>
            @auth
                @if($isMestre || $user->tipo === 'administrador')
                    <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-outline-success btn-sm">✏️ Editar</a>
                @endif
                @if($isMestre)
                    <a href="{{ route('campanhas.mestre', $campanha->id) }}" class="btn btn-danger btn-sm">⚙️ Área do Mestre</a>
                @endif
            @endauth
        </div>
    </div>

    <hr class="text-light">

    {{-- Status --}}
    @auth
        @if($userStatus)
            <div class="alert {{ $userStatus === 'pendente' ? 'alert-warning' : 'alert-success' }} text-center">
                @if($userStatus === 'pendente') ⏳ Solicitação enviada, aguardando aprovação.
                @elseif($userStatus === 'ativo') ✅ Você é jogador desta campanha.
                @elseif($userStatus === 'mestre') ✅ Você é o mestre desta campanha.
                @endif
            </div>
        @endif
    @endauth

    {{-- Aba Jogadores --}}
    <ul class="nav nav-tabs mb-3" id="campanhaTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="jogadores-tab" data-bs-toggle="tab" data-bs-target="#jogadores" type="button" role="tab" aria-controls="jogadores" aria-selected="true">
                👥 Jogadores
            </button>
        </li>
    </ul>

    <div class="tab-content mb-5" id="campanhaTabContent">
        <div class="tab-pane fade show active" id="jogadores" role="tabpanel" aria-labelledby="jogadores-tab">
            @if($campanha->jogadores->count())
                <ul class="list-group list-group-flush">
                    @foreach($campanha->jogadores as $jogador)
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-light border-secondary">
                            <div>
                                <strong>{{ $jogador->nome }}</strong>
                                <span class="text-secondary small">({{ $jogador->papel ?? 'Jogador' }})</span>
                            </div>
                            <span class="badge
                                {{ $jogador->pivot->status === 'ativo' ? 'bg-success' : ($jogador->pivot->status === 'pendente' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                {{ ucfirst($jogador->pivot->status) }}
                            </span>

                            @if($isMestre && $jogador->id !== $user->id)
                                <form action="{{ route('campanhas.usuarios.gerenciar', $campanha->id) }}" method="POST" class="ms-2 d-inline-flex gap-1">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="ativo" {{ $jogador->pivot->status === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                        <option value="pendente" {{ $jogador->pivot->status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="rejeitado" {{ $jogador->pivot->status === 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                                        <option value="remover">Remover</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-light">✅</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary fst-italic">Nenhum jogador cadastrado nesta campanha.</p>
            @endif

            {{-- Convidar Amigos --}}
            @if($isMestre)
                <hr class="text-light mt-4">
                <h5 class="text-warning">➕ Convidar Amigos</h5>
                @if($amigos->count())
                    <form action="{{ route('campanhas.usuarios.adicionar', $campanha->id) }}" method="POST" class="row g-3 mt-2">
                        @csrf
                        <div class="col-md-6">
                            <select name="user_id" class="form-select">
                                @foreach($amigos as $amigo)
                                    <option value="{{ $amigo->id }}">{{ $amigo->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info btn-sm">Enviar Convite</button>
                        </div>
                    </form>
                @else
                    <p class="text-secondary fst-italic mt-2">Nenhum amigo disponível para convite.</p>
                @endif
            @endif
        </div>
    </div>

    <hr class="text-light">

    {{-- Sessões --}}
    <div class="mb-5 mt-5">
        <h3 class="fw-bold text-warning mb-3">🗺️ Sessões</h3>

        @if($proximaSessao)
            <div class="card bg-dark text-light border-warning shadow-sm mb-4 p-3">
                <h5 class="text-warning mb-1">🔥 Próxima Sessão 🔥</h5>
                <p><strong>{{ $proximaSessao->titulo }}</strong> em <span class="badge bg-warning text-dark">{{ $proximaSessao->data->format('d/m/Y H:i') }}</span></p>
                @if($proximaSessao->descricao)
                    <p class="small fst-italic">{{ $proximaSessao->descricao }}</p>
                @endif
                <a href="{{ route('sessoes.show', $proximaSessao->id) }}" class="btn btn-outline-warning btn-sm">🔍 Ver Detalhes</a>
            </div>
        @else
            <div class="alert alert-info text-center">Nenhuma próxima sessão agendada.</div>
        @endif

        <h4 class="fw-bold text-light mt-4 mb-3">Histórico e Próximas</h4>
        @if($campanha->sessoes->count())
            <ul class="list-group list-group-flush mt-3">
                @foreach($campanha->sessoes->sortByDesc('data') as $sessao)
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-light border-secondary">
                        <div>
                            <strong class="{{ $sessao->data->isFuture() ? 'text-info' : 'text-light' }}">{{ $sessao->titulo }}</strong>
                            <span class="text-secondary">— {{ $sessao->data?->format('d/m/Y H:i') ?? 'Sem data' }}</span>
                            @if($sessao->data->isFuture())
                                <span class="badge bg-info">Próxima</span>
                            @endif
                        </div>
                        @if($userStatus && in_array($userStatus, ['mestre','ativo']))
                            <a href="{{ route('sessoes.show', $sessao->id) }}" class="btn btn-outline-info btn-sm">🔍 Ver</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-secondary fst-italic mt-3">Nenhuma sessão criada ainda.</p>
        @endif
    </div>
</div>
@endsection
