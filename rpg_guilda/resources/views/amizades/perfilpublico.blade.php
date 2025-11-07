@extends('layouts.app')

@section('title', 'Perfil de ' . $user->nome)

@section('content')
<div class="container py-5">

    {{-- Banner --}}
    <div class="position-relative mb-5 rounded overflow-hidden shadow-lg" style="height: 350px;">
        <div class="w-100 h-100"
             style="background-image: url('{{ $user->banner_url ?? asset('imagens/default-banner.jpg') }}');
                    background-size: cover;
                    background-position: center;
                    filter: var(--banner-filter, brightness(0.65));">
        </div>
    </div>

    {{-- Avatar --}}
    <div class="text-center mb-5 position-relative" style="margin-top: -120px;">
        <div class="position-relative d-inline-block">
            <img src="{{ $user->avatar_url ?? asset('imagens/default-avatar.png') }}"
                 alt="Avatar de {{ $user->nome }}"
                 class="rounded-circle border shadow-2xl"
                 style="width: 180px; height: 180px; object-fit: cover;
                        border-color: var(--highlight-color) !important; border-width: 5px !important;">
        </div>

        {{-- Nome --}}
        <div class="mt-4">
            <h2 class="fw-bold mb-2 text-highlight"
                style="text-shadow: 0 1px 3px rgba(0,0,0,0.8);">
                {{ $user->nome }}
            </h2>

            <p class="mb-1 text-light opacity-75 small">ID #{{ $user->id }}</p>

            {{-- Papel --}}
            <p class="text-light opacity-90">
                @if($user->is_admin ?? false)
                    👑 Administrador
                @elseif($user->papel === 'mestre')
                    🧙 Mestre de Jogo
                @else
                    🎮 Aventureiro
                @endif
            </p>

            {{-- Botões de Amizade --}}
            @auth
                @if(Auth::id() !== $user->id)
                    <div class="mt-4">

                        {{-- JÁ SÃO AMIGOS --}}
                        @if($friendshipStatus === 'friends')
                            <form action="{{ route('amigos.remover', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger fw-bold">
                                    <i class="bi bi-person-x-fill"></i> Remover Amigo
                                </button>
                            </form>

                        {{-- VOCÊ ENVIOU --}}
                        @elseif($friendshipStatus === 'pending_sent')
                            <form action="{{ route('amigos.cancelar', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-custom fw-bold">
                                    <i class="bi bi-clock-fill"></i> Pedido Enviado (Cancelar)
                                </button>
                            </form>

                        {{-- VOCÊ RECEBEU --}}
                        @elseif($friendshipStatus === 'pending_received')
                            <form action="{{ route('amigos.aceitar', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success fw-bold me-2">
                                    <i class="bi bi-check-circle-fill"></i> Aceitar
                                </button>
                            </form>

                            <form action="{{ route('amigos.rejeitar', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger fw-bold">
                                    <i class="bi bi-x-circle-fill"></i> Rejeitar
                                </button>
                            </form>

                        {{-- NENHUMA RELAÇÃO --}}
                        @else
                            <form action="{{ route('amigos.enviar', $user->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-custom fw-bold">
                                    <i class="bi bi-person-plus-fill"></i> Adicionar Amigo
                                </button>
                            </form>
                        @endif
                    </div>

                @else
                    {{-- Botão para o próprio usuário --}}
                    <a href="{{ route('usuarios.perfil') }}"
                       class="btn btn-outline-custom fw-bold mt-4">
                        <i class="bi bi-pencil-square"></i> Ver/Editar Meu Perfil
                    </a>
                @endif
            @endauth

        </div>
    </div>

    {{-- Biografia + Estatísticas --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card shadow-lg p-4"
                 style="background-color: var(--card-bg); border: 1px solid var(--card-border);">
                <h5 class="fw-bold text-highlight mb-3">📜 Biografia</h5>

                <p class="text-light opacity-90 fs-6">
                    {{ $user->biografia ?? 'Este aventureiro ainda não escreveu sua biografia.' }}
                </p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-lg p-4"
                 style="background-color: var(--card-bg); border: 1px solid var(--card-border);">

                <h5 class="fw-bold text-highlight text-center mb-4">📊 Estatísticas</h5>

                <div class="d-flex justify-content-around gap-3 flex-wrap">

                    {{-- Personagens --}}
                    <div class="text-center border rounded p-3"
                         style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                        <strong class="text-light d-block">Personagens</strong>
                        <span class="fw-bold fs-5 text-highlight">{{ $personagemCount }}</span>
                    </div>

                    {{-- Campanhas --}}
                    <div class="text-center border rounded p-3"
                         style="background: rgba(255,255,255,0.05); border-color: var(--card-border) !important;">
                        <strong class="text-light d-block">Campanhas</strong>
                        <span class="fw-bold fs-5 text-highlight">{{ $campanhas->count() }}</span>
                    </div>
                </div>

                {{-- Mestre Desde --}}
                @if($user->mestre_desde)
                    <div class="border-top mt-4 pt-3 text-center"
                         style="border-color: var(--card-border) !important;">
                        <h6 class="text-light opacity-85">Mestre desde:</h6>
                        <p class="fw-bold fs-4 text-highlight">
                            {{ \Carbon\Carbon::parse($user->mestre_desde)->format('m/Y') }}
                        </p>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Campanhas Ativas --}}
    <div class="card shadow-lg mb-5"
         style="background-color: var(--card-bg); border: 1px solid var(--card-border);">

        <div class="card-header text-center py-3"
             style="background: rgba(0,0,0,0.3); border-bottom-color: var(--card-border);">
            <h3 class="fw-bold text-highlight">🏕️ Campanhas Ativas</h3>
        </div>

        <div class="card-body">

            @if($campanhas->isEmpty())
                <p class="text-center text-light opacity-75 py-4">Nenhuma campanha pública encontrada.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.4);">
                                <th class="text-highlight fw-bold">Nome</th>
                                <th class="text-highlight fw-bold">Sistema</th>
                                <th class="text-highlight fw-bold">Status</th>
                                <th class="text-highlight fw-bold">Mestre</th>
                                <th class="text-highlight fw-bold">Players</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campanhas as $campanha)
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <td class="text-light">{{ $campanha->nome }}</td>
                                    <td class="text-light">{{ $campanha->sistemaRPG }}</td>
                                    <td>
                                        @if($campanha->status === 'ativa')
                                            <span class="badge bg-success">Ativa</span>
                                        @elseif($campanha->status === 'pausada')
                                            <span class="badge bg-warning text-dark">Pausada</span>
                                        @else
                                            <span class="badge bg-secondary">Encerrada</span>
                                        @endif
                                    </td>
                                    <td class="text-light">{{ $campanha->criador->nome ?? 'Desconhecido' }}</td>
                                    <td class="fw-bold text-highlight">{{ $campanha->jogadores->count() }}</td>
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
