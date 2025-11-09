@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">✏️ Editar Classe – {{ $classe->nome }}</h1>
    <form action="{{ route('sistemas.classes.update', [$sistemaId, $classe->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control" value="{{ old('nome', $classe->nome) }}" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="3">{{ old('descricao', $classe->descricao) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="pagina" class="form-label">Página</label>
            <input type="text" id="pagina" name="pagina" class="form-control" value="{{ old('pagina', $classe->pagina) }}">
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('sistemas.classes.index', $sistemaId) }}" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection
