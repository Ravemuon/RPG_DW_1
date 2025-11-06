@extends('layouts.app')

@section('title','Adicionar Perícia')

@section('content')
<div class="container">
    <h2>Adicionar Perícia a {{ $personagem->nome }}</h2>

    <form action="{{ route('pericias.store', $personagem->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Perícia</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label">Valor</label>
            <input type="number" name="valor" class="form-control" min="0">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="definida" class="form-check-input" checked>
            <label class="form-check-label">Definida manualmente</label>
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>
@endsection
