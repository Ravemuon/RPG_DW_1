@extends('layouts.app')

@section('title', "Editar Raça - {$raca->nome}")

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-primary mb-4">
        <i class="bi bi-pencil me-2"></i> Editar Raça - {{ $raca->nome }}
    </h1>

    <form action="{{ route('sistemas.racas.update', [$sistema->id, $raca->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome da Raça</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $raca->nome) }}" required>
            @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $raca->descricao) }}</textarea>
            @error('descricao') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Modificadores de Atributos (JSON)</label>
            <textarea name="modificadores_atributos" class="form-control" rows="2">{{ old('modificadores_atributos', json_encode($raca->modificadores_atributos ?? [])) }}</textarea>
            <small class="text-muted">Ex: {"forca":2,"destreza":1}</small>
            @error('modificadores_atributos') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tipo de Bônus</label>
                <select name="tipo_bonus" class="form-select" required>
                    <option value="flat" {{ old('tipo_bonus', $raca->tipo_bonus)=='flat' ? 'selected' : '' }}>Flat</option>
                    <option value="multiplicador" {{ old('tipo_bonus', $raca->tipo_bonus)=='multiplicador' ? 'selected' : '' }}>Multiplicador</option>
                    <option value="escolha" {{ old('tipo_bonus', $raca->tipo_bonus)=='escolha' ? 'selected' : '' }}>Escolha</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Bônus Livre</label>
                <input type="number" name="bonus_livre" class="form-control" value="{{ old('bonus_livre', $raca->bonus_livre) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Página / Referência</label>
            <input type="text" name="pagina" class="form-control" value="{{ old('pagina', $raca->pagina) }}">
        </div>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-lg me-1"></i> Atualizar
        </button>
        <a href="{{ route('sistemas.racas.index', $sistema->id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
