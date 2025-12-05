@extends('layouts.app')

@section('title', "Nova Origem - {$sistema->nome}")

@section('content')
<div class="container py-4">
    <a href="{{ route('sistemas.origens.index', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar às Origens
    </a>

    <h1 class="mb-4">Nova Origem em {{ $sistema->nome }}</h1>

    <form action="{{ route('sistemas.origens.store', $sistema->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Página</label>
            <input type="text" name="pagina" class="form-control" value="{{ old('pagina') }}">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Origem</button>
    </form>
</div>
@endsection
