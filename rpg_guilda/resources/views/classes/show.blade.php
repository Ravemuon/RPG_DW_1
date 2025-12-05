@extends('layouts.app')

@section('title', "Origem - {$origem->nome}")

@section('content')
<div class="container py-4">

    <a href="{{ route('sistemas.origens.index', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar às Origens
    </a>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h1 class="fw-bold text-primary mb-3">{{ $origem->nome }}</h1>

            @if($origem->descricao)
                <p>{{ $origem->descricao }}</p>
            @endif

            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Perícias Iniciais:</strong> <pre>{{ json_encode($origem->pericias_iniciais, JSON_PRETTY_PRINT) }}</pre></li>
                <li class="list-group-item"><strong>Recursos Adicionais:</strong> <pre>{{ json_encode($origem->recursos_adicionais, JSON_PRETTY_PRINT) }}</pre></li>
                <li class="list-group-item"><strong>Página:</strong> {{ $origem->pagina ?? 'Não informado' }}</li>
            </ul>

            @if(auth()->check() && auth()->user()->is_admin)
                <div class="d-flex gap-2">
                    <a href="{{ route('sistemas.origens.edit', [$sistema->id, $origem->id]) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>

                    <form action="{{ route('sistemas.origens.destroy', [$sistema->id, $origem->id]) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta origem?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Excluir
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
