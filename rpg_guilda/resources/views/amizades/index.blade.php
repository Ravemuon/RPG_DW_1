@extends('layouts.app')

@section('title', 'Amigos')

@section('content')
<div class="container mt-4">

    @include('amizades.partials._alertas')

    {{-- REDIRECIONAMENTOS / RESUMO --}}
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

    {{-- SUGESTÕES DE AMIZADE --}}
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-highlight">Sugestões de Amizade</h4>
        </div>

        <div class="card-body">
            @if($sugestoes->count())
                <div class="row g-4 justify-content-center">
                    @foreach($sugestoes as $usuario)
                        <div class="col-12 col-md-6 d-flex justify-content-center">
                            <div style="width: 100%; max-width: 460px;">
                                @include('amizades.partials._card_usuario', ['usuario' => $usuario])
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0 text-center">Nenhuma sugestão no momento.</p>
            @endif
        </div>
    </div>
</div>

{{-- ESTILIZAÇÃO PERSONALIZADA --}}
<style>
    .amigo-card {
        min-height: 360px;
        border-radius: 18px;
        background: linear-gradient(160deg, #181818, #1f1f1f);
    }

    .amigo-card img {
        width: 110px !important;
        height: 110px !important;
    }

    .amigo-card .card-body {
        padding: 1.5rem !important;
    }
</style>
@endsection
