@extends('layouts.app')
@section('title', 'Nova Missão')

@section('content')
<div class="card p-4">
    <h2 class="mb-3">🗺️ Nova Missão</h2>
    <form action="{{ route('missoes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Recompensa</label>
            <input type="text" name="recompensa" class="form-control">
        </div>
        <button class="btn btn-primary">Salvar</button>
        <a href="{{ route('missoes.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
