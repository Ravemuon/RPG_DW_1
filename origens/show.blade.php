@extends('layouts.app')

@section('title', "Origem - {$origem->nome}")

@section('content')
<div class="container py-4">
    <a href="{{ route('sistemas.origens.index', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar às Origens
    </a>

    <h1 class="fw-bold mb-4">{{ $origem->nome }}</h1>

    @if($origem->descricao)
        <p>{{ $origem->descricao }}</p>
    @endif

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Página:</strong> {{ $origem->pagina ?? '-' }}</li>
        <li class="list-group-item">
            <strong>Perícias Iniciais:</strong>
            {{ json_encode($origem->pericias_iniciais ?? []) }}
        </li>
        <li class="list-group-item">
            <strong>Recursos Adicionais:</strong>
            {{ json_encode($origem->recursos_adicionais ?? []) }}
        </li>
    </ul>

    <a href="{{ route('sistemas.origens.edit', [$sistema->id, $origem->id]) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Editar
    </a>
</div>
@endsection
