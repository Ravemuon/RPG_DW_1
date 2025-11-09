@extends('layouts.app')

@section('title', "Área do Mestre - {$campanha->nome}")

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-warning mb-4">🎩 Área do Mestre — {{ $campanha->nome }}</h2>

    {{-- Alertas de feedback --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 🔸 Gerenciar Jogadores --}}
    <div class="card bg-dark border-warning mb-4 shadow-sm">
        <div class="card-header text-warning fw-bold">
            📋 Gerenciar Jogadores
        </div>
        <div class="card-body">
            @if($campanha->jogadores->count())
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Jogador</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campanha->jogadores as $jogador)
                            @if($jogador->id !== auth()->id())
                                <tr>
                                    <td>{{ $jogador->nome }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $jogador->pivot->status === 'ativo' ? 'bg-success' :
                                               ($jogador->pivot->status === 'pendente' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                            {{ ucfirst($jogador->pivot->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('campanhas.usuarios.aprovar', $campanha->id) }}" method="POST" class="d-flex gap-2 justify-content-center">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $jogador->id }}">
                                            <select name="status" class="form-select form-select-sm w-auto">
                                                <option value="ativo" {{ $jogador->pivot->status === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                                <option value="pendente" {{ $jogador->pivot->status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                                <option value="rejeitado" {{ $jogador->pivot->status === 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                                                <option value="remover">Remover</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-light">✅ Atualizar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-secondary fst-italic mb-0">Nenhum jogador inscrito nesta campanha.</p>
            @endif
        </div>
    </div>

    {{-- 🔹 Convidar Amigos --}}
    <div class="card bg-dark border-info mb-4 shadow-sm">
        <div class="card-header text-info fw-bold d-flex justify-content-between align-items-center">
            🤝 Convidar Amigos para a Campanha
        </div>
        <div class="card-body">
            @php
                $amigos = auth()->user()->amigos ?? collect();
                $amigosDisponiveis = $amigos->filter(fn($amigo) => !$campanha->jogadores->contains('id', $amigo->id));
            @endphp

            @if($amigosDisponiveis->count())
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Amigo</th>
                            <th class="text-center">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($amigosDisponiveis as $amigo)
                            <tr>
                                <td>{{ $amigo->nome }}</td>
                                <td class="text-center">
                                    <form action="{{ route('campanhas.usuarios.adicionar', $campanha->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $amigo->id }}">
                                        <button type="submit" class="btn btn-outline-info btn-sm">➕ Adicionar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-secondary fst-italic mb-0">Todos os seus amigos já estão nesta campanha ou não há amigos disponíveis.</p>
            @endif
        </div>
    </div>

    {{-- 🔸 Missões --}}
    <div class="card bg-dark border-primary mb-4 shadow-sm">
        <div class="card-header text-primary fw-bold d-flex justify-content-between align-items-center">
            🎯 Missões da Campanha
            <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary btn-sm">➕ Nova Missão</a>
        </div>
        <div class="card-body">
            @if($campanha->missoes->count())
                <ul class="list-group list-group-flush">
                    @foreach($campanha->missoes->sortByDesc('data_criacao') as $missao)
                        <li class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                            <span>{{ $missao->titulo }} — <small>{{ optional($missao->data_limite)->format('d/m/Y') }}</small></span>
                            <a href="{{ route('missoes.show', $missao->id) }}" class="btn btn-outline-primary btn-sm">🔍 Ver</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary fst-italic mb-0">Nenhuma missão criada ainda.</p>
            @endif
        </div>
    </div>

    {{-- 🔸 Sessões --}}
    <div class="card bg-dark border-success shadow-sm mb-4">
        <div class="card-header text-success fw-bold d-flex justify-content-between align-items-center">
            🗓️ Sessões da Campanha
            <a href="{{ route('sessoes.create', $campanha->id) }}" class="btn btn-success btn-sm">➕ Nova Sessão</a>
        </div>
        <div class="card-body">
            @if($campanha->sessoes->count())
                <ul class="list-group list-group-flush">
                    @foreach($campanha->sessoes->sortByDesc('data') as $sessao)
                        <li class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                            <span>{{ $sessao->titulo }} — <small>{{ optional($sessao->data)->format('d/m/Y H:i') }}</small></span>
                            <a href="{{ route('sessoes.show', $sessao->id) }}" class="btn btn-outline-success btn-sm">🔍 Ver</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary fst-italic mb-0">Nenhuma sessão criada ainda.</p>
            @endif
        </div>
    </div>
    {{-- 🔸 Missões --}}
    <div class="card bg-dark border-primary mb-4 shadow-sm">
        <div class="card-header text-primary fw-bold d-flex justify-content-between align-items-center">
            🎯 Missões da Campanha
            <a href="{{ route('missoes.create', $campanha->id) }}" class="btn btn-primary btn-sm">➕ Nova Missão</a>
        </div>
        <div class="card-body">
            @if($campanha->missoes->count())
                <ul class="list-group list-group-flush">
                    @foreach($campanha->missoes->sortByDesc('data_criacao') as $missao)
                        <li class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                            <span>{{ $missao->titulo }} — <small>{{ optional($missao->data_limite)->format('d/m/Y') }}</small></span>
                            <a href="{{ route('missoes.show', $missao->id) }}" class="btn btn-outline-primary btn-sm">🔍 Ver</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary fst-italic mb-0">Nenhuma missão criada ainda.</p>
            @endif
        </div>
    </div>


    <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-light btn-sm">⬅️ Voltar à campanha</a>
</div>
@endsection
