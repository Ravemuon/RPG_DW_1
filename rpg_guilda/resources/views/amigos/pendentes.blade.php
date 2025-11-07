@extends('layouts.app')

@section('title', 'Solicitações Pendentes')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning fw-bold">Solicitações Pendentes</h1>
        <a href="{{ route('amigos.index') }}" class="btn btn-outline-light">
            ← Voltar aos Amigos
        </a>
    </div>

    {{-- Mensagens de feedback --}}
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

    {{-- Lista de solicitações --}}
    @if($solicitacoes->count())
        <div class="row g-3">
            @foreach($solicitacoes as $amizade)
                @php
                    $usuario = $amizade->user; // Quem enviou o pedido
                @endphp

                <div class="col-md-3">
                    <div class="card text-center p-3 bg-dark text-light border-0 shadow-sm">
                        <img src="{{ $usuario->avatar_url ?? asset('imagens/default-avatar.png') }}"
                             class="rounded-circle mx-auto mb-2 border border-warning"
                             width="80" height="80">

                        <h5 class="fw-semibold mb-2">{{ $usuario->nome }}</h5>

                        <div class="d-flex flex-column gap-2">
                            {{-- Ver perfil --}}
                            <a href="{{ route('usuarios.show', $usuario->id) }}"
                               class="btn btn-outline-warning btn-sm w-100">
                                Ver Perfil
                            </a>

                            {{-- Aceitar amizade --}}
                            <form action="{{ route('amizades.aceitar', $usuario->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-success btn-sm w-100">
                                    Aceitar
                                </button>
                            </form>

                            {{-- Recusar --}}
                            <form action="{{ route('amigos.remover', $amizade->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm w-100">
                                    Recusar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $solicitacoes->links() }}
        </div>
    @else
        <p class="text-muted">Nenhuma solicitação pendente no momento.</p>
    @endif
</div>
@endsection
