@extends('layouts.app')

@section('content')

<div class="container py-4">
<h1 class="fw-bold mb-4">Classes do Sistema: {{ $sistema->nome }}</h1>

<!-- Redirecionamento 1: Botão para criar nova Classe -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('sistemas.index') }}" class="btn btn-secondary">⬅ Voltar para Sistemas</a>
    <a href="{{ route('sistemas.classes.create', $sistema) }}" class="btn btn-success">➕ Criar Nova Classe</a>
</div>

@if($sistema->classes->isNotEmpty())
    <div class="list-group">
        @foreach($sistema->classes as $classe)
            <!-- Redirecionamento 2: Tornar o item da lista clicável para ver os detalhes (show) -->
            <a
                href="{{ route('sistemas.classes.show', [$sistema, $classe]) }}"
                class="list-group-item list-group-item-action"
            >
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1 fw-bold">{{ $classe->nome }}</h5>
                </div>
                <p class="mb-1 text-truncate">{{ $classe->descricao ?? 'Sem descrição' }}</p>
                <small class="text-muted">Clique para ver detalhes e editar.</small>
            </a>
        @endforeach
    </div>
@else
    <div class="alert alert-info" role="alert">
         Nenhuma classe cadastrada para este sistema.
         <a href="{{ route('sistemas.classes.create', $sistema) }}" class="alert-link">Clique aqui para adicionar a primeira!</a>
    </div>
@endif


</div>
@endsection
