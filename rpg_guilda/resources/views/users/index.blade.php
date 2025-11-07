@extends('layouts.app')

@section('title', 'Aventureiros e Amizades')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-warning fw-bold text-center">Portal dos Aventureiros</h1>

    {{-- ===========================
         🔍 Busca de Usuários
    ============================ --}}
    <form action="{{ route('usuarios.index') }}" method="GET" class="mb-4 text-center">
        <div class="input-group w-50 mx-auto">
            <input type="text" name="search" class="form-control"
                   placeholder="Buscar por nome ou ID..."
                   value="{{ request('search') }}">
            <button class="btn btn-warning" type="submit">Buscar</button>
        </div>
    </form>

    {{-- ===========================
         💌 Convites Pendentes
    ============================ --}}
    @if($pendentes->count())
        <h3 class="text-light mb-3">Convites Pendentes</h3>
        <div class="row g-3 mb-5">
            @foreach($pendentes as $amizade)
                @php
                    $amigo = $amizade->outroUsuario(auth()->id());
                @endphp
                <div class="col-md-3">
                    <div class="card bg-dark border-warning text-center p-3 shadow">
                        <img src="{{ $amigo->avatar_url ?? asset('images/default-avatar.png') }}"
                             alt="{{ $amigo->name }}"
                             class="rounded-circle mx-auto mb-2 border border-warning"
                             width="80" height="80">
                        <h5 class="text-light">{{ $amigo->name }}</h5>

                        <div class="mt-2">
                            <form action="{{ route('amizade.aceitar', $amizade->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Aceitar</button>
                            </form>

                            <form action="{{ route('amizade.recusar', $amizade->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Recusar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ===========================
         🧑 Lista de Usuários
    ============================ --}}
    @if($usuarios->count())
        <h3 class="text-light mb-3">Todos os Aventureiros</h3>
        <div class="row g-4">
            @foreach($usuarios as $usuario)
                @php
                    $amizade = $amizades->firstWhere('friend_id', $usuario->id)
                                ?? $amizades->firstWhere('user_id', $usuario->id);
                @endphp

                <div class="col-md-3">
                    <div class="card bg-dark border-secondary text-center p-3 shadow-sm">
                        <img src="{{ $usuario->avatar_url ?? asset('images/default-avatar.png') }}"
                             alt="{{ $usuario->name }}"
                             class="rounded-circle mx-auto mb-2 border border-warning"
                             width="80" height="80">
                        <h5 class="text-light">{{ $usuario->name }}</h5>

                        {{-- Botão dinâmico --}}
                        @if($usuario->id !== auth()->id())
                            @if(!$amizade)
                                <form action="{{ route('amizade.enviar', $usuario->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-warning btn-sm mt-2">Adicionar Amigo</button>
                                </form>
                            @elseif($amizade->isPendente())
                                <button class="btn btn-secondary btn-sm mt-2" disabled>Convite Enviado</button>
                            @elseif($amizade->isAceita())
                                <button class="btn btn-success btn-sm mt-2" disabled>Amigos</button>
                            @elseif($amizade->isRecusada())
                                <button class="btn btn-danger btn-sm mt-2" disabled>Recusado</button>
                            @endif
                        @endif

                        {{-- Ver perfil --}}
                        <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-outline-light btn-sm mt-2">
                            Ver Perfil
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center text-secondary">Nenhum usuário encontrado.</p>
    @endif
</div>
@endsection
