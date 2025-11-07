@extends('layouts.app')

@section('title', 'Perfil de {{ $user->nome }}')

@section('content')
<div class="container py-5">

    {{-- Banner (Apenas Visualização) --}}
    <div class="position-relative mb-5 rounded overflow-hidden shadow-lg" style="height: 350px;">
        {{-- Usando fallback para o banner, e a variável CSS para o brilho/filtro --}}
        <div class="w-100 h-100"
             style="background-image: url('{{ $user->banner_url ?? asset('imagens/default-banner.jpg') }}');
                    background-size: cover;
                    background-position: center;
                    filter: var(--banner-filter, brightness(0.65));">
        </div>
    </div>

    {{-- Avatar e Info --}}
    <div class="text-center mb-5 position-relative" style="margin-top: -120px;">
        <div class="position-relative d-inline-block">
            <img src="{{ $user->avatar_url ?? asset('imagens/default-avatar.png') }}"
                 alt="Avatar de {{ $user->nome }}"
                 class="rounded-circle border shadow-2xl"
                 style="width: 180px; height: 180px; object-fit: cover;
                        border-color: var(--highlight-color) !important; border-width: 5px !important;">
        </div>

        <div class="mt-4">
            {{-- Nome do Usuário --}}
            <h2 class="fw-bold mb-2 text-highlight" style="text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $user->nome }}</h2>
            <p class="mb-1 text-light opacity-80 small">ID de Usuário: {{ $user->id }}</p>

            {{-- Papel/Status --}}
            <p class="mb-3 text-light opacity-90">
                @if($user->is_admin ?? false)
                    👑 Administrador
                @elseif($user->papel === 'mestre')
                    🧙 Mestre de Jogo
                @else
                    🎮 Aventureiro
                @endif
            </p>

            {{-- BOTÃO DE AMIZADE --}}
            @auth
                @if(Auth::user()->id !== $user->id)
                    <div class="mt-4">
                        {{-- Presumindo que você passa a variável $friendshipStatus para a view --}}
                        @if($friendshipStatus === 'friends')
                            <form action="{{ route('amigos.remover', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger fw-bold">
                                    <i class="bi bi-person-x-fill"></i> Remover Amigo
                                </button>
                            </form>
                        @elseif($friendshipStatus === 'pending_sent')
                            <form action="{{ route('amigos.cancelar', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-custom fw-bold">
                                    <i class="bi bi-clock-fill"></i> Pedido Enviado (Cancelar)
                                </button>
                            </form>
                        @elseif($friendshipStatus === 'pending_received')
                            <form action="{{ route('amigos.aceitar', $user->id) }}" method="POST" class="d-inline me-2">
                                @csrf
                                <button type="submit" class="btn btn-success fw-bold">
                                    <i class="bi bi-check-circle-fill"></i> Aceitar Pedido
                                </button>
                            </form>
                            <form action="{{ route('amigos.rejeitar', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger fw-bold">
                                    <i class="bi bi-x-circle-fill"></i> Rejeitar
                                </button>
                            </form>
                        @else {{-- 'none' --}}
                            <form action="{{ route('amigos.adicionar', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-custom fw-bold">
                                    <i class="bi bi-person-plus-fill"></i> Adicionar Amigo
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    {{-- Botão para o próprio usuário logado --}}
                    <a href="{{ route('usuarios.perfil') }}" class="btn btn-outline-custom fw-bold mt-4">
                        <i class="bi bi-pencil-square"></i> Ver Meu Perfil (Editar)
                    </a>
                @endif
            @endauth
            {{-- FIM DO BOTÃO DE AMIZADE --}}

        </div>
    </div>

    {{-- Biografia e Estatísticas --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card h-100 shadow-lg p-4" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold text-highlight">📜 Biografia</h5>
                    <p class="mb-0 text-light opacity-90" style="line-height: 1.6; font-size: 1.05rem;">
                        {{ $user->biografia ?? 'Este aventureiro ainda não escreveu sua biografia.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 shadow-lg p-4" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="card-body text-center">
                    <h5 class="card-title mb-4 fw-bold text-highlight">📊 Estatísticas de RPG</h5>

                    <div class="d-flex justify-content-around flex-wrap gap-3 mb-4">
                        {{-- Contagem de Personagens --}}
                        <div class="rounded p-3 shadow-sm flex-fill text-center border" style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                            <strong class="d-block text-light mb-1">Personagens</strong>
                            <span class="fw-bold fs-5 text-highlight">{{ $personagemCount }}</span>
                        </div>
                        {{-- Contagem de Campanhas --}}
                        <div class="rounded p-3 shadow-sm flex-fill text-center border" style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                            <strong class="d-block text-light mb-1">Campanhas</strong>
                            <span class="fw-bold fs-5 text-highlight">{{ $campanhas->count() }}</span>
                        </div>
                    </div>

                    {{-- Mestre desde... --}}
                    @if($user->mestre_desde)
                        <div class="mt-4 pt-3 border-top" style="border-color: var(--card-border) !important;">
                            <h6 class="fw-bold mb-1 text-light">Mestre de Jogo desde</h6>
                            <p class="fs-4 fw-bold text-highlight">{{ \Carbon\Carbon::parse($user->mestre_desde)->format('M/Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Campanhas Ativas (Tabela) --}}
    <div class="card shadow-lg mb-5" style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
        <div class="card-header text-center py-3" style="border-bottom-color: var(--card-border); background: rgba(0,0,0,0.3);">
            <h3 class="mb-0 fw-bold text-highlight">🏕️ Campanhas Ativas</h3>
        </div>
        <div class="card-body">
            @if($campanhas->isEmpty())
                <p class="text-center text-light opacity-75 py-4">Este aventureiro não participa de nenhuma campanha pública no momento. ⚔️</p>
            @else
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.4);">
                                <th class="fw-bold py-3 text-highlight" style="border-bottom: 2px solid var(--card-border);">Nome</th>
                                <th class="fw-bold py-3 text-highlight" style="border-bottom: 2px solid var(--card-border);">Sistema</th>
                                <th class="fw-bold py-3 text-highlight" style="border-bottom: 2px solid var(--card-border);">Status</th>
                                <th class="fw-bold py-3 text-highlight" style="border-bottom: 2px solid var(--card-border);">Mestre</th>
                                <th class="fw-bold py-3 text-highlight" style="border-bottom: 2px solid var(--card-border);">Players</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campanhas as $campanha)
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td class="py-3 text-light fw-medium">{{ $campanha->nome }}</td>
                                    <td class="py-3 text-light">{{ $campanha->sistemaRPG }}</td>
                                    <td class="py-3">
                                        @if($campanha->status === 'ativa')
                                            <span class="badge bg-success px-3 py-2 fw-medium">Ativa</span>
                                        @elseif($campanha->status === 'pausada')
                                            <span class="badge bg-warning text-dark px-3 py-2 fw-medium">Pausada</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 fw-medium">Encerrada</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-light">{{ $campanha->criador->nome ?? 'Desconhecido' }}</td>
                                    <td class="py-3 text-highlight fw-bold">{{ $campanha->jogadores->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
