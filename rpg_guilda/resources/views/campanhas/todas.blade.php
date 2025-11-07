{{-- resources/views/campanhas/todas.blade.php --}}
@extends('layouts.app') {{-- Certifique-se de ter um layout base --}}

@section('title', 'Todas as Campanhas')

@section('content')
<div class="container py-5">
    <h1 class="text-warning text-center mb-5" style="text-shadow: 0 0 8px var(--btn-bg);">
        📜 Todas as Campanhas
    </h1>

    {{-- Botão Criar Campanha --}}
    @auth
        <div class="text-center mb-4">
            <a href="{{ route('campanhas.create') }}"
               class="btn btn-success fw-bold px-4 py-2"
               style="background-color: var(--btn-bg); color: var(--btn-text); text-shadow: 0 0 4px var(--btn-bg);">
                ✨ Criar Nova Campanha
            </a>
        </div>
    @endauth

    @php
        $userId = auth()->id();
    @endphp

    @forelse($campanhasPorSistema as $sistemaNome => $campanhasDoSistema)
        <h3 class="text-warning mt-5 mb-3 p-2 rounded bg-dark bg-opacity-50"
            style="text-shadow: 0 0 6px var(--btn-bg);">
            📜 Sistema: {{ $sistemaNome }}
        </h3>

        <div class="row g-4">
            @foreach($campanhasDoSistema as $campanha)
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

                        {{-- Informações principais --}}
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
                                <strong>Próximo encontro:</strong>
                                {{ $campanha->proximo_encontro ? $campanha->proximo_encontro->format('d/m/Y H:i') : 'Não agendado' }}
                            </li>
                        </ul>

                        {{-- Botões de ação --}}
                        @auth
                            @php
                                $jogador = $campanha->jogadores->firstWhere('id', $userId);
                                $status = $jogador->pivot->status ?? null;
                            @endphp

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
                                               placeholder="Código de acesso">
                                        <button type="submit"
                                                class="btn w-100 fw-bold"
                                                style="background-color: var(--btn-bg); color: var(--btn-text);">
                                            🔑 Entrar (Privada)
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
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-light fst-italic fs-5 mt-4">Nenhuma campanha disponível no momento.</p>
    @endforelse
</div>
@endsection
