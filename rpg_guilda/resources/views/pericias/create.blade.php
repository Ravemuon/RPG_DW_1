@extends('layouts.app')

@section('title', 'Criar Perícia')

@section('content')
<div class="container py-4">

    <h3>Nova Perícia para o Sistema: {{ $sistema->nome }}</h3>

    <form action="{{ route('sistemas.pericias.store', $sistema->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Perícia</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Perícia</button>
    </form>

</div>
@endsection
