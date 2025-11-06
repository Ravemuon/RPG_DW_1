@extends('layouts.app')
@section('title', 'Novo Personagem')

@section('content')
<div class="card p-4">
    <h2 class="mb-3">⚔️ Novo Personagem</h2>
    <form action="{{ route('personagens.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nível</label>
            <input type="number" name="nivel" value="1" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Classe</label>
            <select name="classe_id" class="form-select" required>
                <option value="">Selecione...</option>
                @foreach($classes as $classe)
                    <option value="{{ $classe->id }}">{{ $classe->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Missão</label>
            <select name="missao_id" class="form-select">
                <option value="">Sem missão</option>
                @foreach($missoes as $missao)
                    <option value="{{ $missao->id }}">{{ $missao->titulo }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Imagem</label>
            <input type="file" name="imagem" class="form-control">
        </div>
        <button class="btn btn-primary">Salvar</button>
        <a href="{{ route('personagens.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
