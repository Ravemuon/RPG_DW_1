@extends('layouts.app')

@section('title', 'Solicitações Pendentes')

@section('content')
<div class="container mt-4">

    @include('amizades.partials._alertas')

    {{-- Título da Seção --}}
    <h3 class="mb-4 text-highlight">
        Solicitações Pendentes (Total: {{ count($pendentes) }})
    </h3>

    {{-- Bar de Busca (Opcional, mas útil se você a tiver) --}}
    <div class="mb-4">
        <form action="{{ route('amizades.pendentes') }}" method="GET">
            <div class="input-group">
                <input type="text" name="q" class="form-control bg-dark text-light border-secondary" placeholder="Buscar por nome ou username..." value="{{ $query ?? '' }}">
                <button class="btn btn-primary" type="submit">Buscar</button>
                @if(isset($query) && $query)
                    <a href="{{ route('amizades.pendentes') }}" class="btn btn-outline-secondary">Limpar</a>
                @endif
            </div>
        </form>
    </div>

    <hr class="mb-4">

    {{-- LISTA DE PENDENTES (ENVIADOS E RECEBIDOS) --}}
    @if(count($pendentes) > 0)
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            {{-- Loop sobre a coleção $pendentes (enviadas + recebidas) --}}
            @foreach($pendentes as $item)
                <div class="col">
                    {{-- Inclui o partial do cartão de pendente --}}
                    {{-- CORREÇÃO: Converte o ID da amizade para um objeto para acesso via $amizade->id no partial --}}
                    @include('amizades.partials._card_pendente', [
                        'amizade' => (object)['id' => $item['amizade_id']],
                        'usuario' => $item['usuario'],
                        'tipo' => $item['tipo']
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
