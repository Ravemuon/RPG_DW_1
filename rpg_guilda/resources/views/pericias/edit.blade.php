@extends('layouts.app')

@section('title','Editar Perícia')

@section('content')
<div class="container">
    <h2>Editar Perícia: {{ $pericia->nome }} de {{ $personagem->nome }}</h2>

    <form action="{{ route('pericias.update', [$personagem->id, $pericia->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="valor" class="form-label">Valor</label>
            <input type="number" name="valor" class="form-control" min="0" value="{{ $pericia->pivot->valor }}">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="definida" class="form-check-input" {{ $pericia->pivot->definida ? 'checked' : '' }}>
            <label class="form-check-label">Definida manualmente</label>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>
@endsection
