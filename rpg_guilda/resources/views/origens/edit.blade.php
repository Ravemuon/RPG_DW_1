@extends('layouts.app')

@section('title', "Editar Origem - {$sistema->nome}")

@section('content')
<div class="container py-4">
    <a href="{{ route('sistemas.origens.index', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar às Origens
    </a>

    <h1 class="mb-4">Editar Origem: {{ $origem->nome }}</h1>

    <form action="{{ route('sistemas.origens.update', [$sistema->id, $origem->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $origem->nome) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $origem->descricao) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Página</label>
            <input type="text" name="pagina" class="form-control" value="{{ old('pagina', $origem->pagina) }}">
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Origem</button>
    </form>
</div>
@endsection
