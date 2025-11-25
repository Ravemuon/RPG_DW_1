@extends('layouts.app')

@section('title', "Editar Missão: {$missao->titulo}")

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-warning"><i class="bi bi-pencil-square"></i> Editar Missão: {{ $missao->titulo }}</h2>
        <a href="{{ route('missoes.show', [$campanha->id, $missao->id]) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Voltar para Missão
        </a>
    </div>
    <hr>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('missoes.update', [$campanha->id, $missao->id]) }}" method="POST" class="card p-4 shadow-sm rounded-4">
        @csrf
        @method('PUT') {{-- Método PUT para atualização --}}

        <div class="mb-3">
            <label for="titulo" class="form-label fw-bold">Título</label>
            {{-- Usa old() com fallback para o valor atual da missão --}}
            <input type="text" name="titulo" id="titulo" class="form-control"
                   value="{{ old('titulo', $missao->titulo) }}" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label fw-bold">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" rows="4">{{ old('descricao', $missao->descricao) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="recompensa" class="form-label fw-bold">Recompensa</label>
            <input type="text" name="recompensa" id="recompensa" class="form-control"
                   value="{{ old('recompensa', $missao->recompensa) }}">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="prioridade" class="form-label fw-bold">Prioridade</label>
                <select name="prioridade" id="prioridade" class="form-select" required>
                    {{-- Usa old() com fallback para o valor atual da missão --}}
                    <option value="baixa" {{ old('prioridade', $missao->prioridade) == 'baixa' ? 'selected' : '' }}>Baixa</option>
                    <option value="media" {{ old('prioridade', $missao->prioridade) == 'media' ? 'selected' : '' }}>Média</option>
                    <option value="alta" {{ old('prioridade', $missao->prioridade) == 'alta' ? 'selected' : '' }}>Alta</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label fw-bold">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="pendente" {{ old('status', $missao->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="em_andamento" {{ old('status', $missao->status) == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                    <option value="concluida" {{ old('status', $missao->status) == 'concluida' ? 'selected' : '' }}>Concluída</option>
                    <option value="cancelada" {{ old('status', $missao->status) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-warning rounded-pill mt-3 text-dark"><i class="bi bi-check-circle"></i> Atualizar Missão</button>
    </form>
</div>
@endsection
