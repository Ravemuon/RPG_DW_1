@extends('layouts.app')

@section('title', 'Todas as Campanhas')

@section('content')
<div class="container py-5 bg-dark text-light min-vh-100">
    <h1 class="fw-bold text-warning mb-4">🗺️ Todas as Campanhas Disponíveis</h1>

    {{-- Filtros e ações --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <form action="{{ route('campanhas.todas') }}" method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm bg-dark text-light border-warning" placeholder="🔍 Pesquisar campanhas">
            <button type="submit" class="btn btn-warning btn-sm">Pesquisar</button>
        </form>
        <a href="{{ route('campanhas.create') }}" class="btn btn-success fw-bold">➕ Criar Nova Campanha</a>
    </div>

    <div class="row g-4">
        @forelse($todasCampanhas as $campanha)
            <div class="col-md-4">
                <div class="card bg-secondary text-light h-100 border-info shadow">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold">{{ $campanha->nome }}</h5>
                        <p class="card-text small text-warning">Mestre: {{ $campanha->criador->nome ?? 'Desconhecido' }}</p>
                        <p class="card-text small">{{ Str::limit($campanha->descricao, 80, '...') }}</p>
                        <p class="card-text small text-muted mb-3">
                            Sistema: {{ $campanha->sistema->nome ?? 'N/A' }} |
                            Status: <span class="badge bg-{{ $campanha->status === 'ativa' ? 'success' : 'secondary' }}">{{ ucfirst($campanha->status) }}</span> |
                            Privada: <span class="badge bg-{{ $campanha->privada ? 'warning text-dark' : 'info' }}">{{ $campanha->privada ? 'Sim' : 'Não' }}</span>
                        </p>

                        <div class="mt-auto d-flex gap-2 flex-wrap">
                            <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-outline-warning btn-sm">Ver Detalhes</a>

                            @auth
                                {{-- Solicitar entrada --}}
                                @if(auth()->id() !== $campanha->criador_id && !$campanha->jogadores->pluck('id')->contains(auth()->id()))
                                    <form action="{{ route('campanhas.solicitar', $campanha->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Deseja solicitar entrada nesta campanha?')">
                                            Pedir para Entrar
                                        </button>
                                    </form>
                                @endif

                                {{-- Editar/Excluir apenas para mestre/admin --}}
                                @if(auth()->id() === $campanha->criador_id || auth()->user()->tipo === 'administrador')
                                    <a href="{{ route('campanhas.edit', $campanha->id) }}" class="btn btn-outline-info btn-sm">✏️ Editar</a>

                                    <form action="{{ route('campanhas.destroy', $campanha->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Deseja realmente excluir esta campanha?')">
                                            🗑️ Excluir
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Nenhuma campanha disponível no momento.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $todasCampanhas->withQueryString()->links() }}
    </div>
</div>
@endsection
