procurar.blade.php
@extends('layouts.app')

@section('title', 'Procurar Usuários')

@section('content')
<div class="container py-4">

    <h1 class="text-warning fw-bold mb-4">Procurar Usuários</h1>

    <form method="GET" action="{{ route('usuarios.procurar') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control"
                   placeholder="Buscar usuário por nome..." value="{{ request('q') }}">
            <button class="btn btn-warning">Buscar</button>
        </div>
    </form>

    @if($usuarios->count())
        <div class="row g-3">

            @foreach($usuarios as $usuario)
                <div class="col-md-3">
                    <div class="card text-center p-3 bg-dark text-light">

                        <img src="{{ $usuario->avatar_url ?? asset('imagens/default-avatar.png') }}"
                             class="rounded-circle mx-auto mb-2 border border-warning"
                             width="80" height="80">

                        <h5>{{ $usuario->nome }}</h5>

                        <a href="{{ route('usuarios.show', $usuario->id) }}"
                           class="btn btn-outline-warning btn-sm mt-2 w-100">Ver Perfil</a>

                        <form action="{{ route('amigos.enviar', $usuario->id) }}" method="POST" class="mt-2">
                            @csrf
                            <button class="btn btn-warning btn-sm w-100">Adicionar Amigo</button>
                        </form>

                    </div>
                </div>
            @endforeach

        </div>

        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    @else
        <p class="text-muted">Nenhum usuário encontrado.</p>
    @endif

</div>
@endsection
