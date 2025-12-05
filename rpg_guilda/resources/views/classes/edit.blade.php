@extends('layouts.app')

@section('title', "Editar Origem - {$origem->nome}")

@section('content')
<div class="container py-4">

    <a href="{{ route('sistemas.origens.index', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar às Origens
    </a>

    <h1 class="fw-bold text-primary mb-4"><i class="bi bi-pencil me-2"></i> Editar Origem: {{ $origem->nome }}</h1>

    <form action="{{ route('sistemas.origens.update', [$sistema->id, $origem->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $origem->nome) }}" required>
            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control">{{ old('descricao', $origem->descricao) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Perícias Iniciais (JSON)</label>
            <textarea name="pericias_iniciais" class="form-control">{{ old('pericias_iniciais', $origem->pericias_iniciais) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Recursos Adicionais (JSON)</label>
            <textarea name="recursos_adicionais" class="form-control">{{ old('recursos_adicionais', $origem->recursos_adicionais) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Página</label>
            <input type="text" name="pagina" class="form-control" value="{{ old('pagina', $origem->pagina) }}">
        </div>

        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i> Atualizar Origem</button>
    </form>
</div>
@endsection
