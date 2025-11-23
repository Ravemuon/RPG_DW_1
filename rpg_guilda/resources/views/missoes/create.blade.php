@extends('layouts.app')

@section('title', "Nova Missão - {$campanha->nome}")

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">➕ Criar Nova Missão</h2>

    <form action="{{ route('missoes.store', $campanha->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="4">{{ old('descricao') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Recompensa</label>
            <input type="text" name="recompensa" class="form-control" value="{{ old('recompensa') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Prioridade</label>
            <select name="prioridade" class="form-select">
                <option value="baixa" {{ old('prioridade')=='baixa' ? 'selected' : '' }}>Baixa</option>
                <option value="media" {{ old('prioridade')=='media' ? 'selected' : '' }}>Média</option>
                <option value="alta" {{ old('prioridade')=='alta' ? 'selected' : '' }}>Alta</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="pendente" selected>Pendente</option>
                <option value="em_andamento">Em andamento</option>
                <option value="concluida">Concluída</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success rounded-pill">Salvar Missão</button>
        <a href="{{ route('missoes.index', $campanha->id) }}" class="btn btn-secondary rounded-pill">Cancelar</a>
    </form>
</div>
@endsection
