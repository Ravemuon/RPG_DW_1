@extends('layouts.app')

@section('title', 'Meus Amigos')

@section('content')
<div class="container py-4">

    {{-- Alertas de sucesso / erro --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning fw-bold">Meus Amigos</h1>
        <div>
            <a href="{{ route('amizades.pendentes') }}" class="btn btn-outline-light">
                Pedidos Pendentes
            </a>
        </div>
    </div>

    {{-- Amigos atuais --}}
    @if($amigos->count())
        <div class="row g-3">
            @foreach($amigos as $usuario)
                <div class="col-md-3">
                    <div class="card text-center p-3 bg-dark text-light border-0">
                        <img src="{{ $usuario->avatar_url ?? asset('imagens/default-avatar.png') }}"
                             class="rounded-circle mb-2 border border-warning"
                             width="80" height="80">
                        <h5 class="fw-semibold">{{ $usuario->nome }}</h5>

                        <a href="{{ route('usuarios.show', $usuario->id) }}"
                           class="btn btn-outline-warning btn-sm w-100 mb-2">
                            Ver Perfil
                        </a>

                        <form action="{{ route('amigos.remover', $usuario->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm w-100">
                                Desfazer Amizade
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $amigos->links() }}
        </div>
    @else
        <p class="text-muted">Você ainda não tem amigos adicionados.</p>
    @endif

    <hr class="my-5 border-warning">

    {{-- Seção para adicionar amigos --}}
    <h2 class="text-warning fw-bold mb-3">Adicionar Amigos</h2>

    @if($possiveisAmigos->count())
        <div class="row g-3">
            @foreach($possiveisAmigos as $usuario)
                <div class="col-md-3">
                    <div class="card text-center p-3 bg-dark text-light border-0">
                        <img src="{{ $usuario->avatar_url ?? asset('imagens/default-avatar.png') }}"
                             class="rounded-circle mb-2 border border-secondary"
                             width="80" height="80">
                        <h5 class="fw-semibold">{{ $usuario->nome }}</h5>

                        @php
                            $status = $usuario->amizade_status ?? null;
                        @endphp

                        @if($status === 'pendente_enviado')
                            <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                🕓 Pendente
                            </button>
                        @elseif($status === 'pendente_recebido')
                            <form action="{{ route('amizades.aceitar', $usuario->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-primary btn-sm w-100">
                                    💬 Aceitar Amizade
                                </button>
                            </form>
                        @elseif($status === 'aceito')
                            <button class="btn btn-outline-success btn-sm w-100" disabled>
                                ✅ Amigos
                            </button>
                        @else
                            <form action="{{ route('amigos.adicionar', $usuario->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-success btn-sm w-100">
                                    Adicionar Amigo
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $possiveisAmigos->links() }}
        </div>
    @else
        <p class="text-muted">Nenhum usuário disponível para adicionar.</p>
    @endif

</div>
@endsection
