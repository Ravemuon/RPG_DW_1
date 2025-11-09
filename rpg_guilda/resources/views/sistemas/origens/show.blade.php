@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $origem->nome }}</h1>
        <p><strong>Descrição:</strong> {{ $origem->descricao }}</p>
        <p><strong>Página:</strong> {{ $origem->pagina }}</p>

        <a href="{{ route('origens.edit', [$origem->sistema_id, $origem->id]) }}" class="btn btn-warning">Editar</a>
        <form action="{{ route('origens.destroy', [$origem->sistema_id, $origem->id]) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Excluir</button>
        </form>

        <a href="{{ route('origens.index', $origem->sistema_id) }}" class="btn btn-secondary">Voltar</a>
    </div>
@endsection
