@extends('layouts.app')

@section('title', 'Amigos')

@section('content')
<div class="container mt-4">

    @include('amizades.partials._alertas')

    {{-- CABEÇALHO / REDIRECIONAMENTOS --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-highlight">Gestão de Amizades</h4>
            <small class="text-muted">Gerencie suas conexões e convites</small>
        </div>

        <div class="card-body text-center">
            <p class="text-muted mb-4">
                Visualize seus amigos atuais, busque por usuários e mantenha suas conexões organizadas.
            </p>

            {{-- BOTÕES DE NAVEGAÇÃO (iguais ao index) --}}
            <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
                <a href="{{ route('amizades.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    🏠 Resumo
                </a>
                <a href="{{ route('amizades.pendentes') }}" class="btn btn-outline-warning btn-lg rounded-pill px-4">
                    ⚡ Solicitações Pendentes
                </a>
                <a href="{{ route('amizades.procurar') }}" class="btn btn-outline-info btn-lg rounded-pill px-4">
                    🔍 Procurar Usuários
                </a>
            </div>

            {{-- CAMPO DE PESQUISA --}}
            <form method="GET" action="{{ route('amizades.amigos') }}" class="d-flex justify-content-center mb-4">
                <div class="input-group w-50">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-start-pill"
                        placeholder="🔎 Buscar amigo por nome ou usuário...">
                    <button class="btn btn-outline-light rounded-end-pill px-4" type="submit">
                        Buscar
                    </button>
                </div>
            </form>

            {{-- LISTA DE AMIGOS --}}
            @if($amigos->count())
                <div class="row g-4">
                    @include('amizades.partials._lista_amigos', ['amigos' => $amigos])
                </div>

                <div class="mt-4">
                    {{ $amigos->links() }}
                </div>
            @else
                <p class="text-muted mb-0">Você ainda não tem amigos adicionados.</p>
            @endif
        </div>
    </div>

</div>
@endsection
