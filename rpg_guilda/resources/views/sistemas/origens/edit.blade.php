@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar Origem: {{ $origem->nome }}</h1>

        <form action="{{ route('origens.update', [$origem->sistema_id, $origem->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $origem->nome) }}" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao', $origem->descricao) }}</textarea>
            </div>
            <div class="form-group">
                <label for="pagina">Página</label>
                <input type="text" class="form-control" id="pagina" name="pagina" value="{{ old('pagina', $origem->pagina) }}">
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Origem</button>
        </form>
    </div>
@endsection
