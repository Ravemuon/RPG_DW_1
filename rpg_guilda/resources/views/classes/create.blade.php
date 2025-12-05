@extends('layouts.app')

@section('title', "Nova Origem - {$sistema->nome}")

@section('content')
<div class="container py-4">

    {{-- Botão voltar --}}
    <a href="{{ route('sistemas.origens.index', $sistema->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Voltar às Origens
    </a>

    <h1 class="fw-bold text-primary mb-4"><i class="bi bi-plus-lg me-2"></i> Nova Origem em {{ $sistema->nome }}</h1>

    <form action="{{ route('sistemas.origens.store', $sistema->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" required>
            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control">{{ old('descricao') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Perícias Iniciais (JSON)</label>
            <textarea name="pericias_iniciais" class="form-control">{{ old('pericias_iniciais') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Recursos Adicionais (JSON)</label>
            <textarea name="recursos_adicionais" class="form-control">{{ old('recursos_adicionais') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Página</label>
            <input type="text" name="pagina" class="form-control" value="{{ old('pagina') }}">
        </div>

        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Criar Origem</button>
    </form>
</div>
@endsection
