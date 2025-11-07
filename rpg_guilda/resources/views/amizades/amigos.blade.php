@extends('layouts.app')

@section('title', 'Meus Amigos')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        {{-- Título usando a variável de destaque do tema --}}
        <h1 class="text-highlight fw-bold mb-3 mb-md-0">
            <i class="bi bi-people-fill me-2"></i> Meus Amigos
        </h1>

        {{-- Botões de navegação para amigos e pendentes --}}
        <div class="btn-group" role="group" aria-label="Navegação de Amigos">
            {{-- Botão ativo (Amigos Adicionados) --}}
            <a href="{{ route('usuarios.amigos') }}" class="btn btn-custom me-2">
                Amigos Adicionados
            </a>
            {{-- Botão inativo (Pedidos Pendentes) --}}
            <a href="{{ route('usuarios.pendentes') }}" class="btn btn-outline-custom">
                Pedidos Pendentes
            </a>
        </div>
    </div>

    @if($usuarios->count())
        <div class="row g-4">
            {{-- Itera sobre a lista de usuários (amigos) --}}
            @foreach($usuarios as $usuario)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    {{-- Card usando as variáveis de tema --}}
                    <div class="card text-center p-3 h-100">

                        <img src="{{ $usuario->avatar_url ?? asset('imagens/default-avatar.png') }}"
                             alt="{{ $usuario->nome }}"
                             class="rounded-circle mx-auto mb-3"
                             style="width: 80px; height: 80px; border: 3px solid var(--highlight-color);"
                             onerror="this.onerror=null; this.src='{{ asset('imagens/default-avatar.png') }}';">

                        <h5 class="fw-semibold">{{ $usuario->nome }}</h5>
                        <p class="text-muted small">Mestre de {{$usuario->campanhas_mestradas_count ?? 0}} / Jogador em {{$usuario->campanhas_jogadas_count ?? 0}}</p>

                        <div class="mt-auto">
                            <a href="{{ route('usuarios.show', $usuario->id) }}"
                               class="btn btn-outline-custom btn-sm mt-2 w-100">
                                <i class="bi bi-person-lines-fill"></i> Ver Perfil
                            </a>

                            {{-- Ação para remover o amigo --}}
                            <form method="POST" action="{{ route('amigos.remover', $usuario->id) }}" class="d-inline mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100 mt-2">
                                    <i class="bi bi-person-x-fill"></i> Desfazer Amizade
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Exemplo de Paginação --}}
        <div class="d-flex justify-content-center mt-5">
             {{ $usuarios->links() }}
        </div>

    @else
        <div class="card p-5 text-center">
            <h4 class="text-muted">Você ainda não adicionou nenhum amigo.</h4>
            <p class="mt-3">
                Que tal procurar novos companheiros de aventura?
                <a href="{{ route('usuarios.procurar') }}" class="text-highlight fw-bold">
                    Clique aqui para encontrar outros usuários.
                </a>
            </p>
        </div>
    @endif
</div>
@endsection
