@extends('layouts.app')

@section('title', 'Criar Classe')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">✨ Criar Nova Classe</h2>

    <form action="{{ route('classes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Classe</label>
            <input type="text" class="form-control" name="nome" id="nome" required>
        </div>

        <div class="mb-3">
            <label for="sistemaRPG" class="form-label">Sistema RPG</label>
            <select class="form-select" name="sistemaRPG" id="sistemaRPG" required>
                <option value="">Selecione</option>
                <option value="D&D">D&D</option>
                <option value="Ordem Paranormal">Ordem Paranormal</option>
                <option value="Call of Cthulhu">Call of Cthulhu</option>
                <option value="Fate Core">Fate Core</option>
                <option value="Cypher System">Cypher System</option>
                <option value="Apocalypse World">Apocalypse World</option>
                <option value="Cyberpunk 2093 - Arkana-RPG">Cyberpunk 2093 - Arkana-RPG</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" id="descricao" rows="3"></textarea>
        </div>

        {{-- Aqui poderia vir campos de atributos iniciais, se quiser --}}
        <button type="submit" class="btn btn-success">Criar Classe</button>
    </form>
</div>
@endsection
