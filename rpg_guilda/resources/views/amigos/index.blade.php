@extends('layouts.app')

@section('title', 'Lista de Usuários')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning fw-bold">Lista de Usuários</h1>

        {{-- Botões de navegação para amigos e pendentes --}}
        <div>
            <a href="{{ route('usuarios.amigos') }}" class="btn btn-outline-warning me-2">
                Amigos Adicionados
            </a>
            <a href="{{ route('usuarios.pendentes') }}" class="btn btn-outline-light">
                Pedidos Pendentes
            </a>
        </div>
    </div>

    @if($usuarios->count())
        <div class="row g-3">
            @foreach($usuarios as $usuario)
                <div class="col-md-3">
                    <div class="card text-center p-3 shadow-sm border-0 bg-dark text-light">

                        {{-- avatar --}}
                        <img src="{{ $usuario->avatar_url ?? asset('imagens/default-avatar.png') }}"
                             alt="{{ $usuario->nome }}"
                             class="rounded-circle mx-auto mb-2 border border-warning"
                             width="80" height="80">

                        <h5 class="fw-semibold">{{ $usuario->nome }}</h5>

                        <div class="mt-2 w-100">

                            {{-- Ver Perfil público --}}
                            <a href="{{ route('usuarios.show', $usuario->id) }}"
                               class="btn btn-outline-warning btn-sm w-100 mb-2">
                                Ver Perfil
                            </a>

                            {{-- Adicionar Amigo --}}
                            <form action="{{ route('amigos.enviar', $usuario->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                    Adicionar Amigo
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <p class="text-muted">Nenhum usuário encontrado.</p>
    @endif
</div>
@endsection
