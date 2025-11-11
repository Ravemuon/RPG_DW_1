@extends('layouts.app')

@section('title', "Perfil de {$user->nome}")

@section('content')
<div class="container mt-4">

    @include('amizades.partials._alertas')

    {{-- CABEÇALHO / REDIRECIONAMENTOS --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-highlight">👤 Perfil de Usuário</h4>
            <small class="text-muted">Visualize informações e interaja com este aventureiro</small>
        </div>

        <div class="card-body text-center">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('amizades.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    🏠 Resumo   
                </a>
                <a href="{{ route('amizades.amigos') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    👥 Meus Amigos
                </a>
                <a href="{{ route('amizades.pendentes') }}" class="btn btn-outline-warning btn-lg rounded-pill px-4">
                    ⚡ Solicitações
                </a>
                <a href="{{ route('amizades.procurar') }}" class="btn btn-outline-info btn-lg rounded-pill px-4">
                    🔍 Procurar Usuários
                </a>
            </div>
        </div>
    </div>

    {{-- BANNER --}}
    <div class="position-relative rounded shadow mb-5 overflow-hidden" style="height: 300px;">
        <div
            class="w-100 h-100"
            style="
                background-image: url('{{ $user->banner_url }}');
                background-size: cover;
                background-position: center;
                filter: brightness(0.7);
            "
        ></div>
    </div>

    {{-- AVATAR + NOME --}}
    <div class="text-center mb-4" style="margin-top: -100px;">
        <img src="{{ $user->avatar_url }}" class="rounded-circle border shadow"
             style="width: 160px; height: 160px; object-fit: cover;">
        <h3 class="mt-3 fw-bold text-highlight">{{ $user->nome }}</h3>
        <p class="text-muted small">@ {{ $user->username }}</p>

        {{-- AÇÕES DE AMIZADE / CHAT --}}
        @if(Auth::id() !== $user->id)
            <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">

                @if($ehAmigo)
                    {{-- Já são amigos --}}
                    <form action="{{ route('amizades.remover', $amizadeId) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger rounded-pill fw-bold px-4">
                            ❌ Remover Amigo
                        </button>
                    </form>


                @elseif($solicitacaoPendente)
                    {{-- Solicitação já enviada --}}
                    <form action="{{ route('amizades.remover', $solicitacaoPendente->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-warning rounded-pill fw-bold px-4">
                            ⏳ Solicitação Enviada (Cancelar)
                        </button>
                    </form>

                @else
                    {{-- Ainda não são amigos --}}
                    <form action="{{ route('amizades.adicionar', $user->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-light rounded-pill fw-bold px-4">
                            🤝 Adicionar Amigo
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- ESTATÍSTICAS --}}
    <div class="row justify-content-center text-center mb-4">
        <div class="col-md-3">
            <div class="card bg-dark text-light shadow-sm border-0">
                <div class="card-body">
                    <h6>Personagens</h6>
                    <h3 class="text-highlight">{{ $user->personagens->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-light shadow-sm border-0">
                <div class="card-body">
                    <h6>Campanhas</h6>
                    <h3 class="text-highlight">{{ $user->campanhas->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- BIOGRAFIA --}}
    <div class="card shadow-lg border-0">
        <div class="card-body text-light">
            <h5 class="text-highlight mb-3 fw-bold">📜 Biografia</h5>
            <p>{{ $user->biografia ?? 'Este aventureiro ainda não compartilhou sua história.' }}</p>
        </div>
    </div>

</div>
@endsection
