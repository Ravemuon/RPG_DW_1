@extends('layouts.app')

@section('title', 'Usuários Cadastrados')

@section('content')
<div class="container py-5">
    <h1 class="fw-bold text-warning mb-4" style="text-shadow: 0 0 8px var(--btn-bg);">
        👥 Usuários Cadastrados
    </h1>

    @auth
        <div class="row g-4">
            @foreach($usuarios as $usuario)
                @if($usuario->id !== auth()->id()) {{-- Não mostrar o próprio usuário --}}
                    <div class="col-12 col-md-4">
                        <div class="card p-3 shadow-lg h-100 bg-dark border-warning">
                            <h5 class="fw-bold text-warning">{{ $usuario->nome }}</h5>
                            <p><strong>Email:</strong> {{ $usuario->email }}</p>

                            {{-- Botão de adicionar amigo --}}
                            <form action="{{ route('amizade.enviar', $usuario->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-info w-100">
                                    ➕ Adicionar Amigo
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <p class="text-center text-secondary fst-italic mt-4">
            Faça login para adicionar amigos.
        </p>
    @endauth
</div>
@endsection
