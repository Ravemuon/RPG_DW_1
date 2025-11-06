@extends('layouts.app')

@section('title', 'Campanhas Disponíveis')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-warning mb-0" style="text-shadow: 0 0 8px var(--btn-bg);">
            🌟 Campanhas Disponíveis
        </h1>

        @auth
            <a href="{{ route('campanhas.create') }}" class="btn btn-outline-warning btn-lg px-4 py-2 shadow-lg">
                ✨ Criar Nova Campanha
            </a>
        @endauth
    </div>

    @php
        $campanhasPorSistema = $campanhas->groupBy('sistemaRPG');
        $userId = auth()->id();
    @endphp

    @forelse($campanhasPorSistema as $sistema => $campanhasDoSistema)
        <h3 class="text-warning mt-5 mb-3" style="text-shadow: 0 0 6px var(--btn-bg);">
            📜 Sistema: {{ $sistema }}
        </h3>
        <div class="row g-4">
            @foreach($campanhasDoSistema as $campanha)
                <div class="col-12 col-md-4">
                    <div class="card h-100 border-warning shadow-lg bg-gradient-dark p-3">
                        {{-- Nome da campanha --}}
                        <h4 class="fw-bold text-warning text-center mb-3" style="text-shadow: 0 0 6px #ffc107;">
                            {{ $campanha->nome }}
                        </h4>

                        {{-- Informações principais --}}
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-dark d-flex justify-content-between align-items-center border-warning">
                                <span>Mestre:</span>
                                <span class="badge bg-warning text-dark">{{ $campanha->criador->nome ?? 'Desconhecido' }}</span>
                            </li>
                            <li class="list-group-item bg-dark d-flex justify-content-between align-items-center border-warning">
                                <span>Players:</span>
                                <span class="badge bg-info text-dark">{{ $campanha->jogadores->count() }}</span>
                            </li>
                            <li class="list-group-item bg-dark border-warning">
                                <strong>Missões realizadas:</strong> {{ $campanha->missoes->count() }}
                            </li>
                            <li class="list-group-item bg-dark border-warning">
                                <strong>Próximo encontro:</strong>
                                {{ $campanha->proximo_encontro ? $campanha->proximo_encontro->format('d/m/Y H:i') : 'Não agendado' }}
                            </li>
                        </ul>

                        {{-- Botões de ação --}}
                        @auth
                            @if($campanha->jogadores->contains($userId))
                                <span class="badge bg-success w-100 py-2 text-center">🎯 Já participando</span>
                            @else
                                @if($campanha->privada)
                                    <form action="{{ route('campanhas.entrar', $campanha->id) }}" method="POST" class="d-flex flex-column gap-2">
                                        @csrf
                                        <input type="text" name="codigo" class="form-control" placeholder="Código de acesso">
                                        <button type="submit" class="btn btn-warning w-100">🔑 Entrar</button>
                                        @error('codigo')
                                            <p class="text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </form>
                                @else
                                    <a href="{{ route('campanhas.show', $campanha->id) }}" class="btn btn-warning w-100">
                                        🔍 Entrar
                                    </a>
                                @endif
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-center text-secondary fst-italic mt-4">
            Nenhuma campanha disponível no momento.
        </p>
    @endforelse
</div>
@endsection
