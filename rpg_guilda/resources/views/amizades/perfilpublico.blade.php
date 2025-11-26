@extends('layouts.app')

@section('title', 'Solicitações Pendentes')

@section('content')
<div class="container mt-4">

    {{-- ALERTAS --}}
    @include('amizades.partials._alertas')


    {{-- TÍTULO --}}
    <h3 class="mb-4 text-highlight">
        Solicitações Pendentes (Total: {{ $pendentes->count() ?? 0 }})
    </h3>


    {{-- BARRA DE BUSCA --}}
    <div class="mb-4">
        <form action="{{ route('amizades.pendentes') }}" method="GET">
            <div class="input-group">

                <input
                    type="text"
                    name="q"
                    class="form-control bg-dark text-light border-secondary"
                    placeholder="Buscar por nome ou username..."
                    value="{{ $query ?? '' }}"
                >

                <button class="btn btn-primary" type="submit">
                    Buscar
                </button>

                @if(!empty($query))
                    <a href="{{ route('amizades.pendentes') }}" class="btn btn-outline-secondary">
                        Limpar
                    </a>
                @endif

            </div>
        </form>
    </div>


    <hr class="mb-4">


    {{-- LISTA DE PENDENTES --}}
    @if($pendentes->count() > 0)

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">

            @foreach($pendentes as $item)

                <div class="col">

                    {{-- Converter amizade_id para objeto --}}
                    @php
                        $amizade = (object) ['id' => $item['amizade_id']];
                    @endphp

                    @include('amizades.partials._card_pendente', [
                        'amizade' => $amizade,
                        'usuario' => $item['usuario'],
                        'tipo'    => $item['tipo']
                    ])

                </div>

            @endforeach

        </div>

    @else

        <div class="alert alert-info text-center">
            Nenhuma solicitação de amizade pendente encontrada.
        </div>

    @endif

</div>
@endsection
