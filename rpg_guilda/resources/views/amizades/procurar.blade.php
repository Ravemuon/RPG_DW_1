@extends('layouts.app')

@section('title', 'Procurar Usuários')

@section('content')
<div class="container mt-4">

    {{-- ALERTAS --}}
    @include('amizades.partials._alertas')


    <div class="card shadow-lg bg-dark text-light border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center border-bottom-secondary">
            <h4 class="mb-0 text-highlight fw-bold">Procurar Usuários</h4>
            <a href="{{ route('amizades.index') }}" class="btn btn-outline-light btn-sm fw-bold">
                ⬅ Voltar
            </a>
        </div>


        {{-- BODY --}}
        <div class="card-body">

            {{-- BARRA DE BUSCA --}}
            <form method="GET" action="{{ route('amizades.procurar') }}" class="mb-4">

                <div class="input-group">

                    <input
                        type="text"
                        name="q"
                        class="form-control bg-secondary text-light border-secondary"
                        placeholder="Buscar por nome, username ou ID..."
                        value="{{ $query ?? '' }}"
                    >

                    <button class="btn btn-primary fw-bold" type="submit">
                        Buscar
                    </button>

                    @if(!empty($query))
                        <a href="{{ route('amizades.procurar') }}" class="btn btn-outline-secondary">
                            Limpar
                        </a>
                    @endif

                </div>
            </form>


            {{-- RESULTADOS --}}
            <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-md-4">

                @forelse($usuarios as $usuario)

                    <div class="col">
                        @include('amizades.partials._card_usuario', [
                            'usuario' => $usuario
                        ])
                    </div>

                @empty

                    <div class="col-12 text-center mt-3">

                        @if(!empty($query))
                            <p class="text-muted fst-italic">
                                Nenhum usuário encontrado para <strong>"{{ $query }}"</strong>.
                            </p>
                        @else
                            <p class="text-muted fst-italic">
                                Digite um nome, username ou ID para procurar novos aventureiros.
                            </p>
                        @endif

                    </div>

                @endforelse

            </div>

        </div>
    </div>

</div>
@endsection
