@extends('layouts.app')

@section('title', 'Editar Classe')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">✏️ Editar Classe: {{ $classe->nome }}</h2>

    <form action="{{ route('classes.update', $classe->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Classe</label>
            <input type="text" class="form-control" name="nome" id="nome" value="{{ $classe->nome }}" required>
        </div>

        <div class="mb-3">
            <label for="sistemaRPG" class="form-label">Sistema RPG</label>
            <select class="form-select" name="sistemaRPG" id="sistemaRPG" required>
                <option value="">Selecione</option>
                @foreach(['D&D','Ordem Paranormal','Call of Cthulhu','Fate Core','Cypher System','Apocalypse World','Cyberpunk 2093 - Arkana-RPG'] as $sistema)
                    <option value="{{ $sistema }}" @if($classe->sistemaRPG == $sistema) selected @endif>{{ $sistema }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" id="descricao" rows="3">{{ $classe->descricao }}</textarea>
        </div>

        {{-- Campos de atributos iniciais podem ser exibidos aqui, conforme sistema --}}
        <button type="submit" class="btn btn-primary">Atualizar Classe</button>
    </form>
</div>
@endsection
