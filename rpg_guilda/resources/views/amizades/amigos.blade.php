@extends('layouts.app')

@section('title', 'Amigos')

@section('content')
<div class="container mt-4">

    {{-- ALERTAS --}}
    @include('amizades.partials._alertas')

    {{-- ============================
        RESUMO / REDIRECIONAMENTOS
    ============================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-highlight">Central de Amizades</h4>
            <small class="text-muted">Gerencie suas conexões e convites</small>
        </div>

        <div class="card-body text-center">
            <p class="text-muted mb-4">
                Acompanhe suas solicitações, encontre novos amigos ou visualize suas conexões atuais.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('amizades.amigos') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    👥 Meus Amigos
                </a>

                <a href="{{ route('amizades.pendentes') }}" class="btn btn-outline-warning btn-lg rounded-pill px-4">
                    ⚡ Solicitações Pendentes
                </a>

                <a href="{{ route('amizades.procurar') }}" class="btn btn-outline-info btn-lg rounded-pill px-4">
                    🔍 Procurar Usuários
                </a>
            </div>
        </div>
    </div>


    {{-- ============================
         SUGESTÕES DE AMIZADE
    ============================= --}}
    @if(isset($sugestoes) && $sugestoes->isNotEmpty())
        <h5 class="mt-5 mb-3 text-highlight">⭐ Sugestões para Você</h5>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 mb-5">
            @foreach($sugestoes as $usuario)
                <div class="col">
                    {{-- card do usuário com botões de adicionar/pendente --}}
                    @include('amizades.partials._card_usuario', ['usuario' => $usuario])
                </div>
            @endforeach
        </div>

        <hr>
    @endif


    {{-- ============================
          AMIGOS ATUAIS
    ============================= --}}
    <h5 class="mt-4 mb-3 text-highlight">
        🤝 Seus Amigos Atuais ({{ $amigos->total() ?? 0 }})
    </h5>

    @if(isset($amigos) && $amigos->count() > 0)
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">

            @foreach($amigos as $usuario)
                <div class="col">
                    {{-- reuso do card, porém com modo amigo desabilitando botão de adicionar --}}
                    @include('amizades.partials._card_usuario', [
                        'usuario' => $usuario,
                        'is_friend' => true
                    ])
                </div>
            @endforeach

        </div>

        {{-- paginação --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $amigos->links('pagination::bootstrap-5') }}
        </div>

    @else
        <div class="alert alert-secondary text-center mt-3">
            Você ainda não tem amigos conectados.
            <br>
            <a href="{{ route('amizades.procurar') }}" class="text-decoration-underline">
                Clique aqui para procurar novos usuários!
            </a>
        </div>
    @endif

</div>
@endsection
