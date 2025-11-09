@extends('layouts.app')

@section('title', 'Procurar Usuários')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-highlight fw-bold">Procurar Usuários</h4>
            <a href="{{ route('amizades.index') }}" class="btn btn-outline-light btn-sm fw-bold">Voltar</a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('amizades.procurar') }}" class="mb-4">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Buscar por nome ou ID..."
                           value="{{ $query ?? '' }}">
                    <button class="btn btn-custom fw-bold" type="submit">Buscar</button>
                </div>
            </form>

            <div class="row g-4">
                @forelse($usuarios as $usuario)
                    @include('amizades.partials._card_usuario', ['usuario' => $usuario])
                @empty
                    @if($query)
                        <p class="text-muted text-center">Nenhum usuário encontrado para "{{ $query }}".</p>
                    @else
                        <p class="text-muted text-center">Digite algo para começar a busca.</p>
                    @endif
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
