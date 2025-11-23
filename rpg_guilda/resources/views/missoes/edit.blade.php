@extends('layouts.app')

@section('title', "Editar Missão - {$missao->titulo}")

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-warning mb-4">✏️ Editar Missão: {{ $missao->titulo }}</h2>

    <form action="{{ route('missoes.update', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo', $missao->titulo) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="4">{{ old('descricao', $missao->descricao) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Recompensa</label>
            <input type="text" name="recompensa" class="form-control" value="{{ old('recompensa', $missao->recompensa) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Prioridade</label>
            <select name="prioridade" class="form-select">
                <option value="baixa" {{ old('prioridade', $missao->prioridade)=='baixa' ? 'selected' : '' }}>Baixa</option>
                <option value="media" {{ old('prioridade', $missao->prioridade)=='media' ? 'selected' : '' }}>Média</option>
                <option value="alta" {{ old('prioridade', $missao->prioridade)=='alta' ? 'selected' : '' }}>Alta</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="pendente" {{ $missao->status=='pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="em_andamento" {{ $missao->status=='em_andamento' ? 'selected' : '' }}>Em andamento</option>
                <option value="concluida" {{ $missao->status=='concluida' ? 'selected' : '' }}>Concluída</option>
                <option value="cancelada" {{ $missao->status=='cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success rounded-pill">Atualizar Missão</button>
        <a href="{{ route('missoes.show', ['campanha' => $campanha->id, 'missao' => $missao->id]) }}" class="btn btn-secondary rounded-pill">Cancelar</a>
    </form>
</div>
@endsection
