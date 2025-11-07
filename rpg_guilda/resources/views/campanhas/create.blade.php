@extends('layouts.app')

@section('title', 'Criar Nova Campanha')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">🛡️ Criar Nova Campanha</h1>

    <form action="{{ route('campanhas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Campanha</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required>
            @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="sistema_id" class="form-label">Sistema</label>
            <select name="sistema_id" id="sistema_id" class="form-select" required>
                <option value="">Selecione</option>
                @foreach($sistemas as $sistema)
                    <option value="{{ $sistema->id }}" {{ old('sistema_id') == $sistema->id ? 'selected' : '' }}>
                        {{ $sistema->nome }}
                    </option>
                @endforeach
            </select>
            @error('sistema_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" rows="4">{{ old('descricao') }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="privada" id="privada" class="form-check-input" {{ old('privada') ? 'checked' : '' }}>
            <label for="privada" class="form-check-label">Campanha Privada</label>
        </div>

        <div class="mb-3">
            <label for="codigo_convite" class="form-label">Código de Convite (opcional)</label>
            <input type="text" name="codigo_convite" id="codigo_convite" class="form-control" value="{{ old('codigo_convite') }}">
        </div>

        <button type="submit" class="btn btn-primary">Criar Campanha</button>
    </form>
</div>
@endsection
