{{-- resources/views/campanhas/todas.blade.php --}}
@extends('layouts.app')

@section('title', 'Todas as Campanhas')

@section('content')
<div class="container py-5">
    <h1 class="text-warning text-center mb-5" style="text-shadow: 0 0 8px var(--btn-bg);">
        📜 Todas as Campanhas
    </h1>

    {{-- Botões de navegação --}}
    @auth
        <div class="text-center mb-4 d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('campanhas.minhas') }}"
               class="btn btn-outline-warning fw-bold px-4 py-2"
               style="text-shadow: 0 0 4px var(--btn-bg);">
                🧭 Minhas Campanhas
            </a>

            <a href="{{ route('campanhas.create') }}"
               class="btn btn-success fw-bold px-4 py-2"
               style="background-color: var(--btn-bg); color: var(--btn-text); text-shadow: 0 0 4px var(--btn-bg);">
                ✨ Criar Nova Campanha
            </a>
        </div>
    @endauth

    {{-- Campo de busca --}}
    <form method="GET" action="{{ route('campanhas.todas') }}" class="mb-4 d-flex justify-content-center gap-2 flex-wrap">
        <input type="text" name="search" class="form-control w-auto" placeholder="Buscar por nome..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-warning fw-bold">🔍 Buscar</button>
    </form>

    @php $userId = auth()->id(); @endphp

    @forelse($campanhasPorSistema as $sistemaNome => $campanhasDoSistema)
        <h3 class="text-warning mt-5 mb-3 p-2 rounded bg-dark bg-opacity-50"
            style="text-shadow: 0 0 6px var(--btn-bg);">
            📜 Sistema: {{ $sistemaNome }}
        </h3>

        <div class="row g-4">
            @foreach($campanhasDoSistema as $campanha)
                @php
                    // Ordena as sessões por data
                    $sessoesOrdenadas = $campanha->sessoes->sortBy('data');

                    // Próxima sessão
                    $proximaSessao = $sessoesOrdenadas
                        ->filter(fn($s) => $s->data >= now())
                        ->sortBy('data')
                        ->first();

                    // Última sessão
                    $ultimaSessao = $sessoesOrdenadas
                        ->filter(fn($s) => $s->data < now())
                        ->sortByDesc('data')
                        ->first();

                    $jogador = $campanha->jogadores->firstWhere('id', $userId);
                    $status = $jogador?->pivot?->status ?? null;
                @endphp

                <div class="col-12 col-md-4">
                    <div class="card h-100 border-warning shadow-lg p-3"
                         style="background-color: var(--card-bg); border-color: var(--card-border);">

                        {{-- Imagem da campanha --}}
                        @if($campanha->imagem)
                            <img src="{{ asset('storage/' . $campanha->imagem) }}"
                                 class="card-img-top mb-3 rounded" alt="{{ $campanha->nome }}">
                        @endif

                        {{-- Nome da campanha --}}
                        <h4 class="fw-bold text-warning text-center mb-3 p-2 rounded"
                            style="background-color: var(--card-header-bg); text-shadow: 0 0 6px var(--btn-bg);">
                            {{ $campanha->nome }}
                        </h4>

                        {{-- Informações --}}
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                style="background-color: var(--card-bg); border-color: var(--card-border);">
                                <span class="text-light">Mestre:</span>
                                <span class="badge bg-warning text-dark fs-6">
                                    {{ $campanha->criador->nome ?? 'Desconhecido' }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                style="background-color: var(--card-bg); border-color: var(--card-border);">
                                <span class="text-light">Players:</span>
                                <span class="badge bg-info text-dark fs-6">{{ $campanha->jogadores->count() }}</span>
                            </li>

                            <li class="list-group-item"
                                style="background-color: var(--card-bg); border-color: var(--card-border); color: var(--bs-body-color);">
                                <strong>Sistema:</strong> {{ $campanha->sistema_rpg }}
                            </li>

                            <li class="list-group-item"
                                style="background-color: var(--card-bg); border-color: var(--card-border); color: var(--bs-body-color);">
                                <strong>Missões realizadas:</strong> {{ $campanha->missoes->count() }}
                            </li>

                            <li class="list-group-item"
                                style="background-color: var(--card-bg); border-color: var(--card-border); color: var(--bs-body-color);">
                                <strong>Última sessão:</strong>
                                {{ $ultimaSessao?->data ? $ultimaSessao->data->format('d/m/Y H:i') : 'Nenhuma sessão ainda' }}
                            </li>

                            <li class="list-group-item"
                                style="background-color: var(--card-bg); border-color: var(--card-border); color: var(--bs-body-color);">
                                <strong>Próximo encontro:</strong>
                                {{ $proximaSessao?->data ? $proximaSessao->data->format('d/m/Y H:i') : 'Não agendado' }}
                            </li>
                        </ul>

                        {{-- Botões --}}
                        @auth
                            @if($status === 'ativo' || $status === 'mestre')
                                <a href="{{ route('campanhas.show', $campanha->id) }}"
                                   class="btn w-100 fw-bold"
                                   style="background-color: var(--btn-bg); color: var(--btn-text);">
                                    🎯 Participando ({{ $status === 'mestre' ? 'Mestre' : 'Jogador' }})
                                </a>
                            @elseif($status === 'pendente')
                                <a href="{{ route('campanhas.show', $campanha->id) }}"
                                   class="btn btn-info w-100 fw-bold">
                                    ⏳ Solicitação Enviada
                                </a>
                            @else
                                <form action="{{ route('campanhas.entrar', $campanha->id) }}"
                                      method="POST" class="d-flex flex-column gap-2">
                                    @csrf
                                    @if($campanha->privada)
                                        <input type="text" name="codigo"
                                               class="form-control bg-secondary text-light"
                                               placeholder="Código de acesso" required>
                                        <button type="submit"
                                                class="btn w-100 fw-bold"
                                                style="background-color: var(--btn-bg); color: var(--btn-text);">
                                            🔑 Solicitar Entrada (Privada)
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="btn w-100 fw-bold"
                                                style="background-color: var(--btn-bg); color: var(--btn-text);">
                                            🔓 Solicitar Entrada (Pública)
                                        </button>
                                    @endif
                                    @error('codigo')
                                        <p class="text-danger mt-1">{{ $message }}</p>
                                    @enderror
                                </form>
                            @endif
                        @endauth

                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-warning w-100 fw-bold">
                                🔐 Faça login para participar
                            </a>
                        @endguest

                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-light fst-italic fs-5 mt-4">Nenhuma campanha disponível no momento.</p>
    @endforelse
</div>
@endsection
