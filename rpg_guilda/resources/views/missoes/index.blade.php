@extends('layouts.app')
@section('title', 'Missões')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <form action="{{ route('missoes.index') }}" method="GET" class="d-flex">
        <input type="text" name="busca" value="{{ $busca }}" class="form-control me-2" placeholder="Buscar missão...">
        <button class="btn btn-outline-primary">Buscar</button>
    </form>
    <a href="{{ route('missoes.create') }}" class="btn btn-primary">+ Nova Missão</a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Título</th>
            <th>Descrição</th>
            <th>Recompensa</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    @foreach($missoes as $m)
        <tr>
            <td>{{ $m->titulo }}</td>
            <td>{{ $m->descricao }}</td>
            <td>{{ $m->recompensa }}</td>
            <td>
                <a href="{{ route('missoes.edit', $m->id) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('missoes.destroy', $m->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
    